<?php

namespace SilverStripe\Blog\Model;

use SilverStripe\Comments\Model\Comment;
use SilverStripe\Core\Config\Config;
use SilverStripe\Model\List\SS_List;
use SilverStripe\Security\Member;
use SilverStripe\Core\Extension;

/**
 * Customise blog post to support comment notifications.
 *
 * Extends {@see BlogPost} with extensions to {@see CommentNotifiable}.
 *
 * @extends Extension<BlogPost>
 */
class BlogPostNotifications extends Extension
{
    /**
     * Configure whether to send notifications even for spam comments
     *
     * @config
     * @var boolean
     */
    private static $notification_on_spam = true;

    /**
     * Notify all authors of notifications.
     *
     * @param SS_List $list
     * @param mixed $comment
     */
    public function updateNotificationRecipients(&$list, &$comment)
    {
        //default is notification is on regardless of spam status
        $list = $this->owner->Authors();

        // If comment is spam and notification are set to not send on spam clear the recipient list
        if (Config::inst()->get(__CLASS__, 'notification_on_spam') == false && $comment->IsSpam) {
            $list = [];
        }
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
