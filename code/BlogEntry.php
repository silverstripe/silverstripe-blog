<?php
/**
 * An individual blog entry page type.
 * 
 * @package blog
 */
class BlogEntry extends Page {

	private static $db = array(
		"Date" => "SS_Datetime",
		"Author" => "Text",
		"Tags" => "Text"
	);
	
	private static $default_parent = 'BlogHolder';
	
	private static $can_be_root = false;
	
	private static $icon = "blog/images/blogpage-file.png";

	private static $description = "An individual blog entry";
	
	private static $singular_name = 'Blog Entry Page';
	
	private static $plural_name = 'Blog Entry Pages';
		
	private static $has_one = array();
	
	private static $has_many = array();
	
	private static $many_many = array();
	
	private static $belongs_many_many = array();
	
	private static $defaults = array(
		"ProvideComments" => true,
		'ShowInMenus' => false
	);
	
	/**
	 * Is WYSIWYG editing allowed?
	 * @var boolean
	 */
	static $allow_wysiwyg_editing = true;
	
	/**
	 * Overload so that the default date is today.
	 */
	public function populateDefaults(){
		parent::populateDefaults();
		
		$this->setField('Date', date('Y-m-d H:i:s', strtotime('now')));
	}
	
	function getCMSFields() {
		Requirements::javascript('blog/javascript/bbcodehelp.js');
		Requirements::themedCSS('bbcodehelp');
		
		$firstName = Member::currentUser() ? Member::currentUser()->FirstName : '';
		$codeparser = new BBCodeParser();
		
		SiteTree::disableCMSFieldsExtensions();
		$fields = parent::getCMSFields();
		SiteTree::enableCMSFieldsExtensions();
		
		if(!self::$allow_wysiwyg_editing) {
			$fields->removeFieldFromTab("Root.Main","Content");
			$fields->addFieldToTab("Root.Main", new TextareaField("Content", _t("BlogEntry.CN", "Content"), 20));
		}
		
		$fields->addFieldToTab("Root.Main", $dateField = new DatetimeField("Date", _t("BlogEntry.DT", "Date")),"Content");
		$dateField->getDateField()->setConfig('showcalendar', true);
		$dateField->getTimeField()->setConfig('timeformat', 'H:m:s');
		$fields->addFieldToTab("Root.Main", new TextField("Author", _t("BlogEntry.AU", "Author"), $firstName),"Content");
		
		if(!self::$allow_wysiwyg_editing) {
			$fields->addFieldToTab("Root.Main", new LiteralField("BBCodeHelper", "<div id='BBCode' class='field'>" .
							"<a  id=\"BBCodeHint\" target='new'>" . _t("BlogEntry.BBH", "BBCode help") . "</a>" .
							"<div id='BBTagsHolder' style='display:none;'>".$codeparser->useable_tagsHTML()."</div></div>"));
		}
				
		$fields->addFieldToTab("Root.Main", new TextField("Tags", _t("BlogEntry.TS", "Tags (comma sep.)")),"Content");
		
		$this->extend('updateCMSFields', $fields);
		
		return $fields;
	}
	
	/**
	 * Safely split and parse all distinct tags assigned to this BlogEntry
	 * 
	 * @return array Associative array of lowercase tag to native case tags
	 */
	public function TagNames() {
		$tags = preg_split("/\s*,\s*/", trim($this->Tags));
		$results = array();
		foreach($tags as $tag) {
			if($tag) $results[mb_strtolower($tag)] = $tag;
		}
		return $results;
	}
	
	/**
	 * Returns the tags added to this blog entry
	 * 
	 * @return ArrayList List of ArrayData with Tag, Link, and URLTag keys
	 */
	public function TagsCollection() {

		$tags = $this->TagNames();
		$output = new ArrayList();
		
		$link = ($parent = $this->getParent()) ? $parent->Link('tag') : '';
		foreach($tags as $tag => $tagLabel) {
			$urlKey = urlencode($tag);
			$output->push(new ArrayData(array(
				'Tag' => $tagLabel,
				'Link' => Controller::join_links($link, $urlKey),
				'URLTag' => $urlKey
			)));
		}
		
		return $output;
	}

	function Content() {	
		if(self::$allow_wysiwyg_editing) {
			return $this->getField('Content');
		} else {
			$parser = new BBCodeParser($this->Content);
			$content = new HTMLText('Content');
			$content->value = $parser->parse();
			return $content;
		}
	}
	
	/**
	 * To be used by RSSFeed. If RSSFeed uses Content field, it doesn't pull in correctly parsed content. 
	 */ 
	function RSSContent() {
		return $this->Content();
	}
	
	/**
	 * Get a bbcode parsed summary of the blog entry
	 * @deprecated
	 */
	function ParagraphSummary(){
		user_error("BlogEntry::ParagraphSummary() is deprecated; use BlogEntry::Content()", E_USER_NOTICE);
		
		$val = $this->Content(); 
		$content = $val; 
		
		if(!($content instanceof HTMLText)) {
			$content = new HTMLText('Content');
			$content->value = $val;
		}

		return $content->FirstParagraph('html');
	}
	
	/**
	 * Get the bbcode parsed content
	 * @deprecated
	 */
	function ParsedContent() {
		user_error("BlogEntry::ParsedContent() is deprecated; use BlogEntry::Content()", E_USER_NOTICE);
		return $this->Content();
	}
	
	/**
	 * Link for editing this blog entry
	 */
	function EditURL() {
		return ($this->getParent()) ? $this->getParent()->Link('post') . '/' . $this->ID . '/' : false;
	}
	
	function IsOwner() {
		if(method_exists($this->Parent(), 'IsOwner')) {
			return $this->Parent()->IsOwner();
		}
	}
	
	/**
	 * Call this to enable WYSIWYG editing on your blog entries.
	 * By default the blog uses BBCode
	 */
	static function allow_wysiwyg_editing() {
		self::$allow_wysiwyg_editing = true;
	}
	
	
	/**
	 * Get the previous blog entry from this section of blog pages. 
	 *
	 * @return BlogEntry
	 */
	function PreviousBlogEntry() {
		return DataObject::get_one(
			'BlogEntry', 
			"\"SiteTree\".\"ParentID\" = '$this->ParentID' AND \"BlogEntry\".\"Date\" < '$this->Date'", 
			true, 
			'Date DESC'
		);
	}
	
	/**
	 * Get the next blog entry from this section of blog pages.
	 *
	 * @return BlogEntry
	 */
	function NextBlogEntry() {
		return DataObject::get_one(
			'BlogEntry', 
			"\"SiteTree\".\"ParentID\" = '$this->ParentID' AND \"BlogEntry\".\"Date\" > '$this->Date'", 
			true, 
			'Date ASC'
		);		
	}

	/**
	 * Get the blog holder of this entry
	 *
	 * @return BlogHolder
	 */
	function getBlogHolder() {
		$holder = null; 
		if($this->ParentID && $this->Parent()->ClassName == 'BlogHolder') {
			$holder = $this->Parent(); 
		}

		return $holder;
	}
}

class BlogEntry_Controller extends Page_Controller {
	
	private static $allowed_actions = array(
		'index',
		'unpublishPost',
		'PageComments',
		'SearchForm'
	);

	function init() {
		parent::init();
		
		Requirements::themedCSS("blog","blog");
	}
	
	/**
	 * Gets a link to unpublish the blog entry
	 */
	function unpublishPost() {
		if(!$this->IsOwner()) {
			Security::permissionFailure(
				$this,
				'Unpublishing blogs is an administrator task. Please log in.'
			);
		} else {
			$SQL_id = (int) $this->ID;
	
			$page = DataObject::get_by_id('SiteTree', $SQL_id);
			$page->deleteFromStage('Live');
			$page->flushCache();

			$this->redirect($this->getParent()->Link());
		}		
	}
	
	/**
	 * Temporary workaround for compatibility with 'comments' module
	 * (has been extracted from sapphire/trunk in 12/2010).
	 * 
	 * @return Form
	 */
	function PageComments() {
		if($this->hasMethod('CommentsForm')) return $this->CommentsForm();
		else if(method_exists('Page_Controller', 'PageComments')) return parent::PageComments();
	}
		
}
