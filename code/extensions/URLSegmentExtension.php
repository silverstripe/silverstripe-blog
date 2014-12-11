<?php

/**
 * Adds URLSegment functionality to Tags & Categories
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <github@michaelstrong.co.uk>
**/
class URLSegmentExtension extends DataExtension {

	private static $db = array(
		"URLSegment" => "Varchar(255)"
	);

	public function onBeforeWrite() {
		$this->owner->generateURLSegment();
	}



	/** 
	 * Generates a unique URLSegment from the title.
	 *
	 * @param $increment
	 *
	 * @return string URLSegment
	**/
	public function generateURLSegment($increment = null) {
		$filter = new URLSegmentFilter();
		$this->owner->URLSegment = $filter->filter($this->owner->Title);
		if(is_int($increment)) $this->owner->URLSegment .= '-' . $increment;

		// Check to see if the URLSegment already exists
		$duplicate = DataList::create($this->owner->ClassName)->filter(array(
			"URLSegment" => $this->owner->URLSegment,
			"BlogID" => $this->owner->BlogID
		));
		if($this->owner->ID) $duplicate = $duplicate->exclude("ID", $this->owner->ID);
		if($duplicate->count() > 0) {
			$increment = is_int($increment) ? $increment + 1 : 0;
			$this->owner->generateURLSegment((int) $increment);
		}
		return $this->owner->URLSegment;
	}

}