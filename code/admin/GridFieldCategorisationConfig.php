<?php

class GridFieldCategorisationConfig extends GridFieldConfig_RecordEditor
{
    /**
     * @param int $itemsPerPage
     * @param array|SS_List $mergeRecords
     * @param string $parentType
     * @param string $parentMethod
     * @param string $childMethod
     */
    public function __construct($itemsPerPage = 15, $mergeRecords, $parentType, $parentMethod, $childMethod)
    {
        parent::__construct($itemsPerPage);

        $this->removeComponentsByType('GridFieldAddNewButton');

        $this->addComponent(
            new GridFieldAddByDBField('buttons-before-left')
        );

        $this->addComponent(
            new GridFieldMergeAction($mergeRecords, $parentType, $parentMethod, $childMethod)
        );

        /**
         * @var GridFieldDataColumns $columns
         */
        $columns = $this->getComponentByType('GridFieldDataColumns');

        $columns->setFieldFormatting(array(
            'BlogPostsCount' => function ($value, CategorisationObject $item) {
                return $item->BlogPosts()->Count();
            }
        ));

        $this->changeColumnOrder();
    }

    /**
     * Reorders GridField columns so that Actions is last.
     */
    protected function changeColumnOrder()
    {
        /**
         * @var GridFieldDataColumns $columns
         */
        $columns = $this->getComponentByType('GridFieldDataColumns');

        $columns->setDisplayFields(array(
            'Title' => 'Title',
            'BlogPostsCount' => 'Posts',
            'MergeAction' => 'MergeAction',
            'Actions' => 'Actions',
        ));
    }
}
