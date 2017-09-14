<?php

namespace SilverStripe\Blog\Admin;

use SilverStripe\Forms\GridField\GridField_FormAction;

class GridFieldFormAction extends GridField_FormAction
{
    /**
     * @var array
     */
    protected $extraAttributes = [];

    /**
     * @param array $attributes
     */
    public function setExtraAttributes(array $attributes)
    {
        $this->extraAttributes = $attributes;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $attributes = parent::getAttributes();

        return array_merge(
            $attributes,
            $this->extraAttributes
        );
    }
}
