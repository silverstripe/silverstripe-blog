<?php

namespace SilverStripe\Blog\Admin;

use SilverStripe\Blog\Forms\GridField\GridFieldAddByDBField;
use SilverStripe\Blog\Model\CategorisationObject;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\ORM\SS_List;

class GridFieldCategorisationConfig extends GridFieldConfig_RecordEditor
{
    /**
     * @param int           $itemsPerPage
     * @param array|SS_List $mergeRecords
     * @param string        $parentType
     * @param string        $parentMethod
     * @param string        $childMethod
     */
    public function __construct($itemsPerPage, $mergeRecords, $parentType, $parentMethod, $childMethod)
    {
        parent::__construct($itemsPerPage);

        $this->removeComponentsByType(GridFieldAddNewButton::class);

        $this->addComponent(
            GridFieldAddByDBField::create('buttons-before-left')
        );

        $this->addComponent(
            GridFieldMergeAction::create($mergeRecords, $parentType, $parentMethod, $childMethod)
        );

        /**
         * @var GridFieldDataColumns $columns
         */
        $columns = $this->getComponentByType(GridFieldDataColumns::class);

        $columns->setFieldFormatting(
            [
                'BlogPostsCount'    => function ($value, CategorisationObject $item) {
                    return $item->getBlogCount();
                },
                'BlogPostsAllCount' => function ($value, CategorisationObject $item) {
                    return $item->BlogPosts()->Count();
                },
            ]
        );
        $columns->setDisplayFields(
            [
                'Title'             => _t(__CLASS__ . '.Title', 'Title'),
                'BlogPostsCount'    => _t(__CLASS__ . '.PostsThisBlog', 'Posts (This Blog)'),
                'BlogPostsAllCount' => _t(__CLASS__ . '.PostsAllBlogs', 'Posts (All Blogs)'),
                'MergeAction'       => 'MergeAction',
                'Actions'           => 'Actions'
            ]
        );
    }
}
