# Blog Module

[![Build Status](https://secure.travis-ci.org/silverstripe/silverstripe-blog.png?branch=0.6)](http://travis-ci.org/silverstripe/silverstripe-blog)

## Introduction

The blog module allows you to post blogs on your SilverStripe. It includes the ability to post blogs using a site front-end form. Blogs are summarised on the blog holder page type, with more detail viewable when a specific blog is clicked.

## Maintainer Contact ##

 * Saophalkun Ponlu (phalkunz at silverstripe dot com)
 * Carlos Barberis (carlos at silverstripe dot com)

 ## Requirements

 * Silverstripe 3.0
 * (Optional) silverstripe-widgets module

## Feature Overview

*  Front-end blog post form
*  Posts allow bbcode
*  RSS feed for blog and also feeds for comments on posts
*  Easily customizable
*  Tag cloud widget
*  Archive widget
*  Blog management widget
*  RSS widget (will likely move in future)

## Configuration Options

### Use WYSIWYG editing instead of bbcode

Out of the box the blog module uses bbcode, just like the forum module. If you want to go back to using the standard page editing toolbar you need to add the following code to your mysite/_config.php file

	:::php
	BlogEntry::allow_wysiwyg_editing();


## Page types

We have chosen to go with the following page types to include with the blog module:

*  BlogHolder: The BlogHolder shows BlogEntrys, and provides a way to search etc.It would also contain methods to post new blogs.

*  BlogEntry: This is simply an entry/post for the blog.


## Simple form for adding a post

There is a blog management widget, that includes a link "Post new blog entry", which takes the user to [site/CurrentBlogHolder]/post (this is a good url to bookmark if you will be using it to blog regularly). This shows a blog entry form, which requires a subject and some content at the least. Clicking "Post blog entry" takes the user back to the blog. A login form will show if the user is not logged in. The entered author name is stored in a cookie. Initially the shown name will be the user's name.

#### BBcode support

*  BBCode can be entered into the form.

*  A bbcode tags help box shows when the "BBCode help" link is clicked. Javascript is required for this to work.

See [:PEAR:BBCodeParser](/PEAR/BBCodeParser) for more details.

#### Modifying the blog entry form

You may want to add or remove certain fields from the blog entry form. This can be done in **BlogHolder.php**. You will need to modify the $fields FieldSet object in the BlogEntryForm function. [tutorial 3](tutorial/3-forms#creating_the_form) shows you how to do this.

You will likely need to play around with the form and associated css to get the form looking how you  want it.

## View Archived Blogs

Blog archives can be viewed by year/month by appending the year, followed by a forward slash, then the numerical month, to the end of the blogholder URL. Alternately, just the year can be appended to view entries for that year.

for example: mysite/blog/2007/6 would show blog entries for June 2007

or: mysite/blog/2007 would show blog entries for 2007

## Comments and Spam Protection

See [:pagecomment](/pagecomment) for creating Askimet-protected comments for every page.

## Widgets

The module comes with a couple of default widgets, which rely on the "silverstripe/widgets"
module being installed. Since widgets are based on database records and relations
to pages, they need to be enabled through an `Extension` class in your `config.yml`:

	:::yml
	BlogTree:
	  extensions:
	    - WidgetPageExtension
	BlogEntry:
	  extensions:
	    - WidgetPageExtension

Alternatively, you can simply enable the extension on your `Page` records
to have it available globally.

## Working with the theme

The blog comes set up to use the `\themes\blackcandy_blog\` directory by default. 

   * See [:themes](/themes)