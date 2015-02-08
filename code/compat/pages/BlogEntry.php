<?php

/**
 * @deprecated since version 2.0
 */
class BlogEntry extends BlogPost implements MigratableObject {
	
	private static $hide_ancestor = 'BlogEntry';

	private static $db = array(
		"Date" => "SS_Datetime",
		"Author" => "Text",
		"Tags" => "Text"
	);

	/**
	 * Safely split and parse all distinct tags assigned to this BlogEntry
	 *
	 * @return array Associative array of lowercase tag to native case tags
	 * @deprecated since version 2.0
	 */
	public function TagNames() {
		$tags = preg_split("/\s*,\s*/", trim($this->Tags));
		$results = array();
		foreach($tags as $tag) {
			if($tag) $results[mb_strtolower($tag)] = $tag;
		}
		return $results;
	}

	public function canCreate($member = null) {
		// Deprecated
		return false;
	}

	public function up() {
		// Migrate tags
		foreach($this->TagNames() as $tag) {
			// Skip if tag already assigned
			if($this->Tags()->filter('Title', $tag)->count()) continue;

			// Create tag
			$tagObject = new BlogTag();
			$tagObject->Title = $tag;
			$tagObject->BlogID = $this->ParentID;
			$tagObject->write();
			$this->Tags()->add($tagObject);
		}

		// Update fields
		$this->PublishDate = $this->Date;
		if($this->ClassName === 'BlogEntry') {
			$this->ClassName = 'BlogPost';
			$this->write();
		}
	}

	public function requireDefaultRecords() {
		parent::requireDefaultRecords();

		if(BlogMigrationTask::config()->run_during_dev_build) {
			$task = new BlogMigrationTask();
			$task->up();
		}
	}
}

/**
 * @deprecated since version 2.0
 */
class BlogEntry_Controller extends BlogPost_Controller {
}
