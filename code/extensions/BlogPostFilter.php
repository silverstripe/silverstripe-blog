<?php

use SilverStripe\ORM\Versioning\Versioned;
use SilverStripe\Control\Controller;
use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Security\Permission;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\DataExtension;

/**
 * This is responsible for filtering only published posts to users who do not have permission to
 * view non-published posts.
 *
 * @package silverstripe
 * @subpackage blog
 */
class BlogPostFilter extends DataExtension
{
    /**
     * Augment queries so that we don't fetch unpublished articles.
     *
     * @param SQLQuery $query
     */
    public function augmentSQL(SQLQuery &$query)
    {
        $stage = Versioned::current_stage();

        if (Controller::curr() instanceof LeftAndMain) {
            return;
        }

        if ($stage == 'Live' || !Permission::check('VIEW_DRAFT_CONTENT')) {
            $query->addWhere(sprintf('"PublishDate" < \'%s\'', Convert::raw2sql(DBDatetime::now())));
        }
    }

    /**
     * This is a fix so that when we try to fetch subclasses of BlogPost, lazy loading includes the
     * BlogPost table in its query. Leaving this table out means the default sort order column
     * PublishDate causes an error.
     *
     * @see https://github.com/silverstripe/silverstripe-framework/issues/1682
     *
     * @param SQLQuery $query
     * @param mixed $dataQuery
     * @param mixed $parent
     */
    public function augmentLoadLazyFields(SQLQuery &$query, &$dataQuery, $parent)
    {
        $dataQuery->innerJoin('BlogPost', '"SiteTree"."ID" = "BlogPost"."ID"');
    }
}
