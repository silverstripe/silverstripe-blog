<?php

namespace SilverStripe\Blog\Model;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Dev\Deprecation;

/**
 * Adds a checkbox field for featured blog posts widget.
 *
 * @extends DataExtension<BlogPost>
 * @deprecated 4.3.0 Will be removed without equivalent functionality to replace it in a future major release
 */
class BlogPostFeaturedExtension extends DataExtension
{
    public function __construct()
    {
        Deprecation::withSuppressedNotice(function () {
            Deprecation::notice(
                '4.3.0',
                'Will be removed without equivalent functionality to replace it in a future major release',
                Deprecation::SCOPE_CLASS
            );
        });
        parent::__construct();
    }

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
