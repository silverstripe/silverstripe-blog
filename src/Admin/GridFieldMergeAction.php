<?php

namespace SilverStripe\Blog\Admin;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Injector\Injectable;
use Silverstripe\Forms\DropdownField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\GridField_ColumnProvider;
use SilverStripe\ORM\SS_List;

class GridFieldMergeAction implements GridField_ColumnProvider, GridField_ActionProvider
{
    use Injectable;

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
    public function __construct($records, $parentType, $parentMethod, $childMethod)
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
        return ['MergeAction'];
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        if ($columnName === 'MergeAction' && $record->{$this->childMethod}()->Count() > 0) {
            $dropdown = DropdownField::create('Target', 'Target', $this->records->exclude('ID', $record->ID)->map());
            $dropdown->setAttribute('id', 'Target_'.$record->ID);
            $prefix = strtolower($this->parentMethod . '-' . $this->childMethod);

            $action = GridFieldFormAction::create(
                $gridField,
                'MergeAction' . $record->ID,
                'Move',
                'merge',
                [
                    'record' => $record->ID,
                    'target' => $prefix . '-target-record-' . $record->ID,
                ]
            );

            $action->setExtraAttributes([
                'data-target' => $prefix . '-target-record-' . $record->ID
            ]);

            $action->addExtraClass('btn btn-primary btn-sm blog-merge-action');
            $MovePostsTo = _t(__CLASS__ . '.MovePostsTo', 'Move posts to');
            $MergeActionReveal = '<a title="' . $MovePostsTo . '" class="MergeActionReveal">' . $MovePostsTo . '</a>';

            return $dropdown->Field() . $action->Field() . $MergeActionReveal;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return ['class' => 'MergeAction'];
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnMetadata($gridField, $columnName)
    {
        return ['title' => _t(__CLASS__ . '.MovePostsTo', 'Move posts to')];
    }

    /**
     * {@inheritdoc}
     */
    public function getActions($gridField)
    {
        return ['merge'];
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
