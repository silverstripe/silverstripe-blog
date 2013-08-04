<?php

class BlogTagsWidget extends Widget {
	
	private static $title = "Tags";

	private static $cmsTitle = "Blog Tags";

	private static $description = "Displays a list of blog tags.";

	private static $db = array();

	private static $has_one = array(
		"Blog" => "Blog",
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->push(DropdownField::create("BlogID", _t("BlogCategoriesWidget.FieldLabels.BLOG", "Blog"), Blog::get()->map()));
		return $fields;
	}

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