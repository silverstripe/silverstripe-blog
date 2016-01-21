# SilverStripe Blog Module
[![Build Status](https://travis-ci.org/silverstripe/silverstripe-blog.svg?branch=master)](https://travis-ci.org/silverstripe/silverstripe-blog)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/silverstripe/silverstripe-blog/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/silverstripe/silverstripe-blog/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/silverstripe/silverstripe-blog/badges/build.png?b=master)](https://scrutinizer-ci.com/g/silverstripe/silverstripe-blog/build-status/master)
[![codecov.io](https://codecov.io/github/silverstripe/silverstripe-blog/coverage.svg?branch=master)](https://codecov.io/github/silverstripe/silverstripe-blog?branch=master)

[![Latest Stable Version](https://poser.pugx.org/silverstripe/blog/version)](https://packagist.org/packages/silverstripe/blog)
[![Latest Unstable Version](https://poser.pugx.org/silverstripe/blog/v/unstable)](//packagist.org/packages/silverstripe/blog)
[![Total Downloads](https://poser.pugx.org/silverstripe/blog/downloads)](https://packagist.org/packages/silverstripe/blog)
[![License](https://poser.pugx.org/silverstripe/blog/license)](https://packagist.org/packages/silverstripe/blog)
[![Monthly Downloads](https://poser.pugx.org/silverstripe/blog/d/monthly)](https://packagist.org/packages/silverstripe/blog)
[![Daily Downloads](https://poser.pugx.org/silverstripe/blog/d/daily)](https://packagist.org/packages/silverstripe/blog)

[![Dependency Status](https://www.versioneye.com/php/silverstripe:blog/badge.svg)](https://www.versioneye.com/php/silverstripe:blog)
[![Reference Status](https://www.versioneye.com/php/silverstripe:blog/reference_badge.svg?style=flat)](https://www.versioneye.com/php/silverstripe:blog/references)

![codecov.io](https://codecov.io/github/silverstripe/silverstripe-blog/branch.svg?branch=master)


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


