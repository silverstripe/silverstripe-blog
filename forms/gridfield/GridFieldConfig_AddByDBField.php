<?php

class GridFieldConfig_AddByDBField extends GridFieldConfig_RecordEditor {
	
	public function __construct($itemsPerPage = null, $dataObjectField = "Title") {
		parent::__construct($itemsPerPage);

		// Remove uneccesary components
		$this->removeComponentsByType("GridFieldAddNewButton");

		// Add new components
		$this->addComponent(new GridFieldAddByDBField("buttons-before-left", $dataObjectField));
	}

}