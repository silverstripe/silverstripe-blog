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
class GridFieldBlogPostState implements GridField_ColumnProvider {

	public function augmentColumns($gridField, &$columns) {
        // Ensure Actions always appears as the last column.
        $key = array_search("Actions", $columns);
        if($key !== FALSE) unset($columns[$key]);

		$columns = array_merge($columns, array(
			"State",
			"Actions",
		));
	}

	public function getColumnsHandled($gridField) {
		return array("State");
	}

	public function getColumnContent($gridField, $record, $columnName) {
		if($columnName == "State") {
			if($record->hasMethod("isPublished")) {
				$modifiedLabel = "";
				if($record->isModified()) {
					$modifiedLabel = "<span class='modified'>" . _t("GridFieldBlogPostState.Modified") . "</span>";
				} 

				$published = $record->isPublished();
				if(!$published) {
					return _t(
						"GridFieldBlogPostState.Draft", 
						'<i class="btn-icon blog-icon btn-icon-pencil"></i> Saved as Draft on {date}',
						"State for when a post is saved.", 
						array(
							"date" => $record->dbObject("LastEdited")->Nice()
						)
					);
				} else if (strtotime($record->PublishDate) > time()) {
					return _t(
						"GridFieldBlogPostState.Timer", 
						'<i class="blog-icon blog-icon-timer"></i> Publish at {date}', 
						"State for when a post is published.", 
						array(
							"date" => $record->dbObject("PublishDate")->Nice()
						)
					) . $modifiedLabel;
				} else {
					return _t(
						"GridFieldBlogPostState.Published", 
						'<i class="btn-icon blog-icon btn-icon-accept"></i> Published on {date}', 
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
					$class = "blog-icon draft";
				} else if (strtotime($record->PublishDate) > time()) {
					$class = "blog-icon timer";
				} else {
					$class = "blog-icon published";
				}
				return array("class" => $class);
			}
		}
		return array();
	}

	public function getColumnMetaData($gridField, $columnName) {
		switch($columnName) {
			case 'State':
				return array("title" => _t("GridFieldBlogPostState.StateTitle", "State", "Column title for state"));
		}
	}

}