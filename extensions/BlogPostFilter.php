<?php

class BlogPostFilter extends DataExtension {

	/**
	 * Augment queries so that we don't fetch unpublished articles.
	**/
	public function augmentSQL(SQLQuery &$query) {

		if(!Permission::check("VIEW_DRAFT_CONTENT")) {
			$stage = Versioned::current_stage();
			if($stage == "Stage") $stage = "";
			else $stage = "_" . Convert::raw2sql($stage);

			$query->addWhere("PublishDate < NOW()");
		}

	}

	/**
	 * This is a fix so that when we try to fetch subclasses of BlogPost,
	 * lazy loading includes the BlogPost table in its query. Leaving this table out
	 * means the default sort order column PublishDate causes an error.
	**/
	public function augmentLoadLazyFields(SQLQuery &$query, &$dataQuery, $parent) {

		// Ensures that we're joining the BlogPost table which holds required db fields.
		$dataQuery->innerJoin("BlogPost", "`SiteTree`.`ID` = `BlogPost`.`ID`");

	}

}