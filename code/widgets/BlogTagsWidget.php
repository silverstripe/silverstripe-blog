<?php

if(class_exists("Widget")) {

	class BlogTagsWidget extends Widget {
		
		private static $title = "Tags";

		private static $cmsTitle = "Blog Tags";

		private static $description = "Displays a list of blog tags.";

		private static $db = array();

		private static $has_one = array(
			"Blog" => "Blog",
		);

		public function getCMSFields() {
			$this->beforeUpdateCMSFields(function($fields) {
				$fields->push(DropdownField::create("BlogID", _t("BlogTagsWidget.Blog", "Blog"), Blog::get()->map()));
			});
			return parent::getCMSFields();
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

}