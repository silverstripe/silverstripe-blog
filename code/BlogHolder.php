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
		'LandingPageFreshness' => 'Varchar',
		'Name' => 'Varchar',
		'TrackBacksEnabled' => 'Boolean'
	);
	
	static $has_one = array(
		"SideBar" => "WidgetArea"
	);
	
	static $has_many = array(
	);
	
	static $many_many = array(
	);
	
	static $allowed_children = array(
		'BlogEntry'
	);
	
	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeFieldFromTab("Root.Content.Main","Content");
		$fields->addFieldToTab("Root.Content.Widgets", new WidgetAreaEditor("SideBar"));
		$fields->addFieldToTab("Root.Content.Main", new TextField("Name", "Name of blog"));
		
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
			"12 MONTH" => "Last year's entries"
		)));
		
		$fields->addFieldToTab('Root.Content.Main', new CheckboxField('TrackBacksEnabled', 'Enable TrackBacks'));
	
		return $fields;
	}

	/**
	 * Get entries in this blog.
	 * @param string limit A clause to insert into the limit clause.
	 * @param string tag Only get blog entries with this tag
	 * @param string date Only get blog entries on this date - either a year, or a year-month eg '2008' or '2008-02'
	 * @return DataObjectSet
	 */
	public function Entries($limit = '', $tag = '', $date = '') {
		$tagCheck = '';
		$dateCheck = '';
		
		if($tag) {
			$SQL_tag = Convert::raw2sql($tag);
			$tagCheck = "AND `BlogEntry`.Tags LIKE '%$SQL_tag%'";
		}
		
		if($date) {
			if(strpos($date, '-')) {
				$year = (int) substr($date, 0, strpos($date, '-'));
				$month = (int) substr($date, strpos($date, '-') + 1);
				
				if($year && $month) {
					$dateCheck = "AND MONTH(`BlogEntry`.Date) = $month AND YEAR(`BlogEntry`.Date) = $year";
				}
			} else {
				$year = (int) $date;
				if($year) {
					$dateCheck = "AND YEAR(`BlogEntry`.Date) = $year";
				}
			}
		}
		
		return DataObject::get("Page","`ParentID` = $this->ID $tagCheck $dateCheck","`BlogEntry`.Date DESC",'',"$limit");
	}

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

class BlogHolder_Controller extends Page_Controller {
	function init() {
		parent::init();
		
		// This will create a <link> tag point to the RSS feed
		RSSFeed::linkToFeed($this->Link() . "rss", _t('BlogHolder.RSSFEED',"RSS feed of this blog"));
		Requirements::themedCSS("blog");
		Requirements::themedCSS("bbcodehelp");

	}
	
	function BlogEntries($limit = 10) {
		$start = isset($_GET['start']) ? (int) $_GET['start'] : 0;
		$tag = '';
		$date = '';
		
		if(Director::urlParams()) {
			if(Director::urlParam('Action') == 'tag') {
				$tag = Director::urlParam('ID');
			} else {
				$year = Director::urlParam('Action');
				$month = Director::urlParam('ID');
				
				if($month && is_numeric($month) &&  $month >= 1 && $month <= 12 && is_numeric($year)) {
					$date = "$year-$month";
				} else if(is_numeric($year)) {
					$date = $year;
				}
			}
		}
		
		return $this->Entries("$start,$limit", $tag, $date);
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

		$blogName = $this->Name;
		$altBlogName = $project . ' blog';
		
		$children = $this->Children();
		$children->sort('Date', 'DESC');
		$rss = new RSSFeed($children, $this->Link(), ($blogName ? $blogName : $altBlogName), "", "Title", "ParsedContent");
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
			Security::permissionFailure($this, _t('BlogHolder.HAVENTPERM', 'Posting blogs is an administrator task. Please log in.'));
		}
		
		$page = $this->customise(array(
			'Content' => false,
			'Form' => $this->BlogEntryForm()
		));
		
		return $page->renderWith('Page');
	}
	
	function defaultAction($action) {
		// Protection against infinite loops when an RSS widget pointing to this page is added to this page
		if(stristr($_SERVER['HTTP_USER_AGENT'], 'SimplePie')) {
			return $this->rss();
		}
		
		return parent::defaultAction($action);
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
		
		$fields = new FieldSet(
			new HiddenField("ID", "ID"),
			new TextField("Title",_t('BlogHolder.SJ', "Subject")),
			new TextField("Author",_t('BlogEntry.AU'),$membername),
			$contentfield,
			new TextField("Tags","Tags"),
			new LiteralField("Tagsnote"," <label id='tagsnote'>"._t('BlogHolder.TE', "For example: sport, personal, science fiction")."<br/>" .
												_t('BlogHolder.SPUC', "Please separate tags using commas.")."</label>")
		);	
		
		$submitAction = new FormAction('postblog', _t('BlogHolder.POST', 'Post blog entry'));
		$actions = new FieldSet($submitAction);
		$validator = new RequiredFields('Title','Content');
			
		$form = new Form($this, 'BlogEntryForm',$fields, $actions,$validator);
	
		if($id != 0) {
			$entry = DataObject::get_by_id('BlogEntry', $id);
			$form->loadNonBlankDataFrom($entry);
			$form->datafieldByName('BlogPost')->setValue($entry->Content);
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
