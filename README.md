# SilverStripe Blog Module
[![Build Status](https://travis-ci.org/silverstripe/silverstripe-blog.svg?branch=master)](https://travis-ci.org/silverstripe/silverstripe-blog)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/silverstripe/silverstripe-blog/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/silverstripe/silverstripe-blog/?branch=master)
[![codecov.io](https://codecov.io/github/silverstripe/silverstripe-blog/coverage.svg?branch=master)](https://codecov.io/github/silverstripe/silverstripe-blog?branch=master)

## Documentation

* [User guide](docs/en/userguide/index.md)
* [Developer documentation](docs/en/index.md)

## Requirements

* SilverStripe CMS 4.0+
* SilverStripe Lumberjack Module 2.0+
* SilverStripe Tag Field Module 2.0+
* SilverStripe Assets 1.0+
* SilverStripe Asset Admin Module 1.0+

Note: this version is compatible with SilverStripe 4. For SilverStripe 3, please see [the 2.x release line](https://github.com/silverstripe/silverstripe-blog/tree/2).

### Suggested Modules

* SilverStripe Widgets Module
* SilverStripe Comments Module

## Installation

```
composer require silverstripe/blog
```

## Upgrading

### Upgrading from 2.x to 3.x

Aside from the framework and CMS upgrades required the blog module should not require anything extra to be completed.

### Upgrading legacy blog to 2.x

If you're upgrading from blog version 1.0 to 2.x you will need to run the `BlogMigrationTask`. Run the task using `dev/tasks/BlogMigrationTask` either via the browser or sake CLI to migrate your legacy blog to the new version data structure.
