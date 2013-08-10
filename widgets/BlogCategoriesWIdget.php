<?php

class BlogCategoriesWidget extends Widget {
	
	private static $title = "Categories";

	private static $cmsTitle = "Blog Categories";

	private static $description = "Displays a list of blog categories.";

	private static $db = array();

	private static $has_one = array(
		"Blog" => "Blog",
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->push(DropdownField::create("BlogID", _t("BlogCategoriesWidget.Blog", "Blog"), Blog::get()->map()));
		return $fields;
	}

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