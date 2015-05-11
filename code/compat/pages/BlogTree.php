<?php

/**
 * @deprecated since version 2.0
 */
class BlogTree extends Page implements MigratableObject {
	/**
	 * @var string
	 */
	private static $hide_ancestor = 'BlogTree';

	/**
	 * @var array
	 */
	private static $db = array(
		'Name' => 'Varchar(255)',
		'LandingPageFreshness' => 'Varchar',
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
