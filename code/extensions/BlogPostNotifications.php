<?php

/**
 * Customise blog post to support comment notifications
 * Extends {@see BlogPost} with extensions to {@see CommentNotifiable}
 */
class BlogPostNotifications extends DataExtension {

	/**
	 * Notify all authors of notifications
	 *
	 * @param type $list
	 * @param type $comment
	 */
	public function updateNotificationRecipients(&$list, &$comment) {
		// Notify all authors
		$list = $this->owner->Authors();
	}

	/**
	 * Update comment to include the page title
	 *
	 * @param string $subject
	 * @param Comment $comment
	 * @param Member|string $recipient
	 */
	public function updateNotificationSubject(&$subject, &$comment, &$recipient) {
		$subject = "A new comment has been posted on " . $this->owner->Title;
	}
}
