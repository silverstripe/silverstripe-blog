<?php

/**
 * Adds Blog specific behaviour to Comment
 * Extends {@see Comment}
 */
class BlogCommentExtension extends DataExtension {

	/**
	 * Extra CSS classes for styling different comment types.
	 * @return string
	 */
	public function getExtraClass() {
		$blogPost = $this->owner->getParent();
		
		// Make sure we're dealing with a BlogPost.
		if (  ($blogPost instanceof BlogPost)
			&& $blogPost->isAuthor($this->owner->Author())
		) {
			return 'author-comment';
		}
	}
}
