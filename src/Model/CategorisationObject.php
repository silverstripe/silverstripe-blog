<?php

namespace SilverStripe\Blog\Model;

use SilverStripe\ORM\ManyManyList;

/**
 * @method ManyManyList|BlogPost[] BlogPosts()
 */
interface CategorisationObject
{
    /**
     * Number of times this object has blog posts in the current blog
     *
     * @return int
     */
    public function getBlogCount();
}
