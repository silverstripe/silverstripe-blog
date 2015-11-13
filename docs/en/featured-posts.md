# Featured posts

Featured posts can be enabled on a per-blog basis via the 'Settings' tab of each blog
in the CMS.

This will enable a checkbox in the CMS, with which you can feature blog posts:

![](_images/featured-posts-cms.png)

By default, the template will show the most recent featured post at the top of the
list of posts in a blog. This post will be removed from the normal list of blog posts.
You can increase the number of specially-displayed feature posts by modifying the
template to show more, and by changing the following config setting:

```
<% if $CanHaveFeaturedBlogPosts && $FeaturedBlogPosts %>
	<% loop $FeaturedBlogPosts.Limit(10) %>
		<% include FeaturedPostSummary %>
	<% end_loop %>
<% end_if %>
```

```yaml
Blog:
  excluded_featured_posts: 10
```
