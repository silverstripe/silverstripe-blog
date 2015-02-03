<?php

/**
 * GridField config necessary for managing a SiteTree object.
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <github@michaelstrong.co.uk>
**/
class GridFieldConfig_BlogPost extends GridFieldConfig_Lumberjack {
	
	public function __construct($itemsPerPage = null) {
		parent::__construct($itemsPerPage);
		$this->removeComponentsByType('SiteTreeEditButton');
		$this->addComponent(new GridFieldSiteTreeEditButton());
	}
}