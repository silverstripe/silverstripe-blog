<?php

/**
 * A blog tag for keyword descriptions of a Blog post
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <micmania@hotmail.co.uk>
**/
class BlogTag extends DataObject {
	
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
			TextField::create("Title", _t("BlogTag.FieldLabels.TITLE", "Title"))
		);
		$this->extend("updateCMSFields", $fields);
		return $fields;
	}


	/**
	 * Returns a relative URL for the tag link.
	 *
	 * @return string URL
	**/
	public function getLink() {
		return Controller::join_links($this->Blog()->Link(), "tag", $this->URLSegment);
	}

}