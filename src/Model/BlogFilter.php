<?php

namespace SilverStripe\Blog\Model;

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormTransformation;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\Tab;
use SilverStripe\Lumberjack\Model\Lumberjack;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\Versioning\Versioned;
use SilverStripe\Security\Permission;

/**
 * This class is responsible for filtering the SiteTree when necessary and also overlaps into
 * filtering only published posts.
 *
 * @package silverstripe
 * @subpackage blog
 */
class BlogFilter extends Lumberjack
{
    /**
     * {@inheritdoc}
     */
    public function stageChildren($showAll = false)
    {
        $staged = parent::stageChildren($showAll);

        if (!$this->shouldFilter() && $this->subclassForBlog() && !Permission::check('VIEW_DRAFT_CONTENT')) {
            $stage = Versioned::get_stage();

            if ($stage == 'Stage') {
                $stage = '';
            } elseif ($stage) {
                $stage = '_' . $stage;
            }

            $dataQuery = $staged->dataQuery()
                ->innerJoin('BlogPost', sprintf('"BlogPost%s"."ID" = "SiteTree%s"."ID"', $stage, $stage))
                ->where(sprintf('"PublishDate" < \'%s\'', Convert::raw2sql(DBDatetime::now())));

            $staged = $staged->setDataQuery($dataQuery);
        }

        return $staged;
    }

    /**
     * @return bool
     */
    protected function subclassForBlog()
    {
        return in_array(get_class($this->owner), ClassInfo::subclassesFor('Blog'));
    }

    /**
     * {@inheritdoc}
     */
    public function liveChildren($showAll = false, $onlyDeletedFromStage = false)
    {
        $staged = parent::liveChildren($showAll, $onlyDeletedFromStage);

        if (!$this->shouldFilter() && $this->isBlog() && !Permission::check('VIEW_DRAFT_CONTENT')) {
            $dataQuery = $staged->dataQuery()
                ->innerJoin('BlogPost', '"BlogPost_Live"."ID" = "SiteTree_Live"."ID"')
                ->where(sprintf('"PublishDate" < \'%s\'', Convert::raw2sql(DBDatetime::now())));

            $staged = $staged->setDataQuery($dataQuery);
        }

        return $staged;
    }

    /**
     * @return bool
     */
    protected function isBlog()
    {
        return $this->owner instanceof Blog;
    }

    /**
     * {@inheritdoc}
     */
    public function updateCMSFields(FieldList $fields)
    {
        $excluded = $this->owner->getExcludedSiteTreeClassNames();

        if (!empty($excluded)) {
            $pages = BlogPost::get()->filter(array(
                'ParentID' => $this->owner->ID,
                'ClassName' => $excluded
            ));

            $gridField = BlogFilter_GridField::create(
                'ChildPages',
                $this->getLumberjackTitle(),
                $pages,
                $this->getLumberjackGridFieldConfig()
            );

            $tab = Tab::create('ChildPages', $this->getLumberjackTitle(), $gridField);

            $fields->insertBefore($tab, 'Main');
        }
    }
}


/**
 * Enables children of non-editable pages to be edited.
 */
class BlogFilter_GridField extends GridField
{
    /**
     * @param FormTransformation $transformation
     *
     * @return $this
     */
    public function transform(FormTransformation $transformation)
    {
        return $this;
    }
}
