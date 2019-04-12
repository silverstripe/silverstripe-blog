<?php

namespace SilverStripe\Blog\Model;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\CheckboxField;

/**
 * Adds a checkbox field for featured blog posts widget.
 */
class BlogPostFeaturedExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $db = [
        'FeaturedInWidget' => 'Boolean',
    ];

    /**
     * {@inheritdoc}
     */
    public function updateCMSFields(FieldList $fields)
    {
        // Add the checkbox in.
        $fields->addFieldToTab(
            'Root.PostOptions',
            CheckboxField::create('FeaturedInWidget', _t(__CLASS__ . '.FEATURED', 'Include Post in Feature Widget'))
        );
    }
}
