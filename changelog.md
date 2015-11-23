# Changelog

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](http://semver.org/).

## [2.3.0]

* Allow injection on date
* Added standard code of conduct
* Converted to PSR-2
* Update translations
* BUG Fix regression in [#312](https://github.com/silverstripe/silverstripe-blog/pull/312) in PHP 5.3

## [2.2.0]

* Changelog added.
* Added standard git attributes
* Added standard license
* Added standard editor config
* Added hook for extension filters on tag blog posts
* Update translations
* Added hook for extension filters on category blog posts
* FIX Title bug due to [#320](https://github.com/silverstripe/silverstripe-blog/pull/320)
* FIX Hardcode the year to the current year in setUp()
* BUG Fix tag / category filters not being filtered in PaginatedList()
* ENHANCEMENT Default archive year 
* BUGFIX: Dropdowns do not use unique IDs
* BUG Fix crash if parent page isn't Blog type
* ENHANCEMENT filtering for large user base sites.
* FIX Explicitly set the PostsPerPage during migration
* i18n wrong label in BlogPost.php 
* Bugfix: avoid feeding null to PaginatedList constructor
* Default PublishDate to NULL for drafts and sort them at the top
* Update README to include additional requirements
* Fixed a bug where an error occurred when attempting to change the page type of 'Blog'