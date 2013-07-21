<?php
/**
 * This component provides a button for opening the add new form provided by 
 * {@link GridFieldDetailForm}.
 *
 * Only returns a button if {@link DataObject->canCreate()} for this record 
 * returns true.
 *
 * @package framework
 * @subpackage fields-gridfield
 */
class GridFieldSiteTreeAddNewButton extends GridFieldAddNewButton {

	public function getHTMLFragments($gridField) {
		$singleton = singleton($gridField->getModelClass());

		if(!$singleton->canCreate()) {
			return array();
		}

		if(!$this->buttonName) {
			// provide a default button name, can be changed by calling {@link setButtonName()} on this component
			$objectName = $singleton->i18n_singular_name();
			$this->buttonName = _t('GridField.Add', 'Add {name}', array('name' => $objectName));
		}

		$controller = $gridField->getForm()->getController();
		$data = new ArrayData(array(
			'NewLink' => $controller->LinkPageAdd("?ParentID=" . $controller->currentPageID()),
			'ButtonName' => $this->buttonName,
		));

		return array(
			$this->targetFragment => $data->renderWith('GridFieldAddNewbutton'),
		);
	}

}