<?php

/**
 * This class is responsible for filtering the SiteTree when necessary and also
 * overlaps into filtering only published posts.
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <github@michaelstrong.co.uk>
 *
 **/
class BlogFilter extends Lumberjack {

	/**
	 * Augments (@link Hierarchy::stageChildren()}
	 *
	 * @param $staged DataList
	 * @param $showAll boolean
	**/
	public function stageChildren($showAll = false) {
		$staged = parent::stageChildren($showAll);

		if(!$this->shouldFilter()
			&& in_array(get_class($this->owner), ClassInfo::subClassesFor("Blog"))
			&& !Permission::check("VIEW_DRAFT_CONTENT")
		) {

			// Get the current stage.
			$stage = Versioned::current_stage();
			if($stage == "Stage") $stage = "";
			else $stage = "_" . Convert::raw2sql($stage);

			// Filter published posts
			$dataQuery = $staged->dataQuery()
				->innerJoin("BlogPost", '"BlogPost' . $stage . '"."ID" = "SiteTree' . $stage . '"."ID"')
				->where('"PublishDate" < \'' . Convert::raw2sql(SS_Datetime::now()) . '\'');
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

		if(!$this->shouldFilter()
			&& in_array(get_class($this->owner), ClassInfo::subClassesFor("Blog"))
			&& !Permission::check("VIEW_DRAFT_CONTENT")
		) {
			// Filter publish posts
			$dataQuery = $staged->dataQuery()
				->innerJoin("BlogPost", '"BlogPost_Live"."ID" = "SiteTree"_"Live"."ID"')
				->where('"PublishDate" < \'' . Convert::raw2sql(SS_Datetime::now()) . '\'');
			$staged = $staged->setDataQuery($dataQuery);
		}
		return $staged;
	}

}