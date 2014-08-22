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
	
	private static $defaults = array(
		"ProvideComments" => true,
		'ShowInMenus' => false
	);
	
	/**
	 * Is WYSIWYG editing allowed?
	 * @var boolean
	 */
	public static $allow_wysiwyg_editing = true;
	
	/**
	 * Overload so that the default date is today.
	 */
	public function populateDefaults(){
		parent::populateDefaults();
		
		$this->setField('Date', SS_DateTime::now()->getValue());
	}
	
	public function getCMSFields() {
		Requirements::javascript('blog/javascript/bbcodehelp.js');
		Requirements::themedCSS('bbcodehelp', 'blog');

		// Add fields prior to extension
		$this->beforeUpdateCMSFields(function($fields) {
			// Disable HTML editing if wysiwyg is disabled
			if(!BlogEntry::$allow_wysiwyg_editing) {
				$fields->FieldList('Content', TextareaField::create("Content", _t("BlogEntry.CN", "Content"), 20));
			}

			// Date field
			$dateField = DatetimeField::create("Date", _t("BlogEntry.DT", "Date"));;
			$dateField->getDateField()->setConfig('showcalendar', true);
			$dateField->getTimeField()->setConfig('timeformat', 'H:m:s');
			$fields->addFieldToTab("Root.Main", $dateField, "Content");

			// Author field
			$firstName = Member::currentUser() ? Member::currentUser()->FirstName : '';
			$fields->addFieldToTab(
				"Root.Main",
				TextField::create("Author", _t("BlogEntry.AU", "Author"), $firstName),
				"Content"
			);

			// BB code hints
			if(!BlogEntry::$allow_wysiwyg_editing) {
				$codeparser = BBCodeParser::create();
				$hintField = new LiteralField(
					"BBCodeHelper",
					"<div id='BBCode' class='field'>" .
					"<a id=\"BBCodeHint\" target='new'>" . _t("BlogEntry.BBH", "BBCode help") . "</a>" .
					"<div id='BBTagsHolder' style='display:none;'>".$codeparser->useable_tagsHTML()."</div></div>"
				);
				$fields->addFieldToTab("Root.Main", $hintField);
			}

			// Tags
			$fields->addFieldToTab(
				"Root.Main",
				TextField::create("Tags", _t("BlogEntry.TS", "Tags (comma sep.)")),
				"Content"
			);
		});
		
		return parent::getCMSFields();
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

	public function Content() {
		if(self::$allow_wysiwyg_editing) {
			return $this->getField('Content');
		} else {
			$parser = BBCodeParser::create($this->Content);
			$content = new HTMLText('Content');
			$content->value = $parser->parse();
			return $content;
		}
	}
	
	/**
	 * To be used by RSSFeed. If RSSFeed uses Content field, it doesn't pull in correctly parsed content.
	 *
	 * @return string
	 */ 
	public function RSSContent() {
		return $this->Content();
	}
	
	/**
	 * Link for editing this blog entry
	 *
	 * @return string Edit URL
	 */
	public function EditURL() {
		$parent = $this->getParent();
		if($parent) {
			return Controller::join_links($parent->Link('post'), $this->ID);
		}
	}

	/**
	 * Returns true if the current user is an admin, or is the owner of this blog
	 * {@see BlogHolder::IsOwner}
	 *
	 * @return bool
	 */
	public function IsOwner() {
		$parent = $this->Parent();
		return $parent && $parent->hasMethod('IsOwner') && $parent->IsOwner();
	}
	
	/**
	 * Call this to enable WYSIWYG editing on your blog entries.
	 * By default the blog uses BBCode
	 */
	public static function allow_wysiwyg_editing() {
		self::$allow_wysiwyg_editing = true;
	}
	
	
	/**
	 * Get the next oldest blog entry from this section of blog pages.
	 *
	 * @return BlogEntry
	 */
	public function PreviousBlogEntry() {
		return BlogEntry::get()
			->filter('ParentID', $this->ParentID)
			->exclude('ID', $this->ID)
			->filter('Date:LessThanOrEqual', $this->Date)
			->sort('"BlogEntry"."Date" DESC')
			->first();
	}
	
	/**
	 * Get the next most recent blog entry from this section of blog pages.
	 *
	 * @return BlogEntry
	 */
	public function NextBlogEntry() {
		return BlogEntry::get()
			->filter('ParentID', $this->ParentID)
			->exclude('ID', $this->ID)
			->filter('Date:GreaterThanOrEqual', $this->Date)
			->sort('"BlogEntry"."Date" ASC')
			->first();	
	}

	/**
	 * Get the blog holder of this entry
	 *
	 * @return BlogHolder
	 */
	public function getBlogHolder() {
		$holder = null; 
		if($this->ParentID && $this->Parent()->ClassName == 'BlogHolder') {
			$holder = $this->Parent(); 
		}

		return $holder;
	}
}

class BlogEntry_Controller extends Page_Controller {
	
	private static $allowed_actions = array(
		'unpublishPost'
	);

	public function init() {
		parent::init();
		
		Requirements::themedCSS("blog", "blog");
	}
	
	/**
	 * Gets a link to unpublish the blog entry
	 */
	public function unpublishPost() {
		if(!$this->IsOwner()) {
			Security::permissionFailure(
				$this,
				'Unpublishing blogs is an administrator task. Please log in.'
			);
		} else {
			$page = BlogEntry::get()->byID($this->data()->ID);
			$page->deleteFromStage('Live');
			$page->flushCache();

			return $this->redirect($this->getParent()->Link());
		}		
	}
		
}
