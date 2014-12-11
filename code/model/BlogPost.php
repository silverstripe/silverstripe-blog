<?php

/**
 * An indivisual blog post.
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <github@michaelstrong.co.uk>
**/
class BlogPost extends Page {

	private static $db = array(
		"PublishDate" => "SS_Datetime",
	);

	private static $has_one = array(
		"FeaturedImage" => "Image",
	);

	private static $many_many = array(
		"Categories" => "BlogCategory",
		"Tags" => "BlogTag",
	);

	private static $defaults = array(
		"ShowInMenus" => false,
		"InheritSideBar" => true, // Support for widgets
		"ProvideComments" => true, // Support for comments
	);

	private static $extensions = array(
		"BlogPostFilter",
	);

	private static $searchable_fields = array(
		"Title"
	);

	private static $summary_fields = array(
		"Title",
	);

	private static $allowed_children = array();

	private static $default_sort = "PublishDate DESC";

	private static $can_be_root = false;

	/**
	 * This will display or hide the current class from the SiteTree. This
	 * variable can be configured using YAML.
	 *
	 * @var boolean
	**/
	private static $show_in_sitetree = false;



	public function getCMSFields() {
		Requirements::css(BLOGGER_DIR . '/css/cms.css');

		$self =& $this;
		$this->beforeUpdateCMSFields(function($fields) use ($self) {
			$fields->addFieldsToTab('Root.Main', array(
				HeaderField::create('Post Options', 3),
				$publishDate = DatetimeField::create("PublishDate", _t("BlogPost.PublishDate", "Publish Date")),
				ListboxField::create(
					"Categories",
					_t("BlogPost.Categories", "Categories"),
					$self->Parent()->Categories()->map()->toArray()
				)->setMultiple(true),
				ListboxField::create(
					"Tags",
					_t("BlogPost.Tags", "Tags"),
					$self->Parent()->Tags()->map()->toArray()
				)->setMultiple(true)
			));
			$publishDate->getDateField()->setConfig("showcalendar", true);

			// Add featured image
			$fields->insertBefore(
				$uploadField = UploadField::create("FeaturedImage", _t("BlogPost.FeaturedImage", "Featured Image")),
				"Content"
			);
			$uploadField->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'));
		});

		$fields = parent::getCMSFields();

		// We're going to make an SEO tab and move all the usual crap there
		$menuTitle = $fields->dataFieldByName('MenuTitle');
		$urlSegment = $fields->dataFieldByName('URLSegment');
		$fields->addFieldsToTab('Root.SEO', array(
			$menuTitle,
			$urlSegment,
		));

		$metaField = $fields->fieldByName('Root.Main.Metadata');
		if($metaField) {
			$metaFields = $metaField->getChildren();
			if($metaFields->count() > 0) {
				$tab = $fields->findOrMakeTab('Root.SEO');
				$tab->push(HeaderField::create('Meta', 3));
				foreach($metaFields as $field) {
					$tab->push($field);
				}
			}
			$fields->removeByName('Metadata');
		}

		return $fields;
	}



	/**
	 * If no publish date is set, set the date to now.
	**/
	public function onBeforeWrite() {
		parent::onBeforeWrite();
		if(!$this->PublishDate) $this->setCastedField("PublishDate", time());
	}



	/**
	 * Update the PublishDate to now, if being published for the first time, and the date hasn't been set to the future.
	**/
	public function onBeforePublish() {
		if ($this->dbObject('PublishDate')->InPast() && !$this->isPublished()) {
			$this->setCastedField("PublishDate", time());
			$this->write();
		}
	}



	/**
	 * Checks the publish date to see if the blog post has actually been published.
	 *
	 * @param $member Member|null
	 *
	 * @return boolean
	**/
	public function canView($member = null) {
		if(!parent::canView($member)) return false;

		if($this->PublishDate) {
			$publishDate = $this->dbObject("PublishDate");
			if($publishDate->InFuture() && !Permission::checkMember($member, "VIEW_DRAFT_CONTENT")) {
				return false;
			}
		}
		return true;
	}



	/**
	 * Returns the post excerpt.
	 *
	 * @param $wordCount int - number of words to display
	 *
	 * @return string 
	**/
	public function Excerpt($wordCount = 30) {
		return $this->dbObject("Content")->LimitWordCount($wordCount);
	}



	/**
	 * Returns a monthly archive link for the current blog post.
	 *
	 * @param $type string day|month|year
	 *
	 * @return string URL
	**/
	public function getMonthlyArchiveLink($type = "day") {
		$date = $this->dbObject("PublishDate");
		$year = $date->format("Y");
		if($type != "year") {
			if($type == "day") {
				return Controller::join_links(
					$this->Parent()->Link("archive"), 
					$date->format("Y"), 
					$date->format("m"), 
					$date->format("d")
				);
			}
			return Controller::join_links($this->Parent()->Link("archive"), $date->format("Y"), $date->format("m"));
		}
		return Controller::join_links($this->Parent()->Link("archive"), $date->format("Y"));
	}



	/**
	 * Returns a yearly archive link for the current blog post.
	 *
	 * @return string URL
	**/
	public function getYearlyArchiveLink() {
		$date = $this->dbObject("PublishDate");
		return Controller::join_links($this->Parent()->Link("archive"), $date->format("Y"));
	}



	/**
	 * Sets the label for BlogPost.Title to 'Post Title' (Rather than 'Page name')
	 *
	 * @return array
	**/
	public function fieldLabels($includerelations = true) {   
		$labels = parent::fieldLabels($includerelations);
		$labels['Title'] = _t('BlogPost.PageTitleLabel', "Post Title");      
		return $labels;
	}

}


/**
 * Blog Post controller
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <github@michaelstrong.co.uk>
**/
class BlogPost_Controller extends Page_Controller {
	
}
