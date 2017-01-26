<?php

namespace SilverStripe\Blog\Model\BlogFilter;

use SilverStripe\Forms\FormTransformation;
use SilverStripe\Forms\GridField\GridField;

/**
 * Enables children of non-editable pages to be edited.
 */
class BlogFilterGridField extends GridField
{
    /**
     * @param FormTransformation $transformation
     *
     * @return $this
     */
    public function transform(FormTransformation $transformation)
    {
        return $this;
    }
}
