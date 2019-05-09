<?php

namespace SilverStripe\Blog\Widgets;

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Control\Director;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\DataList;
use SilverStripe\Widgets\Model\Widget;

if (!class_exists(Widget::class)) {
    return;
}

/**
 * @method Blog Blog()
 *
 * @property int $NumberOfPosts
 */
class BlogFeaturedPostsWidget extends Widget
{
    /**
     * @var string
     */
    private static $title = 'Featured Posts';

    /**
     * @var string
     */
    private static $cmsTitle = 'Featured Posts';

    /**
     * @var string
     */
    private static $description = 'Displays a list of featured blog posts.';

    /**
     * @var array
     */
    private static $db = [
        'NumberOfPosts' => 'Int',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'Blog' => Blog::class,
    ];

    /**
     * @var string
     */
    private static $table_name = 'BlogFeaturedPostsWidget';

    /**
     * {@inheritdoc}
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            /**
             * @var FieldList $fields
             */
            $fields->merge([
                DropdownField::create('BlogID', _t(__CLASS__ . '.Blog', 'Blog'), Blog::get()->map()),
                NumericField::create('NumberOfPosts', _t(__CLASS__ . '.NumberOfPosts', 'Number of Posts'))
            ]);
        });

        return parent::getCMSFields();
    }

    /**
     * @return array|DataList
     */
    public function getPosts()
    {
        $blog = $this->Blog();

        if ($blog) {
            return $blog->getBlogPosts()
                ->filter('ID:not', Director::get_current_page()->ID)
                ->filter('FeaturedInWidget', true)
                ->sort('RAND()')
                ->limit($this->NumberOfPosts);
        }

        return [];
    }
}
