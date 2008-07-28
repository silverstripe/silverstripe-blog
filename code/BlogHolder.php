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
		"LandingPageFreshness" => "Varchar",
	);
	
	static $has_one = array(
		"SideBar" => "WidgetArea"
	);
	
	static $allowed_children = array(
		'BlogEntry'
	);
	
	static $defaults = array(
		"LandingPageFreshness" => "3 MONTH",
	);
	
	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeFieldFromTab("Root.Content.Main","Content");
		$fields->addFieldToTab("Root.Content.Widgets", new WidgetAreaEditor("SideBar"));

		$fields->addFieldToTab('Root.Content.Main', new DropdownField('LandingPageFreshness', 'When you first open the blog, how many entries should I show', array(
			"" => "All entries",
			"1 MONTH" => "Last month's entries",
			"2 MONTH" => "Last 2 months' entries",
			"3 MONTH" => "Last 3 months' entries",
			"4 MONTH" => "Last 4 months' entries",
			"5 MONTH" => "Last 5 months' entries",
			"6 MONTH" => "Last 6 months' entries",
			"7 MONTH" => "Last 7 months' entries",
			"8 MONTH" => "Last 8 months' entries",
			"9 MONTH" => "Last 9 months' entries",
			"10 MONTH" => "Last 10 months' entries",
			"11 MONTH" => "Last 11 months' entries",
			"12 MONTH" => "Last year's entries",
		)));
	
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
					$nextyear =  ($month==12) ? $year + 1 : $year;
					$nextmonth = $month % 12 + 1;	
					$dateCheck = "AND `BlogEntry`.Date BETWEEN '$year-$month-1' AND '$nextyear-$nextmonth-1'";
				} else if(isset($year)){
					$nextyear = $year + 1;
					$dateCheck = "AND `BlogEntry`.Date BETWEEN '$year-1-1' AND '".$nextyear."-1-1'";			
				} else if($this->LandingPageFreshness) {					
					$dateCheck = "AND `BlogEntry`.Date > NOW() - INTERVAL " . $this->LandingPageFreshness;
				}
			}
		}
		
		return DataObject::get("Page","`ParentID` = $this->ID AND ShowInMenus = 1 $tagCheck $dateCheck","`BlogEntry`.Date DESC",'',"$start, $limit");
	}

	/**
	 * Only display the blog entries that have the specified tag
	 */
	function ShowTag() {
		if(Director::urlParam('Action') == 'tag') {
			return Director::urlParam('ID');
		}
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
			new TextField("Title",_t('BlogHolder.SJ', "Subject")),
			new TextField("Author",_t('BlogEntry.AU'),$membername),
			new CompositeField( 
				new LiteralField("BBCodeHelper","<a  id=\"BBCodeHint\" target='new'>"._t("BlogEntry.BBH")."</a><div class='clear'><!-- --></div>" ),
				new TextareaField("Content", _t("BlogEntry.CN"),20),
				new LiteralField("BBCodeTags","<div id='BBTagsHolder' style='display:none;'>".$codeparser->useable_tagsHTML()."</div>")	
			),
			new TextField("Tags","Tags"),
			new LiteralField("Tagsnote"," <label id='tagsnote'>"._t('BlogHolder.TE', "For example: sport, personal, science fiction")."<br/>" .
												_t('BlogHolder.SPUC', "Please separate tags using commas.")."</label>")
		);	
		
		$submitAction = new FormAction('postblog', _t('BlogHolder.POST', 'Post blog entry'));
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
			$blog->Title = _t('BlogHolder.SUCTITLE', "SilverStripe blog module successfully installed");
			$blog->URLSegment = 'sample-blog-entry';
			$blog->setDate(date("Y-m-d H:i:s",time()));
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

class BlogHolder_Controller extends Page_Controller {
	function init() {
		parent::init();
		
		// This will create a <link> tag point to the RSS feed
		RSSFeed::linkToFeed($this->Link() . "rss", _t('BlogHolder.RSSFEED',"RSS feed of this blog"));
		Requirements::themedCSS("blog");
		Requirements::themedCSS("bbcodehelp");

	}
	
	/**
	 * Gets the archived blogs for a particular month or year, in the format /year/month/ eg: /2008/10/
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
		
		return $output;
	}
	function tag() {
		if($this->ShowTag()) {
			return array(
				'Tag' => $this->ShowTag()
			);
		} else {
			return array();
		}
	}
	
	/**
	 * Get the rss feed for this blog holder's entries
	 */
	function rss() {
		global $project;
		$children = $this->Children();
		$children->sort('Date', 'DESC');
		$rss = new RSSFeed($children, $this->Link(), $project . " blog", "", "Title", "ParsedContent");
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
				_t('BlogHolder.HAVENTPERM',"Posting blogs is an administrator task. Please log in."));
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
