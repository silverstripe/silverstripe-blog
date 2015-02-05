<?php

/**
 * GridField config necessary for managing a SiteTree object.
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <mstrong@silverstripe.org>
**/
class GridFieldConfig_BlogPost extends GridFieldConfig_Lumberjack {
	
	public function __construct($itemsPerPage = null) {
		parent::__construct($itemsPerPage);
		$this->removeComponentsByType('GridFieldSiteTreeState');
		$this->addComponent(new GridFieldBlogPostState());
	}

}