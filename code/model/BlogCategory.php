<?php

/**
 * A blog category for generalising blog posts.
 *
 * @package silverstripe
 * @subpackage blog
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
     * @var array
     */
    private static $db = array(
        'Title' => 'Varchar(255)',
        'URLSegment' => 'Varchar(255)',
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'Blog' => 'Blog',
    );

    /**
     * @var array
     */
    private static $belongs_many_many = array(
        'BlogPosts' => 'BlogPost',
    );

    /**
     * {@inheritdoc}
     */
    protected function getListUrlSegment()
    {
        return 'categories';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDuplicateError()
    {
        return _t('BlogCategory.Duplicate', 'A blog category already exists with that name.');
    }

}
