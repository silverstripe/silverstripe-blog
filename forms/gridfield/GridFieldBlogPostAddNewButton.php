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
 *
 * @author Michael Strong <github@michaelstrong.co.uk>
 */
class GridFieldBlogPostAddNewButton extends GridFieldAddNewButton 
	implements GridField_ActionProvider {

	public function getHTMLFragments($gridField) {
		$singleton = singleton($gridField->getModelClass());

		if(!$singleton->canCreate()) {
			return array();
		}

		$parent = SiteTree::get()->byId(Controller::curr()->currentPageID());

		if(!$this->buttonName) {
			// provide a default button name, can be changed by calling {@link setButtonName()} on this component
			$objectName = $singleton->i18n_singular_name();
			$this->buttonName = _t('GridFieldSiteTreeAddNewButton.Add', 'Add {name}', "Add button text", array('name' => $singleton->i18n_singular_name()));
		}

		$state = $gridField->State->GridFieldSiteTreeAddNewButton;
		$state->currentPageID = $parent->ID;
		$state->pageType = $parent->defaultChild();

		$addAction = new GridField_FormAction($gridField, 
			'add',
			$this->buttonName, 
			'add', 
			'add'
		);
		$addAction->setAttribute('data-icon', 'add')->addExtraClass("no-ajax");

		$allowedChildren = $parent->allowedChildren();
		$children = array();
		foreach($allowedChildren as $class) {
			$children[$class] = singleton($class)->i18n_singular_name();
		}

		$pageTypes = DropdownField::create(
			"PageType", 
			"Page Type",
			$children,
			$singleton->defaultChild()
		);
		$pageTypes->setFieldHolderTemplate("BlogDropdownField_holder")
			->addExtraClass("gridfield-dropdown");

		$forTemplate = new ArrayData(array());
		$forTemplate->Fields = new ArrayList();
		$forTemplate->Fields->push($pageTypes);
		$forTemplate->Fields->push($addAction);

		Requirements::css(blog_dir() . "/css/cms.css");
		Requirements::javascript(blog_dir() . "/javascript/GridField.js");

		return array(
			$this->targetFragment => $forTemplate->renderWith("GridFieldSiteTreeAddNewButton")
		);
	}



	/**
	 * Provide actions to this component.
	 *
	 * @param $gridField GridField
	 *
	 * @return array
	**/
	public function getActions($gridField) {
		return array("add");
	}



	/**
	 * Handles the add action, but only acts as a wrapper for {@link CMSPageAddController::doAdd()}
	 *
	 * @param $gridFIeld GridFIeld
	 * @param $actionName string
	 * @param $arguments mixed
	 * @param $data array
	**/
	public function handleAction(GridField $gridField, $actionName, $arguments, $data) {
		if($actionName == "add") {
			$tmpData = json_decode($data['BlogPost']['GridState'], true);
			$tmpData = $tmpData['GridFieldSiteTreeAddNewButton'];
			
			$data = array(
				"ParentID" => $tmpData['currentPageID'],
				"PageType" => $tmpData['pageType']
			);

			$controller = Injector::inst()->create("CMSPageAddController");

			$form = $controller->AddForm();
			$form->loadDataFrom($data);

			$controller->doAdd($data, $form);
			$response = $controller->getResponseNegotiator()->getResponse();

			// Get the current record
			$record = SiteTree::get()->byId($controller->currentPageID());
			if($record) {
				$response->redirect(Director::absoluteBaseURL() . $record->CMSEditLink(), 301);
			}
			return $response;

		}
	}

}