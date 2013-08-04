<?php

/**
 * An indivisual blog post.
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <micmania@hotmail.co.uk>
**/
class BlogPost extends Page {

	/**
	 * @var array
	**/
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
		"ShowInMenus" => 0,
	);


	/**
	 * @var array
	**/
	private static $allowed_children = array();
	


	/**
	 * Set the default sort to publish date
	 *
	 * @var string
	**/
	private static $default_sort = "PublishDate DESC";



	/**
	 * Add blog post filter to BlogPost
	 *
	 * @var array
	**/
	private static $extensions = array(
		"BlogPostFilter",
	);



	/**
	 * @var boolean
	**/
	public static $can_be_root = false;



	/**
	 * This will display or hide the current class from the SiteTree. This
	 * variable can be configured using YAML.
	 *
	 * @var boolean
	**/
	private static $show_in_site_tree = false;



	public function getCMSFields() {
		$fields = parent::getCMSFields();

		// Add Publish date fields
		$fields->insertAfter(
			$publishDate = DatetimeField::create("PublishDate", _t("BlogPost.FieldLabels.PUBLISHDATE", "Publish Date")), 
			"Content"
		);
		$publishDate->getDateField()->setConfig("showcalendar", true);

		// Add Categories & Tags fields
		$categories = $this->Parent()->Categories()->map()->toArray();
		$categoriesField = ListboxField::create("Categories", _t("BlogPost.FieldLabels.CATEGORIES", "Categories"), $categories)
			->setMultiple(true);
		$fields->insertAfter($categoriesField, "PublishDate");

		$tags = $this->Parent()->Tags()->map()->toArray();
		$tagsField = ListboxField::create("Tags", _t("BlogPost.FieldLabels.TAGS", "Tags"), $tags)
			->setMultiple(true);
		$fields->insertAfter($tagsField, "Categories");

		// Add featured image
		$fields->insertBefore(
			$uploadField = UploadField::create("FeaturedImage", _t("BlogPost.FieldLabels.FEATUREDIMAGE", "Featured Image")),
			"Content"
		);
        $uploadField->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'));

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
	 * Checks the publish date to see if the blog post as actually been published.
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
	public function getExcerpt($wordCount = 30) {
		return $this->dbObject("Content")->LimitWordCount($wordCount);
	}



	/**
	 * Returns a monthly archive link for the current blog post.
	 *
	 * @return string URL
	**/
	public function getMonthlyArchiveLink() {
		$date = $this->dbObject("PublishDate");
		return Controller::join_links($this->Parent()->Link("archive"), $date->format("Y"), $date->format("m"));
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

}


/**
 * Blog Post controller
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <micmania@hotmail.co.uk>
**/
class BlogPost_Controller extends Page_Controller {
	
}
