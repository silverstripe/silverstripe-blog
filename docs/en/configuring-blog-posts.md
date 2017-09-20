## Configuring blog posts in SiteTree

Because your blog is part of the SiteTree, usage is the same as any other page.

By default, blog posts don't appear in the SiteTree, to avoid clutter. Instead they appear inside your blog as a GridField.

![](_images/blog-post-management.png)

If you'd rather display your posts within the SiteTree, you can do so using SilverStripe config.

In mysite/_config/settings.yml

```yaml
SilverStripe\Blog\Model\BlogPost:
  show_in_sitetree: true
```
