<?php

/**
 * GridField config necessary for managing a SiteTree object.
 *
 * @package silverstripe
 * @subpackage blog
 */
class GridFieldConfig_BlogPost extends GridFieldConfig_Lumberjack
{
    /**
     * @param null|int $itemsPerPage
     */
    public function __construct($itemsPerPage = null)
    {
        parent::__construct($itemsPerPage);

        $this->removeComponentsByType('GridFieldSiteTreeState');
        $this->addComponent(new GridFieldBlogPostState());
    }
}
