# Changelog

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](http://semver.org/).

## [2.2.0]

* Changelog added.
* Default PublishDate to NULL for drafts and sort them at the top
* FIX Avoid feeding null to PaginatedList constructor
* FIX Explicitly set the PostsPerPage during migration
* ENHANCEMENT filtering for large user base sites.
* BUG Fix crash if parent page isn't Blog type
* BUGFIX: Dropdowns do not use unique IDs
* ENHANCEMENT Default archive year
* BUG Fix tag / category filters not being filtered in PaginatedList()
* FIX Hardcode the year to the current year in setUp()
* FIX Title bug due to [#320](https://github.com/silverstripe/silverstripe-blog/pull/320)
* Added featured posts to CMS
* Added hook for extension filters on category blog posts
* Update translations
* Added hook for extension filters on tag blog posts
