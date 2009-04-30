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
class BlogHolder extends BlogTree {
	
	static $icon = "blog/images/blogholder";
	
	static $db = array(
		'Name' => 'Varchar',
		'TrackBacksEnabled' => 'Boolean',
		'AllowCustomAuthors' => 'Boolean',
	);
	
	static $has_one = array(
		'Owner' => 'Member',
	);
	
	static $allowed_children = array(
		'BlogEntry'
	);
	
	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.Main", new TextField("Name", "Name of blog"));

		$fields->addFieldToTab('Root.Content.Main', new CheckboxField('TrackBacksEnabled', 'Enable TrackBacks'));
		$fields->addFieldToTab('Root.Content.Main', new DropdownField('OwnerID', 'Blog owner', DataObject::get('Member')->toDropDownMap('ID', 'Name', 'None')));
		$fields->addFieldToTab('Root.Content.Main', new CheckboxField('AllowCustomAuthors', 'Allow non-admins to have a custom author field'));
	
		return $fields;
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
		if(Director::urlParam('Action') == 'tag') {
			return Convert::raw2xml(Director::urlParam('ID'));
		}
	}
	
	/**
	 * Check if url has "/post"
	 */
	function isPost() {
		return Director::urlParam('Action') == 'post';
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
		return Permission::check('ADMIN') || (Member::CurrentMember() && Member::CurrentMember()->ID == $this->OwnerID);
	}
	
	/**
	 * Create default blog setup
	 */
	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		
		if(!DataObject::get_one('BlogHolder')) {
			$blogholder = new BlogHolder();
			$blogholder->Title = "Blog";
			$blogholder->URLSegment = "blog";
			$blogholder->Status = "Published";
			
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
			
			$blog = new BlogEntry();
			$blog->Title = _t('BlogHolder.SUCTITLE', "SilverStripe blog module successfully installed");
			$blog->URLSegment = 'sample-blog-entry';
			$blog->Tags = _t('BlogHolder.SUCTAGS',"silverstripe, blog");
			$blog->Content = _t('BlogHolder.SUCCONTENT',"Congratulations, the SilverStripe blog module has been successfully installed. This blog entry can be safely deleted. You can configure aspects of your blog (such as the widgets displayed in the sidebar) in [url=admin]the CMS[/url].");
			$blog->Status = "Published";
			$blog->ParentID = $blogholder->ID;
			$blog->write();
			$blog->publish("Stage", "Live");
			
			Database::alteration_message("Blog page created","created");
		}
	}
}

class BlogHolder_Controller extends BlogTree_Controller {
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
	
	/**
	 * Post a new blog entry
	 */
	function post(){
		if(!$this->IsOwner()){
			Security::permissionFailure($this, _t('BlogHolder.HAVENTPERM', 'Posting blogs is an administrator task. Please log in.'));
		}
		
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
		Requirements::javascript('jsparty/behaviour.js');
		Requirements::javascript('jsparty/prototype.js');
		Requirements::javascript('jsparty/scriptaculous/effects.js');
		Requirements::javascript('cms/javascript/PageCommentInterface.js');
		Requirements::javascript('blog/javascript/bbcodehelp.js');
					
		$id = 0;
		if(Director::urlParam('ID')) {
			$id = (int) Director::urlParam('ID');
		}
		
		$codeparser = new BBCodeParser();
		$membername = Member::currentMember() ? Member::currentMember()->getName() : "";
		
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
		$fields = new FieldSet(
			new HiddenField("ID", "ID"),
			new TextField("Title",_t('BlogHolder.SJ', "Subject")),
			new $field("Author",_t('BlogEntry.AU'),$membername),
			$contentfield,
			$tagfield,
			new LiteralField("Tagsnote"," <label id='tagsnote'>"._t('BlogHolder.TE', "For example: sport, personal, science fiction")."<br/>" .
												_t('BlogHolder.SPUC', "Please separate tags using commas.")."</label>")
		);	
		
		$submitAction = new FormAction('postblog', _t('BlogHolder.POST', 'Post blog entry'));
		$actions = new FieldSet($submitAction);
		$validator = new RequiredFields('Title','Content');
			
		$form = new Form($this, 'BlogEntryForm',$fields, $actions,$validator);
	
		if($id != 0) {
			$entry = DataObject::get_by_id('BlogEntry', $id);
			if($entry->IsOwner()) {
				$form->loadNonBlankDataFrom($entry);
				$form->datafieldByName('BlogPost')->setValue($entry->Content);
			}
		} else {
			$form->loadNonBlankDataFrom(array("Author" => Cookie::get("BlogHolder_Name")));
		}
		
		return $form;
	}
	
	function postblog($data, $form) {
		Cookie::set("BlogHolder_Name", $data['Author']);
		$blogentry = false;
		
		if($data['ID']) {
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
		$blogentry->Content = $form->datafieldByName('BlogPost')->dataValue();
		
		$blogentry->Status = "Published";
		$blogentry->writeToStage("Stage");
		$blogentry->publish("Stage", "Live");
		
		Director::redirect($this->Link());
	}
}


?>
