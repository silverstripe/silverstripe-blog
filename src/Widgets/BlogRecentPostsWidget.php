<?php

namespace SilverStripe\Blog\Widgets;

if (!class_exists('\\SilverStripe\\Widgets\\Model\\Widget')) {
    return;
}

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Widgets\Model\Widget;

/**
 * @method Blog Blog()
 *
 * @property int $NumberOfPosts
 */
class BlogRecentPostsWidget extends Widget
{
    /**
     * @var string
     */
    private static $title = 'Recent Posts';

    /**
     * @var string
     */
    private static $cmsTitle = 'Recent Posts';

    /**
     * @var string
     */
    private static $description = 'Displays a list of recent blog posts.';

    /**
     * @var array
     */
    private static $db = array(
        'NumberOfPosts' => 'Int',
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'Blog' => Blog::class,
    );

    /**
     * {@inheritdoc}
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            /**
             * @var FieldList $fields
             */
            $fields->merge(array(
                DropdownField::create('BlogID', _t(__CLASS__ . '.Blog', 'Blog'), Blog::get()->map()),
                NumericField::create('NumberOfPosts', _t(__CLASS__ . '.NumberOfPosts', 'Number of Posts'))
            ));
        });

        return parent::getCMSFields();
    }

    /**
     * @return array
     */
    public function getPosts()
    {
        $blog = $this->Blog();

        if ($blog) {
            return $blog->getBlogPosts()
                ->sort('"PublishDate" DESC')
                ->limit($this->NumberOfPosts);
        }

        return array();
    }
}
