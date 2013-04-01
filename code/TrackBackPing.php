<?php

class TrackBackPing extends DataObject {
	private static $db = array(
		'Title' => 'Varchar',
		'Excerpt' => 'Text',
		// 2083 is URL-length limit for IE, AFAIK.
		// see: http://www.boutell.com/newfaq/misc/urllength.html
		'Url' => 'Varchar(2048)',
		'BlogName' => 'Varchar'
	);
	
	private static $has_one = array(
		'Page' => 'Page'
	);
}