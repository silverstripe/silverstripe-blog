<?php

/**
 * @package blog
 */
 
/**
 * Blog holder to display summarised blog entries
 */
 
class BlogHolder extends Page {
	
	static $icon = "blog/images/blogholder";
	
	static $db = array(
	);
	
	static $has_one = array(
		"SideBar" => "WidgetArea"
	);
	
	static $allowed_children = array(
		'BlogEntry'
	);
	
	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeFieldFromTab("Root.Content.Main","Content");
		$fields->addFieldToTab("Root.Content.Widgets", new WidgetAreaEditor("SideBar"));
		
		return $fields;
	}
	
	/**
	 * The DataObject of blog entries
	 */
	public function BlogEntries($limit = 10) {
		$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
		$tagCheck = '';
		$dateCheck = "";
		
		if(isset($_GET['tag'])) {
			$tag = addslashes($_GET['tag']);
			$tag = str_replace(array("\\",'_','%',"'"), array("\\\\","\\_","\\%","\\'"), $tag);
			$tagCheck = "AND `BlogEntry`.Tags LIKE '%$tag%'";
		}
		
		
		if(Director::urlParams()){
			if(Director::urlParam('Action') == 'tag') {
				$tag = addslashes(Director::urlParam('ID'));
				$tag = str_replace(array("\\",'_','%',"'"), array("\\\\","\\_","\\%","\\'"), $tag);
				$tagCheck = "AND `BlogEntry`.Tags LIKE '%$tag%'";
			} else {
				$year = Director::urlParam('Action');
				$month = Director::urlParam('ID');
			
				if(is_numeric($month) && is_numeric($month)){
					$dateCheck = "AND `BlogEntry`.Date BETWEEN '$year-$month-1' AND '$year-$month-31'";
				}
				else if(isset($year)){
						$dateCheck = "AND `BlogEntry`.Date BETWEEN '$year-1-1' AND '$year-12-31'";			
				}
			}
		}
		
		return DataObject::get("Page","`ParentID` = $this->ID AND ShowInMenus = 1 $tagCheck $dateCheck","`BlogEntry`.Date DESC",'',"$start, $limit");
	}

	/**
	 * Only display the blog entries that have the specified tag
	 */
	function Tag() {
		return isset($_GET['tag']) ? $_GET['tag'] : false;
	}
	
	/**
	 * A simple form for creating blog entries
	 */
	function BlogEntryForm(){
		Requirements::javascript('jsparty/behaviour.js');
		Requirements::javascript('jsparty/prototype.js');
		Requirements::javascript('jsparty/scriptaculous/effects.js');
		Requirements::javascript('cms/javascript/PageCommentInterface.js');
		Requirements::javascript('blog/javascript/bbcodehelp.js');
				
		$id = 0;
		if(Director::urlParam('ID')){
			$id = Director::urlParam('ID');
		}
				
		$codeparser = new BBCodeParser();
		$membername = Member::currentMember() ? Member::currentMember()->getName() : "";
		
		$fields = new FieldSet(
			new HiddenField("ParentID", "ParentID", $this->ID),
			new HiddenField("ID","ID"),
			new HiddenField("Date","Date"),
			new TextField("Title","Subject"),
			new TextField("Author","Author",$membername),
			new CompositeField( 
				new LiteralField("BBCodeHelper","<a  id=\"BBCodeHint\" target='new'>BBCode help</a><div class='clear'><!-- --></div>" ),
				new TextareaField("Content", "Content",20),
				new LiteralField("BBCodeTags","<div id='BBTagsHolder' style='display:none;'>".$codeparser->useable_tagsHTML()."</div>")	
			),
			new TextField("Tags","Tags"),
			new LiteralField("Tagsnote"," <label id='tagsnote'>For example: sport, personal, science fiction<br/>" .
												"Please separate tags using commas.</label>")
		);	
		
		$submitAction = new FormAction('postblog', 'Post blog entry');
		$actions = new FieldSet($submitAction);
		$validator = new RequiredFields('Title','Content');
			
		$form = new BlogEntry_Form($this, 'BlogEntryForm',$fields, $actions,$validator);
	
		if($id != 0){
			$form->loadNonBlankDataFrom(DataObject::get_by_id('BlogEntry',$id));
		}else{
				$form->loadNonBlankDataFrom(array("Author" => Cookie::get("BlogHolder_Name")));
		}
		
		return $form;
	}

	/**
	 * Check if url has "/post"
	 */
	function isPost(){
		return Director::urlParam('Action') == 'post';
	}
	
	/**
	 * Link for creating a new blog entry
	 */
	function postURL(){
		return  $this->Link('post');
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
			$blog->Title = "SilverStripe blog module successfully installed";
			$blog->URLSegment = 'sample-blog-entry';
			$blog->setDate(date("Y-m-d H:i:s",time()));
			$blog->Tags = "silverstripe, blog";
			$blog->Content = "Congratulations, the SilverStripe blog module has been successfully installed. This blog entry can be safely deleted. You can configure aspects of your blog (such as the widgets displayed in the sidebar) in [url=admin]the CMS[/url].";
			$blog->Status = "Published";
			$blog->ParentID = $blogholder->ID;
			$blog->write();
			$blog->publish("Stage", "Live");
			
			Database::alteration_message("Blog page created","created");
		}
	}
}

class BlogHolder_Controller extends Page_Controller {
	function init() {
		parent::init();	
		// This will create a <link> tag point to the RSS feed
		RSSFeed::linkToFeed($this->Link() . "rss", "RSS feed of this blog");
		Requirements::themedCSS("blog");
		Requirements::themedCSS("bbcodehelp");

	}

	/**
	 * Get the archived blogs for a particular month or year, in the format /year/month/ eg: /2008/10/
	 */
	function showarchive() {
		$month = addslashes($this->urlParams['ID']);
		return array(
			"Children" => DataObject::get('SiteTree', "ParentID = $this->ID AND DATE_FORMAT(`BlogEntry`.`Date`, '%Y-%M') = '$month'"),
		);		
	}

	function ArchiveMonths() {
		$months = DB::query("SELECT DISTINCT DATE_FORMAT(`BlogEntry`.`Date`, '%M') AS `Month`, DATE_FORMAT(`BlogEntry`.`Date`, '%Y') AS `Year` FROM `BlogEntry` ORDER BY `BlogEntry`.`Date` DESC");
		$output = new DataObjectSet();
		foreach($months as $month) {
			$month['Link'] = $this->Link() . "showarchive/$month[Year]-$month[Month]";
			$output->push(new ArrayData($month));
		}
		Debug::show($output);
		return $output;
	}
	
	/**
	 * Get the rss fee for this blog holder's entries
	 */
	function rss() {
		global $project;
		$rss = new RSSFeed($this->Children(), $this->Link(), $project . " blog", "", "Title", "ParsedContent");
		$rss->outputToBrowser();
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
		if(!Permission::check('ADMIN')){
			Security::permissionFailure($this,
				"Posting blogs is an administrator task. Please log in.");
		}
		return array();
	}
	
	function defaultAction($action) {
		// Protection against infinite loops when an RSS widget pointing to this page is added to this page
		if(stristr($_SERVER['HTTP_USER_AGENT'], 'SimplePie')) {
			return $this->rss();
		}
		
		return parent::defaultAction($action);
	}
	
}

/**
 * Blog entry form
 */
class BlogEntry_Form extends Form {
	function postblog($data) {
		Cookie::set("BlogHolder_Name", $data['Author']);
		$blogentry = new BlogEntry();
		$this->saveInto($blogentry);
				
		if($data['ID'] != 0){ //new post
			$blogentry = DataObject::get_by_id("BlogEntry",$data['ID']);
			$this->saveInto($blogentry);
			$blogentry->setDate($data['Date']);
		}else{
			$blogentry->setDate(date("Y-m-d H:i:s",time()));
		}
		
		$blogentry->Status = "Published";
		$blogentry->writeToStage("Stage");
		$blogentry->publish("Stage", "Live");

		Director::redirect(Director::currentURLSegment());
		
	}
}

?>
