<?php

namespace SilverStripe\Blog\Model;

use SilverStripe\Blog\Model\BlogFilter\BlogFilterGridField;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Tab;
use SilverStripe\Lumberjack\Model\Lumberjack;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\Versioned;

/**
 * This class is responsible for filtering the SiteTree when necessary and also overlaps into
 * filtering only published posts.
 *
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
                ->innerJoin(
                    DataObject::getSchema()->tableName(BlogPost::class),
                    sprintf('"BlogPost%s"."ID" = "SiteTree%s"."ID"', $stage, $stage)
                )
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
        return in_array(get_class($this->owner), ClassInfo::subclassesFor(Blog::class));
    }

    /**
     * {@inheritdoc}
     */
    public function liveChildren($showAll = false, $onlyDeletedFromStage = false)
    {
        $staged = parent::liveChildren($showAll, $onlyDeletedFromStage);

        if (!$this->shouldFilter() && $this->isBlog() && !Permission::check('VIEW_DRAFT_CONTENT')) {
            $dataQuery = $staged->dataQuery()
                ->innerJoin(
                    DataObject::getSchema()->tableName(BlogPost::class),
                    '"BlogPost_Live"."ID" = "SiteTree_Live"."ID"'
                )
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
            $pages = BlogPost::get()->filter([
                'ParentID' => $this->owner->ID,
                'ClassName' => $excluded
            ]);

            $gridField = BlogFilterGridField::create(
                'ChildPages',
                $this->getLumberjackTitle(),
                $pages,
                $this->getLumberjackGridFieldConfig()
            );

            $tab = Tab::create('ChildPages', $this->getLumberjackTitle(), $gridField);

            $fields->insertBefore('Main', $tab);
        }
    }
}
