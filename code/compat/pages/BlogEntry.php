<?php

/**
 * @deprecated since version 2.0
 *
 * @property int $ParentID
 * @property string $Date
 * @property string $PublishDate
 * @property string $Tags
 */
class BlogEntry extends BlogPost implements MigratableObject {
	/**
	 * @var string
	 */
	private static $hide_ancestor = 'BlogEntry';

	/**
	 * @var array
	 */
	private static $db = array(
		'Date' => 'SS_Datetime',
		'Author' => 'Text',
		'Tags' => 'Text',
	);

	/**
	 * {@inheritdoc}
	 */
	public function canCreate($member = null) {
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		foreach($this->TagNames() as $tag) {
			if($this->Tags()->filter('Title', $tag)->count()) {
				continue;
			}

			$tagObject = new BlogTag();
			$tagObject->Title = $tag;
			$tagObject->BlogID = $this->ParentID;

			$tagObject->write();

			$this->Tags()->add($tagObject);
		}

		$this->PublishDate = $this->Date;

		if($this->ClassName === 'BlogEntry') {
			$this->ClassName = 'BlogPost';
			$this->write();
		}
	}

	/**
	 * Safely split and parse all distinct tags assigned to this BlogEntry.
	 *
	 * @deprecated since version 2.0
	 *
	 * @return array
	 */
	public function TagNames() {
		$tags = preg_split('/\s*,\s*/', trim($this->Tags));

		$results = array();

		foreach($tags as $tag) {
			if($tag) $results[mb_strtolower($tag)] = $tag;
		}

		return $results;
	}

	/**
	 * {@inheritdoc}
	 */
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
