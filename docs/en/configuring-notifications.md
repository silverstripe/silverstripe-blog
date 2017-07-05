# Configuring notifications

## Configuring whether notifications will send to authors of blogs if comments are spam

Default behaviour using the `silverstripe/comment-notifications` module is to send notification to authors of comments occuring regardless if they are spam or not.

In some cases you may wish to not send a notification email to an author if the comment is spam, this is a configurable option.

Add the following into your yaml config:

```
BlogPostNotifications:
  notification_on_spam: false
```

