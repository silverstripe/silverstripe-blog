<?php

namespace SilverStripe\Blog\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ManyManyList;

/**
 * A blog tag for keyword descriptions of a blog post.
 *
 *
 * @method Blog Blog()
 *
 * @method ManyManyList|BlogPost[] BlogPosts()
 * @property string $Title
 * @property string $URLSegment
 */
class BlogTag extends DataObject implements CategorisationObject
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
    private static $table_name = 'BlogTag';

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
    private static $indexes = [
        'URLSegment' => true,
    ];

    /**
     * @var array
     */
    private static $belongs_many_many = [
        'BlogPosts' => BlogPost::class
    ];

    /**
     * @var string
     */
    private static $default_sort = '"BlogTag"."Title" ASC';

    /**
     * {@inheritdoc}
     */
    protected function getListUrlSegment()
    {
        return 'tag';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDuplicateError()
    {
        return _t(__CLASS__ . '.Duplicate', 'A blog tag already exists with that name.');
    }
}
