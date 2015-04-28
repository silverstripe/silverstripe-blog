<?php

/**
 * @deprecated since version 2.0
 */
class BlogTree extends Page implements MigratableObject {

	private static $hide_ancestor = 'BlogTree';

	private static $db = array(
		'Name' => 'Varchar(255)',
		'LandingPageFreshness' => 'Varchar',
	);

	public function canCreate($member = null) {
		// Deprecated
		return false;
	}

	public function up() {
		if($this->ClassName === 'BlogTree') {
			$this->ClassName = 'Page';
			$this->write();
		}
	}
}

/**
 * @deprecated since version 2.0
 */
class BlogTree_Controller extends Page_Controller {
}