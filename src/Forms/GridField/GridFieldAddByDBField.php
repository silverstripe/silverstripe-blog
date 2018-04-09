<?php

namespace SilverStripe\Blog\Forms\GridField;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Security;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use UnexpectedValueException;

class GridFieldAddByDBField implements GridField_ActionProvider, GridField_HTMLProvider
{
    use Injectable;

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
        return [
            'add',
        ];
    }

    /**
     * Handles the add action for the given DataObject.
     *
     * @param $gridField GridField
     * @param $actionName string
     * @param $arguments mixed
     * @param $data array
     *
     * @return null|HTTPResponse
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
            $obj = $objClass::create();

            if ($obj->hasField($dbField)) {
                $obj->setCastedField($dbField, $data['gridfieldaddbydbfield'][$obj->ClassName][$dbField]);

                if ($obj->canCreate()) {
                    $id = $gridField->getList()->add($obj);
                    if (!$id) {
                        $gridField->setCustomValidationMessage(
                            _t(
                                __CLASS__ . '.AddFail',
                                'Unable to save {class} to the database.',
                                'Unable to add the DataObject.',
                                [
                                    'class' => get_class($obj),
                                ]
                            )
                        );
                    }
                } else {
                    return Security::permissionFailure(
                        Controller::curr(),
                        _t(
                            __CLASS__ . '.PermissionFail',
                            'You don\'t have permission to create a {class}.',
                            'Unable to add the DataObject.',
                            [
                                'class' => get_class($obj)
                            ]
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
     * @return string[]
     */
    public function getHTMLFragments($gridField)
    {
        Requirements::javascript('silverstripe/blog:client/dist/js/main.bundle.js');

        /**
         * @var DataList $dataList
         */
        $dataList = $gridField->getList();

        $dataClass = $dataList->dataClass();

        $obj = singleton($dataClass);

        if (!$obj->canCreate()) {
            return [];
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

        $addAction = GridField_FormAction::create(
            $gridField,
            'add',
            _t(
                __CLASS__ . '.Add',
                'Add {name}',
                'Add button text',
                ['name' => $obj->i18n_singular_name()]
            ),
            'add',
            'add'
        );
        $addAction->setAttribute('data-icon', 'add');
        $addAction->addExtraClass('btn btn-primary');

        $forTemplate = ArrayData::create([]);

        $forTemplate->Fields = ArrayList::create();
        $forTemplate->Fields->push($textField);
        $forTemplate->Fields->push($addAction);

        return [$this->targetFragment => $forTemplate->renderWith(self::class)];
    }
}
