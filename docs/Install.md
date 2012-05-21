# Blog Module

## Introduction

The blog module allows you to post blogs on your SilverStripe. It includes the ability to post blogs using a site front-end form. Blogs are summarised on the blog holder page type, with more detail viewable when a specific blog is clicked. 

## Feature Overview

- Front-end blog post form
- Posts allow bbcode
- RSS feed for blog and also feeds for comments on posts
- Easily customizable
- Tag cloud widget
- Archive widget
- Blog management widget
- RSS widget (will likely move in future)
	
## Page types

We have chosen to go with the following page types to include with the blog module:

- **BlogTree** This is a holder of BlogHolder. If your site has only one blog holder, you won't need this page type.
- **BlogHolder** The BlogHolder shows BlogEntries, and provides a way to search etc.It would also contain methods to post new blogs.
- BlogEntry: This is simply an entry/post for the blog.

## View Archived Blogs

Blog archives can be viewed by `year/month` by appending the year, followed by a forward slash, then the numerical month, to the end of the BlogHolder URL. Alternately, just the year can be appended to view entries for that year.

for example: 

- `mysite/blog/2007/6` would show blog entries for June 2007
- `mysite/blog/2007` would show blog entries for 2007

## Comments and Spam Protection

See [PageComment](http://doc.silverstripe.org/pagecomment). 

## Widgets

See [Widgets](http://doc.silverstripe.org/widgets).

## Working with the theme

The blog comes set up to use the `\themes\blackcandy_blog\` directory by default. See [themes](http://doc.silverstripe.org/themes).
