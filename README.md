# SilverStripe Blog Module

[![Build Status](https://travis-ci.org/silverstripe/silverstripe-blog.png?branch=master)](https://travis-ci.org/silverstripe/silverstripe-blog) [![Latest Stable Version](https://poser.pugx.org/silverstripe/blog/v/stable.svg)](https://packagist.org/packages/silverstripe/blog) [![Total Downloads](https://poser.pugx.org/silverstripe/blog/downloads.svg)](https://packagist.org/packages/silverstripe/blog) [![Latest Unstable Version](https://poser.pugx.org/silverstripe/blog/v/unstable.svg)](https://packagist.org/silverstripe/silverstripe/blog) [![License](https://poser.pugx.org/silverstripe/blog/license.svg)](https://packagist.org/packages/silverstripe/blog)

## Documentation
[User guide](docs/en/userguide/index.md)

[Developer documentation](docs/en/index.md)

## Requirements

```
silverstripe/cms: ~3.1
silverstripe/lumberjack: ~1.1
silverstripe/tagfield: ^1.0
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


