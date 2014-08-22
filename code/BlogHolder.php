<?php

/**
 * @package blog
 */

/**
 * Blog holder to display summarised blog entries.
 *
 * A blog holder is the leaf end of a BlogTree, but can also be used standalone in simpler circumstances.
 * BlogHolders can only hold BlogEntries, BlogTrees can only hold BlogTrees and BlogHolders
 * BlogHolders have a form on them for easy posting, and an owner that can post to them, BlogTrees don't
 */
class BlogHolder extends BlogTree implements PermissionProvider {

	private static $icon = "blog/images/blogholder-file.png";
	
	private static $description = "Displays listings of blog entries";
	
	private static $singular_name = 'Blog Holder Page';

	private static $plural_name = 'Blog Holder Pages';

	private static $db = array(
		'AllowCustomAuthors' => 'Boolean',
		'ShowFullEntry' => 'Boolean', 
	);

	private static $has_one = array(
		'Owner' => 'Member',
	);

	private static $allowed_children = array(
		'BlogEntry'
	);

	public function getCMSFields() {
		$blogOwners = $this->blogOwners(); 

		// Add holder fields prior to extensions being called
		$this->beforeUpdateCMSFields(function($fields) use ($blogOwners) {
			$fields->addFieldsToTab(
				'Root.Main',
				array(
					DropdownField::create('OwnerID', 'Blog owner', $blogOwners->map('ID', 'Name')->toArray())
						->setEmptyString('(None)')
						->setHasEmptyDefault(true),
					CheckboxField::create('AllowCustomAuthors', 'Allow non-admins to have a custom author field'),
					CheckboxField::create("ShowFullEntry", "Show Full Entry")
						->setDescription('Show full content in overviews rather than summary')
				),
				"Content"
			);
		});
		
		return parent::getCMSFields();
	}
	
	/**
	 * Get members who have BLOGMANAGEMENT and ADMIN permission
	 *
	 * @param array $sort
	 * @param string $direction
	 * @return SS_List
	 */
	public function blogOwners($sort = array('FirstName'=>'ASC','Surname'=>'ASC'), $direction = null) {
		$members = Permission::get_members_by_permission(array('ADMIN', 'BLOGMANAGEMENT'));
		$members->sort($sort);
		
		$this->extend('extendBlogOwners', $members);
		
		return $members;
	}

	public function BlogHolderIDs() {
		return array( $this->ID );
	}

	/*
	 * @todo: These next few functions don't really belong in the model. Can we remove them?
	 */

	/**
	 * Only display the blog entries that have the specified tag
	 */
	public function ShowTag() {
		if($this->request->latestParam('Action') == 'tag') {
			return Convert::raw2xml(Director::urlParam('ID'));
		}
	}

	/**
	 * Check if url has "/post"
	 */
	public function isPost() {
		return $this->request->latestParam('Action') == 'post';
	}

	/**
	 * Link for creating a new blog entry
	 */
	public function postURL() {
		return $this->Link('post');
	}

	/**
	 * Returns true if the current user is an admin, or is the owner of this blog
	 *
	 * @return bool
	 */
	public function IsOwner() {
		return (Permission::check('BLOGMANAGEMENT') || Permission::check('ADMIN'));
	}

	/**
	 * Create default blog setup
	 */
	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		
		// Skip creation of default records
		if(!self::config()->create_default_pages) return;
		
		$blogHolder = DataObject::get_one('BlogHolder');
		//TODO: This does not check for whether this blogholder is an orphan or not
		if(!$blogHolder) {
			$blogholder = new BlogHolder();
			$blogholder->Title = "Blog";
			$blogholder->URLSegment = "blog";
			$blogholder->Status = "Published";
			$blogholder->write();
			$blogholder->publish("Stage", "Live");

			// Add default widgets to first found WidgetArea relationship
			if(class_exists('WidgetArea')) {
				foreach($this->has_one() as $name => $class) {
					if($class == 'WidgetArea' || is_subclass_of($class, 'WidgetArea')) {
						$relationName = "{$name}ID";
						$widgetarea = new WidgetArea();
						$widgetarea->write();

						$blogholder->$relationName = $widgetarea->ID;
						$blogholder->write();
						$blogholder->publish("Stage", "Live");

						$managementwidget = new BlogManagementWidget();
						$managementwidget->ParentID = $widgetarea->ID;
						$managementwidget->write();

						$tagcloudwidget = new TagCloudWidget();
						$tagcloudwidget->ParentID = $widgetarea->ID;
						$tagcloudwidget->write();

						$archivewidget = new ArchiveWidget();
						$archivewidget->ParentID = $widgetarea->ID;
						$archivewidget->write();

						$widgetarea->write();	

						break; // only apply to one
					}
				}
			}

			$blog = new BlogEntry();
			$blog->Title = _t('BlogHolder.SUCTITLE', "SilverStripe blog module successfully installed");
			$blog->URLSegment = 'sample-blog-entry';
			$blog->Tags = _t('BlogHolder.SUCTAGS',"silverstripe, blog");
			$blog->Content = _t('BlogHolder.SUCCONTENT',"<p>Congratulations, the SilverStripe blog module has been successfully installed. This blog entry can be safely deleted. You can configure aspects of your blog in <a href=\"admin\">the CMS</a>.</p>");
			$blog->Status = "Published";
			$blog->ParentID = $blogholder->ID;
			$blog->write();
			$blog->publish("Stage", "Live");

			DB::alteration_message("Blog page created","created");
		}
	}

	public function providePermissions() {
		return array("BLOGMANAGEMENT" => "Blog management");
	}
}

class BlogHolder_Controller extends BlogTree_Controller {

	private static $allowed_actions = array(
		'index',
		'tag',
		'date',
		'metaweblog',
		'postblog' => 'BLOGMANAGEMENT',
		'post',
		'BlogEntryForm' => 'BLOGMANAGEMENT',
	);
	
	public function init() {
		parent::init();
		Requirements::themedCSS("bbcodehelp");
	}

	/**
	 * Return list of usable tags for help
	 */
	public function BBTags() {
		return BBCodeParser::usable_tags();
	}

	/**
	 * Post a new blog entry
	 */
	public function post(){
		if(!Permission::check('BLOGMANAGEMENT')) return Security::permissionFailure();
		$page = $this->customise(array(
			'Content' => false,
			'Form' => $this->BlogEntryForm()
		));

		return $page->renderWith('Page');
	}

	/**
	 * A simple form for creating blog entries
	 *
	 * @return Form
	 */
	public function BlogEntryForm() {
		if(!Permission::check('BLOGMANAGEMENT')) return Security::permissionFailure();
		

		$codeparser = BBCodeParser::create();
		$membername = Member::currentUser() ? Member::currentUser()->getName() : "";

		if(BlogEntry::$allow_wysiwyg_editing) {
			$contentfield = HtmlEditorField::create("BlogPost", _t("BlogEntry.CN"));
		} else {
			$contentfield = CompositeField::create(
				LiteralField::create(
					"BBCodeHelper",
					"<a id=\"BBCodeHint\" target='new'>"._t("BlogEntry.BBH")."</a><div class='clear'><!-- --></div>"
				),
				TextareaField::create(
					"BlogPost",
					_t("BlogEntry.CN"),
					20
				), // This is called BlogPost as the id #Content is generally used already
				LiteralField::create(
					"BBCodeTags",
					"<div id=\"BBTagsHolder\">".$codeparser->useable_tagsHTML()."</div>"
				)
			);
		}

		// Support for https://github.com/chillu/silverstripe-tagfield
		if(class_exists('TagField')) {
			$tagfield =  TagField::create('Tags', null, null, 'BlogEntry');
			$tagfield->setSeparator(', ');
		} else {
			$tagfield = TextField::create('Tags');
		}
		
		$field = 'TextField';
		if(!$this->AllowCustomAuthors && !Permission::check('ADMIN')) {
			$field = 'ReadonlyField';
		}
		$fields = FieldList::create(
			HiddenField::create("ID", "ID"),
			TextField::create("Title", _t('BlogHolder.SJ', "Subject")),
			$field::create("Author", _t('BlogEntry.AU'), $membername),
			$contentfield,
			$tagfield,
			LiteralField::create(
				"Tagsnote",
				" <label id='tagsnote'>"._t('BlogHolder.TE', "For example: sport, personal, science fiction")."<br/>" .
				_t('BlogHolder.SPUC', "Please separate tags using commas.")."</label>"
			)
		);
		
		$submitAction = FormAction::create('postblog', _t('BlogHolder.POST', 'Post blog entry'));

		$actions = FieldList::create($submitAction);
		$validator = RequiredFields::create('Title','BlogPost');

		$form = Form::create($this, 'BlogEntryForm', $fields, $actions, $validator);

		$id = (int) $this->request->latestParam('ID');
		if($id) {
			$entry = BlogEntry::get()->byID($id);
			if($entry->IsOwner()) {
				$form->loadDataFrom($entry);
				$form->Fields()->fieldByName('BlogPost')->setValue($entry->Content);
			}
		} else {
			$form->loadDataFrom(array("Author" => Cookie::get("BlogHolder_Name")));
		}

		return $form;
	}

	public function postblog($data, $form) {
		if(!Permission::check('BLOGMANAGEMENT')) return Security::permissionFailure();

		Cookie::set("BlogHolder_Name", $data['Author']);
		$blogentry = false;

		if(!empty($data['ID'])) {
			$candidate = BlogEntry::get()->byID($data['ID']);
			if($candidate->IsOwner()) $blogentry = $candidate;
		}

		if(!$blogentry) $blogentry = BlogEntry::create();

		$form->saveInto($blogentry);
		$blogentry->ParentID = $this->ID;

		$blogentry->Content = str_replace("\r\n", "\n", $form->Fields()->fieldByName('BlogPost')->dataValue());

		if(Object::has_extension($this->ClassName, 'Translatable')) {
			$blogentry->Locale = $this->Locale; 
		}

		$oldMode = Versioned::get_reading_mode();
		Versioned::reading_stage('Stage');
		$blogentry->write();
		$blogentry->publish("Stage", "Live");
		Versioned::set_reading_mode($oldMode);

		$this->redirect($this->Link());
	}
}
