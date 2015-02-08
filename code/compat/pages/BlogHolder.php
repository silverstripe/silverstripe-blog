<?php

/**
 * @deprecated since version 2.0
 */
class BlogHolder extends BlogTree implements MigratableObject {

	private static $hide_ancestor = 'BlogHolder';
	
	private static $db = array(
		'AllowCustomAuthors' => 'Boolean',
		'ShowFullEntry' => 'Boolean',
	);

	private static $has_one = array(
		'Owner' => 'Member',
	);

	public function canCreate($member = null) {
		// Deprecated
		return false;
	}

	public function up() {
		if($this->ClassName === 'BlogHolder') {
			$this->ClassName = 'Blog';
			$this->write();
		}
	}
}

/**
 * @deprecated since version 2.0
 */
class BlogHolder_Controller extends BlogTree_Controller {
	
}