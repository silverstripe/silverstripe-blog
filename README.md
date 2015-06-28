# SilverStripe Blog Module

[![Build Status](https://travis-ci.org/silverstripe/silverstripe-blog.png?branch=master)](https://travis-ci.org/silverstripe/silverstripe-blog) [![Latest Stable Version](https://poser.pugx.org/silverstripe/blog/v/stable.svg)](https://packagist.org/packages/silverstripe/blog) [![Total Downloads](https://poser.pugx.org/silverstripe/blog/downloads.svg)](https://packagist.org/packages/silverstripe/blog) [![Latest Unstable Version](https://poser.pugx.org/silverstripe/blog/v/unstable.svg)](https://packagist.org/silverstripe/silverstripe/blog) [![License](https://poser.pugx.org/silverstripe/blog/license.svg)](https://packagist.org/packages/silverstripe/blog)

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

## Upgrading legacy blog to 2.0

If you're upgrading from blog version 1.0 to 2.0 you will need to run the `BlogMigrationTask`. Run the task using `dev/tasks/BlogMigrationTask` either via the browser or sake CLI to migrate your legacy blog to the new version data structure.

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
