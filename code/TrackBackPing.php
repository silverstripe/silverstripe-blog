<?php

class TrackBackPing extends DataObject {
	static $db = array(
		'Title' => 'Varchar',
		'Excerpt' => 'Text',
		// 2083 is URL-length limit for IE, AFAIK.
		// see: http://www.boutell.com/newfaq/misc/urllength.html
		'Url' => 'Varchar(2048)',
		'BlogName' => 'Varchar'
	);
	
	static $has_one = array(
		'Page' => 'Page'
	);
	
	static $has_many = array();
	
	static $many_many = array();
	
	static $belongs_many_many = array();
}

?>
