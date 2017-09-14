<?php

namespace SilverStripe\Blog\Forms\GridField;

use SilverStripe\Lumberjack\Forms\GridFieldConfig_Lumberjack;
use SilverStripe\Lumberjack\Forms\GridFieldSiteTreeState;

/**
 * GridField config necessary for managing a SiteTree object.
 *
 */
class GridFieldConfig_BlogPost extends GridFieldConfig_Lumberjack
{
    /**
     * @param null|int $itemsPerPage
     */
    public function __construct($itemsPerPage = null)
    {
        parent::__construct($itemsPerPage);

        $this->removeComponentsByType(GridFieldSiteTreeState::class);
        $this->addComponent(GridFieldBlogPostState::create());
    }
}
