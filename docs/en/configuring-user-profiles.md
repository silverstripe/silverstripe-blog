## Configuring user profiles

This module ships with User Profiles enabled by default.

If you'd prefer to disable this functionality and instead return a 404 for the `/profile/` page, you can do so using SilverStripe config.

In mysite/_config/settings.yml

```yaml
SilverStripe\Blog\Model\BlogController:
  disable_profiles: true
```

Note that if you have a really large members table (in the hundreds of thousands or more) you probably want to add an index for the `URLSegment` column:

```yaml
SilverStripe\Security\Member:
  indexes:
    URLSegment: true
```
