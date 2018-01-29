<?php

namespace SilverStripe\Blog\Model;

use SilverStripe\ORM\DataObject;

/**
 * A blog category for generalising blog posts.
 *
*
 * @method Blog Blog()
 *
 * @property string $Title
 * @property string $URLSegment
 * @property int $BlogID
 */
class BlogCategory extends DataObject implements CategorisationObject
{
    use BlogObject;

    /**
     * Use an exception code so that attempted writes can continue on
     * duplicate errors.
     *
     * @const string
     * This must be a string because ValidationException has decided we can't use int
     */
    const DUPLICATE_EXCEPTION = 'DUPLICATE';

    /**
     * {@inheritDoc}
     * @var string
     */
    private static $table_name = 'BlogCategory';

    /**
     * @var array
     */
    private static $db = [
        'Title'      => 'Varchar(255)',
        'URLSegment' => 'Varchar(255)'
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'Blog' => Blog::class,
    ];

    /**
     * @var array
     */
    private static $belongs_many_many = [
        'BlogPosts' => BlogPost::class,
    ];

    /**
     * {@inheritdoc}
     */
    protected function getListUrlSegment()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDuplicateError()
    {
        return _t(__CLASS__ . '.Duplicate', 'A blog category already exists with that name.');
    }
}
