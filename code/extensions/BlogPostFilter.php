<?php

/**
 * This is responsible for filtering only published posts to users who do not have
 * permission to view non-published posts.
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <github@michaelstrong.co.uk>
**/
class BlogPostFilter extends DataExtension {

	/**
	 * Augment queries so that we don't fetch unpublished articles.
	**/
	public function augmentSQL(SQLQuery &$query) {
		$stage = Versioned::current_stage();
		if($stage == 'Live' || !Permission::check("VIEW_DRAFT_CONTENT")) {
			$query->addWhere("PublishDate < '" . Convert::raw2sql(SS_Datetime::now()) . "'");
		}
	}

	/**
	 * This is a fix so that when we try to fetch subclasses of BlogPost,
	 * lazy loading includes the BlogPost table in its query. Leaving this table out
	 * means the default sort order column PublishDate causes an error.
	 *
	 * @see https://github.com/silverstripe/silverstripe-framework/issues/1682
	**/
	public function augmentLoadLazyFields(SQLQuery &$query, &$dataQuery, $parent) {
		// Ensures that we're joining the BlogPost table which holds required db fields.
		$dataQuery->innerJoin("BlogPost", "`SiteTree`.`ID` = `BlogPost`.`ID`");
	}

}