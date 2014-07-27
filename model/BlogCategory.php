<?php

/**
 * A blog category fo generalising blog posts.
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <github@michaelstrong.co.uk>
**/
class BlogCategory extends DataObject {
	
	private static $db = array(
		"Title" => "Varchar(255)",
	);



	private static $has_one = array(
		"Blog" => "Blog",
	);



	private static $belongs_many_many = array(
		"BlogPosts" => "BlogPost",
	);



	private static $extensions = array(
		"URLSegmentExtension",
	);



	public function getCMSFields() {
		$fields = new FieldList(
			TextField::create("Title", _t("BlogCategory.Title", "Title"))
		);
		$this->extend("updateCMSFields", $fields);
		return $fields;
	}



	/**
	 * Returns a relative link to this category.
	 *
	 * @return string URL
	**/
	public function getLink() {
		return Controller::join_links($this->Blog()->Link(), "category", $this->URLSegment);
	}



	/**
	 * Inherits from the parent blog or can be overwritten using a DataExtension
	 *
	 * @param $member Member
	 *
	 * @return boolean
	 */
	public function canView($member = null) {
		$extended = $this->extendedCan(__FUNCTION__, $member);
		if($extended !== null) {
			return $extended;
		}
		return $this->Blog()->canView($member);
	}



	/**
	 * Inherits from the parent blog or can be overwritten using a DataExtension
	 *
	 * @param $member Member
	 *
	 * @return boolean
	 */
	public function canCreate($member = null) {
		$extended = $this->extendedCan(__FUNCTION__, $member);
		if($extended !== null) {
			return $extended;
		}
		return $this->Blog()->canEdit($member);
	}



	/**
	 * Inherits from the parent blog or can be overwritten using a DataExtension
	 *
	 * @param $member Member
	 *
	 * @return boolean
	 */
	public function canDelete($member = null) {
		$extended = $this->extendedCan(__FUNCTION__, $member);
		if($extended !== null) {
			return $extended;
		}
		return $this->Blog()->canEdit($member);
	}



	/**
	 * Inherits from the parent blog or can be overwritten using a DataExtension
	 *
	 * @param $member Member
	 *
	 * @return boolean
	 */
	public function canEdit($member = null) {
		$extended = $this->extendedCan(__FUNCTION__, $member);
		if($extended !== null) {
			return $extended;
		}
		return $this->Blog()->canEdit($member);
	}

}
