<?php

if(!class_exists("Widget")) {
	return;
}

/**
 * @method Blog Blog()
 */
class BlogTagsWidget extends Widget {
	/**
	 * @var string
	 */
	private static $title = 'Tags';

	/**
	 * @var string
	 */
	private static $cmsTitle = 'Blog Tags';

	/**
	 * @var string
	 */
	private static $description = 'Displays a list of blog tags.';

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
			if (!$fields) {
				return;
			}

			/**
			 * @var FieldList $fields
			 */
			$fields->push(
				DropdownField::create('BlogID', _t('BlogTagsWidget.Blog', 'Blog'), Blog::get()->map())
			);
		});

		return parent::getCMSFields();
	}

	/**
	 * @return array
	 */
	public function getTags() {
		$blog = $this->Blog();

		if($blog) {
			return $blog->Tags();
		}

		return array();
	}
}

class BlogTagsWidget_Controller extends Widget_Controller {

}
