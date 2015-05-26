# SilverStripe Blog Module

[![Build Status](https://travis-ci.org/micmania1/silverstripe-blogger.png?branch=1.0)](https://travis-ci.org/micmania1/silverstripe-blogger) [![Latest Stable Version](https://poser.pugx.org/micmania1/silverstripe-blog/v/stable.svg)](https://packagist.org/packages/micmania1/silverstripe-blog) [![Total Downloads](https://poser.pugx.org/micmania1/silverstripe-blog/downloads.svg)](https://packagist.org/packages/micmania1/silverstripe-blog) [![Latest Unstable Version](https://poser.pugx.org/micmania1/silverstripe-blog/v/unstable.svg)](https://packagist.org/packages/micmania1/silverstripe-blog) [![License](https://poser.pugx.org/micmania1/silverstripe-blog/license.svg)](https://packagist.org/packages/micmania1/silverstripe-blog)

## Features

* [User roles](docs/en/roles.md)
* [Tags and categories](docs/en/tags-and-categories.md)
* [Custom publish dates](docs/en/custom-publish-dates.md)
* [RSS Feed](docs/en/rss-feed.md)
* [Widgets](docs/en/widgets.md) (optional)
* [Custom pagination](docs/en/pagination.md)
* [Minimal design reduces SiteTree clutter](#usage)

## Requirements

```
silverstripe/cms: ~3.1
```

### Suggested Modules

```
silverstripe/widgets: *
silverstripe/comments: *
```

## Installation

```
composer require silverstripe/blog 2.0.x-dev
```

## Upgrading

If you're upgrading from an earlier version to 2.0, running a `dev/build` will migrate your legacy blog to the new version.

## Usage

Because your blog is part of the SiteTree, usage is the same as any other page.

By default, blog posts don't appear in the SiteTree, to avoid clutter. Instead they appear inside your blog as a GridField.

![](docs/en/_images/blog-post-management.png)

If you'd rather display your posts within the SiteTree, you can do so using SilverStripe config.

In mysite/_config/settings.yml

```yaml
BlogPost:
  show_in_sitetree: true
```
