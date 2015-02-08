<?php

/**
 * @deprecated since version 2.0
 */
class BlogTree extends Blog implements MigratableObject {

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
			$this->ClassName = 'Blog';
			$this->write();
		}
	}
}

/**
 * @deprecated since version 2.0
 */
class BlogTree_Controller extends Blog_Controller {
}