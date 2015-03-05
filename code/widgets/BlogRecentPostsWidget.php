<?php

if(class_exists("Widget")) {

	class BlogRecentPostsWidget extends Widget {

		private static $title = "Recent Posts";

		private static $cmsTitle = "Recent Posts";

		private static $description = "Displays a list of recent blog posts.";

		private static $db = array(
			"Title" => "Varchar(255)",
			"NumberOfPosts" => "Int",
		);

		private static $has_one = array(
			"Blog" => "Blog",
		);

		public function Title() {
			return $this->getField('Title') ?: parent::Title();
		}

		public function populateDefaults() {
			parent::populateDefaults();
			$this->setField('Title', parent::Title());
		}

		public function getCMSFields() {
			$fields = FieldList::create();
			$fields->merge(array(
				TextField::create('Title', 'Title', null, 255),
				DropdownField::create("BlogID", _t("BlogRecentPostsWidget.Blog", "Blog"), Blog::get()->map()),
				NumericField::create("NumberOfPosts", _t("BlogRecentPostsWidget.NumberOfPosts", "Number of Posts"))
			));
			$this->extend("updateCMSFields", $fields);
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
}
