<?php

/**
 * GirdField config necessary for managing a SiteTree object.
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael String <micmania@hotmail.co.uk>
**/
class GridFieldConfig_SiteTree extends GridFieldConfig {
	
	public function __construct($itemsPerPage = null) {
		parent::__construct($itemsPerPage);
		$this->addComponent(new GridFieldButtonRow('before'));
		$this->addComponent(new GridFieldSiteTreeAddNewButton('buttons-before-left'));
		$this->addComponent(new GridFieldToolbarHeader());
		$this->addComponent($sort = new GridFieldSortableHeader());
		$this->addComponent($filter = new GridFieldFilterHeader());
		$this->addComponent(new GridFieldDataColumns());
		$this->addComponent(new GridFieldSiteTreeEditButton());
		$this->addComponent(new GridFieldPageCount('toolbar-header-right'));
		$this->addComponent($pagination = new GridFieldPaginator($itemsPerPage));

		$pagination->setThrowExceptionOnBadDataType(false);
	}
}