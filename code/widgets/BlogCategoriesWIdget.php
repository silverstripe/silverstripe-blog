<?php

if(class_exists("Widget")) {

	class BlogCategoriesWidget extends Widget {
		
		private static $title = "Categories";

		private static $cmsTitle = "Blog Categories";

		private static $description = "Displays a list of blog categories.";

		private static $db = array();

		private static $has_one = array(
			"Blog" => "Blog",
		);

		public function getCMSFields() {
			$fields = FieldList::create();
			$fields->push(
				DropdownField::create("BlogID", _t("BlogCategoriesWidget.Blog", "Blog"), Blog::get()->map())
			);
			$this->extend("updateCMSFields", $fields);
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

}
