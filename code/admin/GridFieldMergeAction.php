<?php

class GridFieldMergeAction implements GridField_ColumnProvider, GridField_ActionProvider
{

    /**
     * Cache results of records queries. This is useful for blogs
     * with a lot of tags or categories.
     *
     * @var array
     *
     * @example
     *  array(1) {
     *      ["2d73dd8f18b8da3c8e5e75b6bee66d8d"]=> array(3) {
     *          [2]=> string(5) "tag2"
     *          [1]=> string(6) "tag1"
     *          [3]=> string(4) "tag3"
     *      }
     *  }
     *  The array key in the first level is made up of an md5 hash of the sql query,
     *  then the second level is made if up ID => tag name.
     */
    protected static $cached_results = array();

    /**
     * List of records to show in the MergeAction column.
     *
     * @var array|SS_List
     */
    protected $records;

    /**
     * Type of parent DataObject (i.e BlogTag, BlogCategory).
     *
     * @var string
     */
    protected $parentType;

    /**
     * Relationship method to reference parent (i.e BlogTags).
     *
     * @var string
     */
    protected $parentMethod;

    /**
     * Relationship method to reference child (i.e BlogPosts).
     *
     * @var string
     */
    protected $childMethod;

    /**
     * @param array|SS_List $records
     * @param string $parentType
     * @param string $parentMethod
     * @param string $childMethod
     */
    public function __construct($records = array(), $parentType, $parentMethod, $childMethod)
    {
        $this->records = $records;
        $this->parentType = $parentType;
        $this->parentMethod = $parentMethod;
        $this->childMethod = $childMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function augmentColumns($gridField, &$columns)
    {
        if (!in_array('MergeAction', $columns)) {
            $columns[] = 'MergeAction';
        }

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnsHandled($gridField)
    {
        return array('MergeAction');
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        if ($columnName === 'MergeAction') {

            if(!$record->hasMethod($this->childMethod) || $record->{$this->childMethod}()->count() < 1) {
                return '';
            }

            $children = $record->{$this->childMethod}();
            $cacheKey = md5($children->dataQuery()->sql());
            if(isset(self::$cached_results[$cacheKey])) {
                $data = self::$cached_results[$cacheKey];
            } else {
                $data = $this->records->map()->toArray();
                self::$cached_results[$cacheKey] = $data;
            }

            // Unset the current record
            if(isset($data[$record->ID])) {
                unset($data[$record->ID]);
            }

            if(count($data) > 0) {
                $dropdown = new DropdownField('Target', 'Target', $data);
                $dropdown->setAttribute('id', 'Target_'.$record->ID);
                $prefix = strtolower($this->parentMethod . '-' . $this->childMethod);

                $action = GridFieldFormAction::create(
                    $gridField,
                    'MergeAction' . $record->ID,
                    'Move',
                    'merge',
                    array(
                        'record' => $record->ID,
                        'target' => $prefix . '-target-record-' . $record->ID,
                    )
                );

                $action->setExtraAttributes(array(
                    'data-target' => $prefix . '-target-record-' . $record->ID
                ));

                return $dropdown->Field() . $action->Field() . '<a title="Move posts to" class="MergeActionReveal">move posts to</a>';
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return array('class' => 'MergeAction');
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnMetadata($gridField, $columnName)
    {
        return array('title' => 'Move posts to');
    }

    /**
     * {@inheritdoc}
     */
    public function getActions($gridField)
    {
        return array('merge');
    }

    /**
     * {@inheritdoc}
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName === 'merge') {
            $controller = Controller::curr();

            $request = $controller->getRequest();

            $target = $request->requestVar($arguments["target"]);

            $parentType = $this->parentType;

            $fromParent = $parentType::get()->byId($arguments['record']);
            $toParent = $parentType::get()->byId($target);

            $posts = $fromParent->{$this->childMethod}();

            foreach ($posts as $post) {
                $relationship = $post->{$this->parentMethod}();

                $relationship->remove($fromParent);
                $relationship->add($toParent);
            }
        }
    }
}
