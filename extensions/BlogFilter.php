<?php

/**
 * Base extension of BlogPost.
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <micmania@hotmail.co.uk>
 *
 **/
class BlogFilter extends Hierarchy {

	/**
	 * Augments (@link Hierarchy::stageChildren()}
	 *
	 * @param $staged DataList
	 * @param $showAll boolean
	**/
	public function stageChildren($showAll = false) {
		$staged = parent::stageChildren($showAll);

		$controller = Controller::curr();
		if($controller->class == "CMSPagesController" && in_array($controller->getAction(), array("treeview", "listview", "getsubtree"))) {
			return $staged->exclude("ClassName", $this->owner->getExcludedSiteTreeClassNames());
		} else if(in_array($this->owner->ClassName, ClassInfo::subClassesFor("Blog")) && !Permission::check("VIEW_DRAFT_CONTENT")) {

			// Get the current stage.
			$stage = Versioned::current_stage();
			if($stage == "Stage") $stage = "";
			else $stage = "_" . Convert::raw2sql($stage);

			$dataQuery = $staged->dataQuery()
				->innerJoin("BlogPost", "BlogPost" . $stage . ".ID = SiteTree" . $stage . ".ID")
				->where("PublishDate < NOW()");
			$staged = $staged->setDataQuery($dataQuery);

		}
		return $staged;
	}



	/**
	 * Augments (@link Hierarchy::liveChildren()}
	 *
	 * @param $staged DataList
	 * @param $showAll boolean
	**/
	public function liveChildren($showAll = false, $onlyDeletedFromStage = false) {
		$staged = parent::liveChildren($showAll, $onlyDeletedFromStage);

		$controller = Controller::curr();
		if($controller->class == "CMSPagesController" && in_array($controller->getAction(), array("treeview", "listview", "getsubtree"))) {
			return $staged->exclude("ClassName", $this->owner->getExcludedSiteTreeClassNames());
		} else if(in_array($this->owner->ClassName, ClassInfo::subClassesFor("Blog")) && !Permission::check("VIEW_DRAFT_CONTENT")) {
			$dataQuery = $staged->dataQuery()
				->innerJoin("BlogPost", "BlogPost_Live.ID = SiteTree_Live.ID")
				->where("PublishDate < NOW()");
			$staged = $staged->setDataQuery($dataQuery);
		}
		return $staged;
	}

}