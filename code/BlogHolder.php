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
	static $icon = "blog/images/blogholder-file.png";

	static $description = "Displays listings of blog entries";
	
	static $singular_name = 'Blog Holder Page';
	
	static $plural_name = 'Blog Holder Pages';

	static $db = array(
		'TrackBacksEnabled' => 'Boolean',
		'AllowCustomAuthors' => 'Boolean',
		'ShowFullEntry' => 'Boolean', 
	);

	static $has_one = array(
		'Owner' => 'Member',
	);

	static $allowed_children = array(
		'BlogEntry'
	);

	function getCMSFields() {
		$blogOwners = $this->blogOwners(); 

		SiteTree::disableCMSFieldsExtensions();
		$fields = parent::getCMSFields();
		SiteTree::enableCMSFieldsExtensions();
		
		$fields->addFieldToTab(
			'Root.Main', 
			DropdownField::create('OwnerID', 'Blog owner', $blogOwners->map('ID', 'Name')->toArray())
				->setEmptyString('(None)')
				->setHasEmptyDefault(true),
			"Content"
		);
		$fields->addFieldToTab('Root.Main', new CheckboxField('TrackBacksEnabled', 'Enable TrackBacks'), "Content");
		$fields->addFieldToTab('Root.Main', new CheckboxField('AllowCustomAuthors', 'Allow non-admins to have a custom author field'), "Content");
		$fields->addFieldToTab(
			"Root.Main", 
			CheckboxField::create("ShowFullEntry", "Show Full Entry")
				->setDescription('Show full content in overviews rather than summary'), 
			"Content"
		);

		$this->extend('updateCMSFields', $fields);

		return $fields;
	}
	
	/**
	 * Get members who have BLOGMANAGEMENT and ADMIN permission
	 */ 

	function blogOwners($sort = array('FirstName'=>'ASC','Surname'=>'ASC'), $direction = null) {
		
		$members = Permission::get_members_by_permission(array('ADMIN','BLOGMANAGEMENT')); 
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
	function ShowTag() {
		if($this->request->latestParam('Action') == 'tag') {
			return Convert::raw2xml(Director::urlParam('ID'));
		}
	}

	/**
	 * Check if url has "/post"
	 */
	function isPost() {
		return $this->request->latestParam('Action') == 'post';
	}

	/**
	 * Link for creating a new blog entry
	 */
	function postURL(){
		return $this->Link('post');
	}

	/**
	 * Returns true if the current user is an admin, or is the owner of this blog
	 *
	 * @return Boolean
	 */
	function IsOwner() {
		return (Permission::check('BLOGMANAGEMENT') || Permission::check('ADMIN'));
	}

	/**
	 * Create default blog setup
	 */
	function requireDefaultRecords() {
		parent::requireDefaultRecords();

		$blogHolder = DataObject::get_one('BlogHolder');
		//TODO: This does not check for whether this blogholder is an orphan or not
		if(!$blogHolder) {
			$blogholder = new BlogHolder();
			$blogholder->Title = "Blog";
			$blogholder->URLSegment = "blog";
			$blogholder->Status = "Published";

			if(class_exists('WidgetArea')) {
				$widgetarea = new WidgetArea();
				$widgetarea->write();

				$blogholder->SideBarID = $widgetarea->ID;
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
			} else {
				$blogholder->write();
				$blogholder->publish("Stage", "Live");
			}	
			

			$blog = new BlogEntry();
			$blog->Title = _t('BlogHolder.SUCTITLE', "SilverStripe blog module successfully installed");
			$blog->URLSegment = 'sample-blog-entry';
			$blog->Tags = _t('BlogHolder.SUCTAGS',"silverstripe, blog");
			$blog->Content = _t('BlogHolder.SUCCONTENT',"<p>Congratulations, the SilverStripe blog module has been successfully installed. This blog entry can be safely deleted. You can configure aspects of your blog (such as the widgets displayed in the sidebar) in <a href=\"admin\">the CMS</a>.</p>");
			$blog->Status = "Published";
			$blog->ParentID = $blogholder->ID;
			$blog->write();
			$blog->publish("Stage", "Live");

			DB::alteration_message("Blog page created","created");
		}
	}
}

class BlogHolder_Controller extends BlogTree_Controller {
	static $allowed_actions = array(
		'index',
		'tag',
		'date',
		'metaweblog',
		'postblog' => 'BLOGMANAGEMENT',
		'post',
		'BlogEntryForm' => 'BLOGMANAGEMENT',
	);
	
	function init() {
		parent::init();
		Requirements::themedCSS("bbcodehelp");
	}

	/**
	 * Return list of usable tags for help
	 */
	function BBTags() {
		return BBCodeParser::usable_tags();
	}

	function providePermissions() {
		return array("BLOGMANAGEMENT" => "Blog management");
	}

	/**
	 * Post a new blog entry
	 */
	function post(){
		if(!Permission::check('BLOGMANAGEMENT')) return Security::permissionFailure();
		$page = $this->customise(array(
			'Content' => false,
			'Form' => $this->BlogEntryForm()
		));

		return $page->renderWith('Page');
	}

	/**
	 * A simple form for creating blog entries
	 */
	function BlogEntryForm() {	
		if(!Permission::check('BLOGMANAGEMENT')) return Security::permissionFailure();
		

		$id = 0;
		if($this->request->latestParam('ID')) {
			$id = (int) $this->request->latestParam('ID');
		}

		$codeparser = new BBCodeParser();
		$membername = Member::currentUser() ? Member::currentUser()->getName() : "";

		if(BlogEntry::$allow_wysiwyg_editing) {
			$contentfield = new HtmlEditorField("BlogPost", _t("BlogEntry.CN"));
		} else {
			$contentfield = new CompositeField(
				new LiteralField("BBCodeHelper","<a id=\"BBCodeHint\" target='new'>"._t("BlogEntry.BBH")."</a><div class='clear'><!-- --></div>" ),
				new TextareaField("BlogPost", _t("BlogEntry.CN"),20), // This is called BlogPost as the id #Content is generally used already
				new LiteralField("BBCodeTags","<div id=\"BBTagsHolder\">".$codeparser->useable_tagsHTML()."</div>")
			);
		}
		if(class_exists('TagField')) {
			$tagfield = new TagField('Tags', null, null, 'BlogEntry');
			$tagfield->setSeparator(', ');
		} else {
			$tagfield = new TextField('Tags');
		}
		
		$field = 'TextField';
		if(!$this->AllowCustomAuthors && !Permission::check('ADMIN')) {
			$field = 'ReadonlyField';
		}
		$fields = new FieldList(
			new HiddenField("ID", "ID"),
			new TextField("Title", _t('BlogHolder.SJ', "Subject")),
			new $field("Author", _t('BlogEntry.AU'), $membername),
			$contentfield,
			$tagfield,
			new LiteralField("Tagsnote"," <label id='tagsnote'>"._t('BlogHolder.TE', "For example: sport, personal, science fiction")."<br/>" .
												_t('BlogHolder.SPUC', "Please separate tags using commas.")."</label>")
		);
		
		$submitAction = new FormAction('postblog', _t('BlogHolder.POST', 'Post blog entry'));

		$actions = new FieldList($submitAction);
		$validator = new RequiredFields('Title','BlogPost');

		$form = new Form($this, 'BlogEntryForm',$fields, $actions,$validator);

		if($id != 0) {
			$entry = DataObject::get_by_id('BlogEntry', $id);
			if($entry->IsOwner()) {
				$form->loadDataFrom($entry);
				$form->Fields()->fieldByName('BlogPost')->setValue($entry->Content);

			}
		} else {
			$form->loadDataFrom(array("Author" => Cookie::get("BlogHolder_Name")));
		}

		return $form;
	}

	function postblog($data, $form) {
		if(!Permission::check('BLOGMANAGEMENT')) return Security::permissionFailure();

		Cookie::set("BlogHolder_Name", $data['Author']);
		$blogentry = false;

		if(isset($data['ID']) && $data['ID']) {
			$blogentry = DataObject::get_by_id("BlogEntry", $data['ID']);
			if(!$blogentry->IsOwner()) {
				unset($blogentry);
			}
		}

		if(!$blogentry) {
			$blogentry = new BlogEntry();
		}

		$form->saveInto($blogentry);
		$blogentry->ParentID = $this->ID;

		$blogentry->Content = str_replace("\r\n", "\n", $form->Fields()->fieldByName('BlogPost')->dataValue());

		if(Object::has_extension($this->ClassName, 'Translatable')) {
			$blogentry->Locale = $this->Locale; 
		}

		$blogentry->Status = "Published";
		$blogentry->writeToStage("Live");
		$blogentry->publish("Stage", "Live");

		$this->redirect($this->Link());
	}
}


?>
