<?php

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
                ->where(sprintf('"PublishDate" < \'%s\'', Convert::raw2sql(SS_Datetime::now())));

            $staged = $staged->setDataQuery($dataQuery);
        }

        return $staged;
    }

    /**
     * @return bool
     */
    protected function subclassForBlog()
    {
        return in_array(get_class($this->owner), ClassInfo::subClassesFor('Blog'));
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
                ->where(sprintf('"PublishDate" < \'%s\'', Convert::raw2sql(SS_Datetime::now())));

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

            $gridField = new BlogFilter_GridField(
                'ChildPages',
                $this->getLumberjackTitle(),
                $pages,
                $this->getLumberjackGridFieldConfig()
            );

            $tab = new Tab('ChildPages', $this->getLumberjackTitle(), $gridField);

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
