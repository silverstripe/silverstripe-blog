<?php

/**
 * @deprecated since version 2.0
 */
class BlogHolder extends BlogTree implements MigratableObject {
	/**
	 * @var string
	 */
	private static $hide_ancestor = 'BlogHolder';

	/**
	 * @var array
	 */
	private static $db = array(
		'AllowCustomAuthors' => 'Boolean',
		'ShowFullEntry' => 'Boolean',
	);

	/**
	 * @var array
	 */
	private static $has_one = array(
		'Owner' => 'Member',
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
