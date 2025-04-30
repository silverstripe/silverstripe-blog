<?php

namespace SilverStripe\Blog\Widgets;

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Control\Director;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\DataList;
use SilverStripe\Widgets\Model\Widget;
use SilverStripe\ORM\DataObject;
use SilverStripe\Dev\Deprecation;

if (!class_exists(Widget::class)) {
    return;
}

/**
 * @method Blog Blog()
 *
 * @property int $NumberOfPosts
 * @deprecated 4.3.0 Will be removed without equivalent functionality to replace it in a future major release
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
    private static $table_name = 'BlogRecentPostsWidget';

    public function __construct($record = [], $creationType = DataObject::CREATE_OBJECT, $queryParams = [])
    {
        Deprecation::withSuppressedNotice(function () {
            Deprecation::notice(
                '4.3.0',
                'Will be removed without equivalent functionality to replace it in a future major release',
                Deprecation::SCOPE_CLASS
            );
        });
        parent::__construct($record, $creationType, $queryParams);
    }

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
                ->sort('"PublishDate" DESC')
                ->limit($this->NumberOfPosts);
        }

        return [];
    }
}
