<?php

/**
 * Customise blog post to support comment notifications.
 *
 * Extends {@see BlogPost} with extensions to {@see CommentNotifiable}.
 */
class BlogPostNotifications extends DataExtension
{
    /**
     * Notify all authors of notifications.
     *
     * @param SS_List $list
     * @param mixed $comment
     */
    public function updateNotificationRecipients(&$list, &$comment)
    {
        $list = $this->owner->Authors();
    }

    /**
     * Update comment to include the page title.
     *
     * @param string $subject
     * @param Comment $comment
     * @param Member|string $recipient
     */
    public function updateNotificationSubject(&$subject, &$comment, &$recipient)
    {
        $subject = sprintf('A new comment has been posted on %s', $this->owner->Title);
    }
}
