<?php

/**
 * @package blog
 */
 
/**
 * An individual blog entry page to show a blog entry in full
 */
class BlogEntry extends Page {
	
	static $default_parent = array('BlogHolder');
	
	static $can_be_root = false;
	
		static $icon = "blog/images/blogpage";
	
	static $db = array(
		"Date" => "Datetime",
		"Author" => "Text",
		"Tags" => "Text"
	);
	
	static $casting = array(
		"Date" => "Date"
	);
	
	static $defaults = array(
		"ProvideComments" => true
	);
		
	static $allowed_children = "none";
		
	/**
	 * overload so that the default date is today.
	 */
	public function populateDefaults(){
		parent::populateDefaults();
		$this->Date = date("d/m/Y H:i:s",time());
	}
	
	/**
	 * Ensures the most recent article edited on the same day is shown first.
	 */
	public function setDate($val){
		$datepart = date("Y-m-d",strtotime($val));
		$minutepart = date("H:i:s",time());
		$date = $datepart . " " . $minutepart;	
		return $this->setField("Date",$date);
	}

	function getCMSFields() {
		Requirements::javascript('blog/javascript/bbcodehelp.js');
		Requirements::css('blog/css/bbcodehelp.css');
		$firstName = Member::CurrentMember() ? Member::currentMember()->FirstName : '';
		 $codeparser = new BBCodeParser();
		 
		$fields = parent::getCMSFields();
		$fields->removeFieldFromTab("Root.Content.Main","Content");
		$fields->addFieldToTab("Root.Content.Main", new TextareaField("Content", "Content", 20));
		$fields->addFieldToTab("Root.Content.Main", new CalendarDateField("Date", "Date"),"Content");
		$fields->addFieldToTab("Root.Content.Main", new TextField("Author", "Author", $firstName),"Content");
		
		$fields->addFieldToTab("Root.Content.Main", new LiteralField("BBCodeHelper", "<div id='BBCode' class='field'>" .
						"<a  id=\"BBCodeHint\" target='new'>BBCode help</a>" .
						"<div id='BBTagsHolder' style='display:none;'>".$codeparser->useable_tagsHTML()."</div></div>"));
				
		$fields->addFieldToTab("Root.Content.Main", new TextField("Tags", "Tags (comma sep.)"),"Content");
		return $fields;
	}
	
	/**
	 * Returns the tags added to this blog entry
	 */
	function Tags() {
		$theseTags = split(" *, *", trim($this->Tags));
		
		$output = new DataObjectSet();
		foreach($theseTags as $tag) {
			$output->push(new ArrayData(array(
				"Tag" => $tag,
				"Link" => $this->getParent()->Link() . '?tag=' . urlencode($tag)		
			)));
		}
		if($this->Tags){
			return $output;
		}
	}

	/**
	 * Get the sidebar
	 */
	function SideBar() {
		return $this->getParent()->SideBar();
	}
	
	/**
	 * Get a bbcode parsed summary of the blog entry
	 */
	function ParagraphSummary(){
		$content = new Text('Content');
		$content->value = Convert::raw2xml($this->Content);
		$parser = new BBCodeParser($content->FirstParagraph());
		return $parser->parse();		
	}
	
	/**
	 * Get the bbcode parsed content
	 */
	function ParsedContent() {
		$parser = new BBCodeParser($this->Content);
		$content = new Text('Content');
		$content->value =$parser->parse();
		return $content;
	}
	
	/**
	 * Link for editing this blog entry 
	 */
	function EditURL(){
		return $this->getParent()->Link('post')."/".$this->ID."/";
	}

}

class BlogEntry_Controller extends Page_Controller {
	function init() {
		parent::init();
		Requirements::themedCSS("blog");
	}
	
	/**
	 * Gets a link to unpublish the blog entry
	 */
	function unpublishPost(){	
		if(!Permission::check('ADMIN')){
			Security::permissionFailure($this,
				"Unpublishing blogs is an administrator task. Please log in.");
		}
		else{
			$SQL_id = Convert::raw2sql($this->ID);
	
			$page = DataObject::get_by_id("SiteTree", $SQL_id);
			$page->deleteFromStage('Live');
			$page->flushCache();
	
			$page = DataObject::get_by_id("SiteTree", $SQL_id);
			$page->Status = "Unpublished";

			Director::redirect($this->getParent()->Link());
		}		
	}
	
}

?>