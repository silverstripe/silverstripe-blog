<?php

if(!class_exists("Widget")) {
	return;
}

/**
 * @method Blog Blog()
 */
class BlogCategoriesWidget extends Widget {
	/**
	 * @var string
	 */
	private static $title = 'Categories';

	/**
	 * @var string
	 */
	private static $cmsTitle = 'Blog Categories';

	/**
	 * @var string
	 */
	private static $description = 'Displays a list of blog categories.';

	/**
	 * @var array
	 */
	private static $db = array();

	/**
	 * @var array
	 */
	private static $has_one = array(
		'Blog' => 'Blog',
	);

	/**
	 * {@inheritdoc}
	 */
	public function getCMSFields() {
		$this->beforeUpdateCMSFields(function ($fields) {
			$fields->push(
				DropdownField::create('BlogID', _t('BlogCategoriesWidget.Blog', 'Blog'), Blog::get()->map())
			);
		});

		return parent::getCMSFields();
	}

	/**
	 * @return array
	 */
	public function getCategories() {
		$blog = $this->Blog();

		if($blog) {
			return $blog->Categories();
		}

		return array();
	}
}

class BlogCategoriesWidget_Controller extends Widget_Controller {

}
