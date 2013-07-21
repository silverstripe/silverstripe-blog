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


	/**
	 * @var array
	**/
	private static $allowed_children = array();
	


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
		$fields->insertBefore(
			$publishDate = DatetimeField::create("PublishDate", _t("BlogPost.FieldLabels.PublishDate", "Publish Date")), 
			"Content"
		);
		
		// Publish date field config.
		$publishDate->getDateField()->setConfig("showcalendar", true);

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

}


/**
 * An indivisual blog post.
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <micmania@hotmail.co.uk>
**/
class BlogPost_Controller extends Page_Controller {
	
}