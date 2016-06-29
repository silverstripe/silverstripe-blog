# SilverStripe Blog Module
[![Build Status](https://travis-ci.org/silverstripe/silverstripe-blog.svg?branch=master)](https://travis-ci.org/silverstripe/silverstripe-blog)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/silverstripe/silverstripe-blog/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/silverstripe/silverstripe-blog/?branch=master)
[![codecov.io](https://codecov.io/github/silverstripe/silverstripe-blog/coverage.svg?branch=master)](https://codecov.io/github/silverstripe/silverstripe-blog?branch=master)
![helpfulrobot](https://helpfulrobot.io/silverstripe/blog/badge)

[![Latest Stable Version](https://poser.pugx.org/silverstripe/blog/version)](https://packagist.org/packages/silverstripe/blog)
[![License](https://poser.pugx.org/silverstripe/blog/license)](https://packagist.org/packages/silverstripe/blog)
[![Monthly Downloads](https://poser.pugx.org/silverstripe/blog/d/monthly)](https://packagist.org/packages/silverstripe/blog)


## Documentation
[User guide](docs/en/userguide/index.md)

[Developer documentation](docs/en/index.md)

## Requirements

```
silverstripe/cms: ^3.1
silverstripe/lumberjack: ^1.1
silverstripe/tagfield: ^1.0
```

### Suggested Modules

```
silverstripe/widgets: *
silverstripe/comments: *
```

## Installation

```
composer require silverstripe/blog
```

## Upgrading legacy blog to 2.x

If you're upgrading from blog version 1.0 to 2.x you will need to run the `BlogMigrationTask`. Run the task using `dev/tasks/BlogMigrationTask` either via the browser or sake CLI to migrate your legacy blog to the new version data structure.


