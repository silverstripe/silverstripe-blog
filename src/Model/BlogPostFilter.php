<?php

namespace SilverStripe\Blog\Model;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DataQuery;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\Queries\SQLSelect;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\Versioned;

/**
 * This is responsible for filtering only published posts to users who do not have permission to
 * view non-published posts.
 *
 */
class BlogPostFilter extends DataExtension
{
    /**
     * Augment queries so that we don't fetch unpublished articles.
     *
     * @param SQLSelect $query
     * @param DataQuery $query
     */
    public function augmentSQL(SQLSelect $query, DataQuery $dataQuery = null)
    {
        $stage = Versioned::get_stage();

        if (Controller::has_curr() && Controller::curr() instanceof LeftAndMain) {
            return;
        }

        if ($stage == 'Live' || !Permission::check('VIEW_DRAFT_CONTENT')) {
            $query->addWhere(sprintf(
                '"PublishDate" < \'%s\'',
                Convert::raw2sql(DBDatetime::now())
            ));
        }
    }

    /**
     * {@inheritDoc}
     *
     * This is a fix so that when we try to fetch subclasses of BlogPost, lazy loading includes the
     * BlogPost table in its query. Leaving this table out means the default sort order column
     * PublishDate causes an error.
     *
     * @see https://github.com/silverstripe/silverstripe-framework/issues/1682
     *
     * @param SQLSelect $query
     * @param DataQuery $dataQuery
     * @param DataObject $dataObject
     */
    public function augmentLoadLazyFields(SQLSelect &$query, DataQuery &$dataQuery = null, $dataObject)
    {
        $dataQuery->innerJoin(
            DataObject::getSchema()->tableName(BlogPost::class),
            '"SiteTree"."ID" = "BlogPost"."ID"'
        );
    }
}
