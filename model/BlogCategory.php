<?php

/**
 * A blog category fo generalising blog posts.
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <micmania@hotmail.co.uk>
**/
class BlogCategory extends DataObject {
	
	private static $db = array(
		"Title" => "Varchar(255)",
	);

	private static $has_one = array(
		"Blog" => "Blog",
	);

	private static $many_many = array(
		"BlogPosts" => "BlogPost",
	);

}