<?php

class BlogRecentPostsWidget extends Widget {
	
	private static $title = "Recent Posts";

	private static $cmsTitle = "Recent Posts";

	private static $description = "Displays a list of recent blog posts.";

	private static $db = array(
		"NumberOfPosts" => "Int",
	);

	private static $has_one = array(
		"Blog" => "Blog",
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->push(DropdownField::create("BlogID", _t("BlogCategoriesWidget.FieldLabels.BLOG", "Blog"), Blog::get()->map()));
		$fields->push(NumericField::create("NumberOfPosts", _t("BlogRecentPostsWidget.FieldLabels.NUMBEROFPOSTS", "Number of Posts")));
		return $fields;
	}

	public function getPosts() {
		$blog = $this->Blog();
		if($blog) {
			return $blog->getBlogPosts()
				->sort("PublishDate DESC")
				->limit($this->NumberOfPosts);
		}
		return array();
	}

}

class BlogRecentPostsWidget_Controller extends Widget_Controller {
	
}