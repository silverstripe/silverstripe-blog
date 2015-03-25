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
			elseif($stage) $stage = "_{$stage}";

			// Filter published posts
			$dataQuery = $staged->dataQuery()
				->innerJoin("BlogPost", '"BlogPost' . $stage . '"."ID" = "SiteTree' . $stage . '"."ID"')
				->where('"PublishDate" < \'' . Convert::raw2sql(SS_Datetime::now()) . '\'');
			$staged = $staged->setDataQuery($dataQuery);
		}
		return $staged;
	}

	public function liveChildren($showAll = false, $onlyDeletedFromStage = false) {
		$staged = parent::liveChildren($showAll, $onlyDeletedFromStage);

		if(!$this->shouldFilter()
			&& $this->owner instanceof Blog
			&& !Permission::check("VIEW_DRAFT_CONTENT")
		) {
			// Filter publish posts
			$dataQuery = $staged->dataQuery()
				->innerJoin("BlogPost", '"BlogPost_Live"."ID" = "SiteTree_Live"."ID"')
				->where('"BlogPost"."PublishDate" < \'' . Convert::raw2sql(SS_Datetime::now()->getValue()) . '\'');
			$staged = $staged->setDataQuery($dataQuery);
		}
		return $staged;
	}

	public function updateCMSFields(FieldList $fields) {
		$excluded = $this->owner->getExcludedSiteTreeClassNames();
		if(!empty($excluded)) {
			$pages = SiteTree::get()->filter(array(
				'ParentID' => $this->owner->ID,
				'ClassName' => $excluded
			))->sort('"SiteTree"."Created" DESC');
			$gridField = new BlogFilter_GridField(
				"ChildPages",
				$this->getLumberjackTitle(),
				$pages,
				$this->getLumberjackGridFieldConfig()
			);

			$tab = new Tab('ChildPages', $this->getLumberjackTitle(), $gridField);
			$fields->insertAfter($tab, 'Main');
		}
	}

}


/**
 * Enables children of non-editable pages to be edited
 */
class BlogFilter_GridField extends GridField {
	public function transform(FormTransformation $trans) {
		// Don't allow parent object transformations to propegate automatically to child records
		return $this;
	}
}
