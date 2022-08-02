# Silverstripe Blog Module

[![CI](https://github.com/silverstripe/silverstripe-blog/actions/workflows/ci.yml/badge.svg)](https://github.com/silverstripe/silverstripe-blog/actions/workflows/ci.yml)
[![Silverstripe supported module](https://img.shields.io/badge/silverstripe-supported-0071C4.svg)](https://www.silverstripe.org/software/addons/silverstripe-commercially-supported-module-list/)

## Documentation

* [User guide](docs/en/userguide/index.md)
* [Developer documentation](docs/en/index.md)

## Requirements

* Silverstripe CMS 4.0+
* Silverstripe Lumberjack Module 2.0+
* Silverstripe Tag Field Module 2.0+
* Silverstripe Assets 1.0+
* Silverstripe Asset Admin Module 1.0+

Note: this version is compatible with Silverstripe 4. For Silverstripe 3, please see [the 2.x release line](https://github.com/silverstripe/silverstripe-blog/tree/2).

### Suggested Modules

* Silverstripe Widgets Module
* Silverstripe Comments Module

## Installation

```
composer require silverstripe/blog
```

## Upgrading

### Upgrading from 2.x to 3.x

Aside from the framework and CMS upgrades required the blog module should not require anything extra to be completed.

### Upgrading legacy blog to 2.x

If you're upgrading from blog version 1.0 to 2.x you will need to run the `BlogMigrationTask`. Run the task using `dev/tasks/BlogMigrationTask` either via the browser or sake CLI to migrate your legacy blog to the new version data structure.
