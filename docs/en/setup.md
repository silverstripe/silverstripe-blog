# Setup

## Requirements

```
silverstripe/cms: ~3.1
```

## Suggested Modules

```
silverstripe/widgets: *
silverstripe/comments: *
```

## Installation

```
composer require silverstripe/blog 2.0.x-dev
```

## Usage

Because the blog is part of the SiteTree the usage is the same as any other page.

By default, blog posts are filtered out of the SiteTree to avoid clutter and instead put in a GridField inside
of the blog. If you wish to display the blog posts within the site tree you can do so using Silverstripe config.

In mysite/_config/settings.yml

```yaml
BlogPost:
  show_in_sitetree: true
```

Doing this will remove the GridField & result in a normal behaving SiteTree.
