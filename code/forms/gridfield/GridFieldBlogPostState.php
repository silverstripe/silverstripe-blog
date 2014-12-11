<?php

/**
 * Provides a component to the {@link GridField} which tells the user whether or not a
 * blog post has been published and when.
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <github@michaelstrong.co.uk>
 **/
class GridFieldBlogPostState extends GridFieldSiteTreeState {

	public function getColumnContent($gridField, $record, $columnName) {
		if($columnName == "State") {
			Requirements::css(BLOGGER_DIR . '/css/cms.css');

			if($record->hasMethod("isPublished")) {
				$modifiedLabel = "";
				if($record->isModifiedOnStage) {
					$modifiedLabel = "<span class='modified'>" . _t("GridFieldBlogPostState.Modified") . "</span>";
				}

				$published = $record->isPublished();
				if(!$published) {
					return _t(
						"GridFieldBlogPostState.Draft",
						'<i class="btn-icon gridfield-icon btn-icon-pencil"></i> Saved as Draft on {date}',
						"State for when a post is saved.",
						array(
							"date" => $record->dbObject("LastEdited")->Nice()
						)
					);
				} else if (strtotime($record->PublishDate) > time()) {
					return _t(
						"GridFieldBlogPostState.Timer",
						'<i class="gridfield-icon blog-icon-timer"></i> Publish at {date}',
						"State for when a post is published.",
						array(
							"date" => $record->dbObject("PublishDate")->Nice()
						)
					) . $modifiedLabel;
				} else {
					return _t(
						"GridFieldBlogPostState.Published",
						'<i class="btn-icon gridfield-icon btn-icon-accept"></i> Published on {date}',
						"State for when a post is published.",
						array(
							"date" => $record->dbObject("PublishDate")->Nice()
						)
					) . $modifiedLabel;
				}
			}
		}
	}

	public function getColumnAttributes($gridField, $record, $columnName) {
		if($columnName == "State") {
			if($record->hasMethod("isPublished")) {
				$published = $record->isPublished();
				if(!$published) {
					$class = "gridfield-icon draft";
				} else if (strtotime($record->PublishDate) > time()) {
					$class = "gridfield-icon timer";
				} else {
					$class = "gridfield-icon published";
				}
				return array("class" => $class);
			}
		}
		return array();
	}

}