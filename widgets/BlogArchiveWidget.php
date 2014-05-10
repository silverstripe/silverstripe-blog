<?php

if(class_exists("Widget")) {

	class BlogArchiveWidget extends Widget {
		
		private static $title = "Archive";

		private static $cmsTitle = "Archive";

		private static $description = "Displays an archive list of posts.";

		private static $db = array(
			"NumberToDisplay" => "Int",
			"Type" => "Enum('Monthly, Yearly', 'Monthly')"
		);

		private static $defaults = array(
			"NumberOfMonths" => 12
		);

		private static $has_one = array(
			"Blog" => "Blog",
		);

		public function getCMSFields() {
			$fields = parent::getCMSFields();

			$type = $this->dbObject("Type")->enumValues();
			foreach($type as $k => $v) {
				$type[$k] = _t("BlogArchiveWidget." . ucfirst(strtolower($v)), $v);
			}

			$fields->merge(array(
				DropdownField::create("BlogID", _t("BlogArchiveWidget.Blog", "Blog"), Blog::get()->map()),
				DropdownField::create("Type", _t("BlogArchiveWidget.Type", "Type"), $type),
				NumericField::create("NumberToDisplay", _t("BlogArchiveWidget.NumberToDisplay", "No. to Display"))
			));
			$this->extend("updateCMSFields", $fields);
			return $fields;
		}


		/**
		 * Returns a list of months where blog posts are present.
		 *
		 * @return DataList
		**/
		public function getArchive() {
			$query = $this->Blog()->getBlogPosts()->dataQuery();

			if($this->Type == "Yearly") {
				$query->groupBy("DATE_FORMAT(PublishDate, '%Y')");
			} else {
				$query->groupBy("DATE_FORMAT(PublishDate, '%Y-%M')");
			}

			$articles = $this->Blog()->getBlogPosts()->setDataQuery($query);
			if($this->NumberToDisplay > 0) $articles = $articles->limit($this->NumberToDisplay);
			
			$archive = new ArrayList();
			if($articles->count() > 0) {
				foreach($articles as $article) {
					if($this->Type == "Yearly") {
						$year = date('Y', strtotime($article->PublishDate));
						$month = null;
						$title = $year;
					} else {
						$year = date('Y', strtotime($article->PublishDate));
						$month = date('m', strtotime($article->PublishDate));
						$title = date('F Y', strtotime($article->PublishDate));
					}
					$archive->push(new ArrayData(array(
						"Title" => $title,
						"Link" => Controller::join_links($this->Blog()->Link("archive"), $year, $month)
					)));
				}
			}
			return $archive;
		}

	}

	class BlogArchiveWidget_Controller extends Widget_Controller {
		
	}

}
