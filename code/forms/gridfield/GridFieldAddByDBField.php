<?php

/**
 * Adds a component which allows a user to add a new DataObject by database field.
 *
 * @package silverstripe
 * @subpackage blog
 */
class GridFieldAddByDBField implements GridField_ActionProvider, GridField_HTMLProvider
{
    /**
     * HTML Fragment to render the field.
     *
     * @var string
     */
    protected $targetFragment;

    /**
     * Default field to create the DataObject by should be Title.
     *
     * @var string
     */
    protected $dataObjectField = 'Title';

    /**
     * Creates a text field and add button which allows the user to directly create a new
     * DataObject by just entering the title.
     *
     * @param string $targetFragment
     * @param string $dataObjectField
     */
    public function __construct($targetFragment = 'before', $dataObjectField = 'Title')
    {
        $this->targetFragment = $targetFragment;
        $this->dataObjectField = (string) $dataObjectField;
    }

    /**
     * Provide actions to this component.
     *
     * @param GridField $gridField
     *
     * @return array
     */
    public function getActions($gridField)
    {
        return array(
            'add',
        );
    }

    /**
     * Handles the add action for the given DataObject.
     *
     * @param $gridField GridField
     * @param $actionName string
     * @param $arguments mixed
     * @param $data array
     *
     * @return null|SS_HTTPResponse
     *
     * @throws UnexpectedValueException
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName == 'add') {
            $dbField = $this->getDataObjectField();

            $objClass = $gridField->getModelClass();

            /**
             * @var DataObject $obj
             */
            $obj = new $objClass();

            if ($obj->hasField($dbField)) {
                $obj->setCastedField($dbField, $data['gridfieldaddbydbfield'][$obj->ClassName][$dbField]);

                if ($obj->canCreate()) {
                    $id = $gridField->getList()->add($obj);
                    if (!$id) {
                        $gridField->setError(
                            _t(
                                'GridFieldAddByDBField.AddFail',
                                'Unable to save {class} to the database.',
                                'Unable to add the DataObject.',
                                array(
                                    'class' => get_class($obj),
                                )
                            ),
                            'error'
                        );
                    }
                } else {
                    return Security::permissionFailure(
                        Controller::curr(),
                        _t(
                            'GridFieldAddByDBField.PermissionFail',
                            'You don\'t have permission to create a {class}.',
                            'Unable to add the DataObject.',
                            array(
                                'class' => get_class($obj)
                            )
                        )
                    );
                }
            } else {
                throw new UnexpectedValueException(
                    sprintf(
                        'Invalid field (%s) on %s.',
                        $dbField,
                        $obj->ClassName
                    )
                );
            }
        }

        return null;
    }

    /**
     * Returns the database field for which we'll add the new data object.
     *
     * @return string
     */
    public function getDataObjectField()
    {
        return $this->dataObjectField;
    }

    /**
     * Set the database field.
     *
     * @param $field string
     */
    public function setDataObjectField($field)
    {
        $this->dataObjectField = (string) $field;
    }

    /**
     * Renders the TextField and add button to the GridField.
     *
     * @param $gridField GridField
     *
     * @return string
     */
    public function getHTMLFragments($gridField)
    {
        Requirements::javascript(BLOGGER_DIR . '/js/gridfieldaddbydbfield.js');

        /**
         * @var DataList $dataList
         */
        $dataList = $gridField->getList();

        $dataClass = $dataList->dataClass();

        $obj = singleton($dataClass);

        if (!$obj->canCreate()) {
            return "";
        }

        $dbField = $this->getDataObjectField();

        $textField = TextField::create(
            sprintf(
                "gridfieldaddbydbfield[%s][%s]",
                $obj->ClassName,
                Convert::raw2htmlatt($dbField)
            )
        )
            ->setAttribute('placeholder', $obj->fieldLabel($dbField))
            ->addExtraClass('no-change-track');

        $addAction = new GridField_FormAction(
            $gridField,
            'add',
            _t('GridFieldAddByDBField.Add',
                'Add {name}', "Add button text",
                array(
                    'name' => $obj->i18n_singular_name(),
                )
            ),
            'add',
            'add'
        );
        $addAction->setAttribute('data-icon', 'add');

        $forTemplate = new ArrayData(array());

        $forTemplate->Fields = new ArrayList();
        $forTemplate->Fields->push($textField);
        $forTemplate->Fields->push($addAction);

        return array(
            $this->targetFragment => $forTemplate->renderWith('GridFieldAddByDBField')
        );
    }
}
