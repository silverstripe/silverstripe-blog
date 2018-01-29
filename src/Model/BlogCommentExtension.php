<?php

namespace SilverStripe\Blog\Model;

use SilverStripe\ORM\DataExtension;

/**
 * Adds Blog specific behaviour to Comment.
 */
class BlogCommentExtension extends DataExtension
{
    /**
     * Extra CSS classes for styling different comment types.
     *
     * @return string
     */
    public function getExtraClass()
    {
        $blogPost = $this->owner->getParent();

        if ($blogPost instanceof BlogPost) {
            if ($blogPost->isAuthor($this->owner->Author())) {
                return 'author-comment';
            }
        }

        return '';
    }
}
