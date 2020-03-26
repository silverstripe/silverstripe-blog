<?php

namespace SilverStripe\Blog\Widgets;

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Blog\Model\BlogTag;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\Widgets\Model\Widget;

if (!class_exists(Widget::class)) {
    return;
}

/**
 * @method Blog Blog()
 */
class BlogTagsCloudWidget extends Widget
{
    /**
     * @var string
     */
    private static $title = 'Tags Cloud';

    /**
     * @var string
     */
    private static $cmsTitle = 'Blog Tags Cloud';

    /**
     * @var string
     */
    private static $description = 'Displays a tag cloud for this blog.';

    /**
     * @var array
     */
    private static $db = [];

    /**
     * @var array
     */
    private static $has_one = [
        'Blog' => Blog::class,
    ];

    /**
     * @var string
     */
    private static $table_name = 'BlogTagsCloudWidget';

    /**
     * {@inheritdoc}
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            /*
             * @var FieldList $fields
             */
            $fields->push(
                DropdownField::create(
                    'BlogID',
                    _t(__CLASS__ . '.Blog', 'Blog'),
                    Blog::get()->map()
                )
            );
        });

        return parent::getCMSFields();
    }

    /**
     * @return ArrayList
     */
    public function getTags()
    {
        // Check blog exists
        $blog = $this->Blog();
        if (!$blog) {
            return ArrayList::create([]);
        }

        // create ArrayData that can be used to render the tag cloud
        $maxTagCount = 0;
        $tags = ArrayList::create();
        /** @var BlogTag $record */
        foreach ($blog->Tags() as $record) {
            // Remember max count found
            $count = $record->getBlogCount();
            $maxTagCount = $maxTagCount > $count ? $maxTagCount : $count;

            // Save
            $tags->push(ArrayData::create([
                'TagName'  => $record->Title,
                'Link'     => $record->getLink(),
                'TagCount' => $count,
            ]));
        }

        // normalize the tag counts from 1 to 10
        if ($maxTagCount) {
            $tagfactor = 10 / $maxTagCount;
            foreach ($tags->getIterator() as $tag) {
                $normalized = round($tagfactor * ($tag->TagCount));
                $tag->NormalizedTag = $normalized;
            }
        }

        return $tags;
    }
}
