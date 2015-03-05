<?php

if(class_exists("Widget")) {

	class BlogTagsWidget extends Widget {

		private static $title = "Tags";

		private static $cmsTitle = "Blog Tags";

		private static $description = "Displays a list of blog tags.";

		private static $db = array(
			"Title" => "Varchar(255)",
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
				DropdownField::create("BlogID", _t("BlogTagsWidget.Blog", "Blog"), Blog::get()->map())
			));
			$this->extend("updateCMSFields", $fields);
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
}
