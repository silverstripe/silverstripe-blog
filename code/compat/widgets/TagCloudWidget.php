<?php

if(! class_exists('Widget')) return;

/**
 * A list of tags associated with blog posts
 *
 * @package blog
 */
class TagCloudWidget extends BlogTagsWidget implements MigratableObject {

	private static $db = array(
		"Title" => "Varchar",
		"Limit" => "Int",
		"Sortby" => "Varchar"
	);

	private static $only_available_in = array('none');

	public function canCreate($member = null) {
		// Deprecated
		return false;
	}

	public function up() {
		$this->ClassName = 'BlogTagsWidget';
		$this->write();
	}
}