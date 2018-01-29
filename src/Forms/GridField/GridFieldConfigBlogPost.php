<?php

namespace SilverStripe\Blog\Forms\GridField;

use SilverStripe\Core\Injector\Injector;
use SilverStripe\Lumberjack\Forms\GridFieldConfig_Lumberjack;
use SilverStripe\Lumberjack\Forms\GridFieldSiteTreeState;

/**
 * GridField config necessary for managing a SiteTree object.
 *
 */
class GridFieldConfigBlogPost extends GridFieldConfig_Lumberjack
{
    /**
     * @param null|int $itemsPerPage
     */
    public function __construct($itemsPerPage = null)
    {
        parent::__construct($itemsPerPage);

        $this->removeComponentsByType(GridFieldSiteTreeState::class);
        $this->addComponent(Injector::inst()->create(GridFieldBlogPostState::class));
    }
}
