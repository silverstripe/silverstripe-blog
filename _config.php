<?php

/**
 * Fetches the name of the current module folder name.
 *
 * @return string
**/
function blog_dir() {
	return trim(str_replace(BASE_PATH, "", dirname(__FILE__)), DIRECTORY_SEPARATOR);
}



/**
 * Returns the absolute  path of the current module path
 *
 * @return string
**/
function blog_path() {
	return BASE_PATH . '/' . blog_dir();
}