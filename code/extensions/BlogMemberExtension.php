<?php

/**
 * This class is responsible for add Blog specific behaviour to Members.
 *
 * @package silverstripe
 * @subpackage blog
 *
 **/
class BlogMemberExtension extends DataExtension {

	private static $db = array(
		'URLSegment' => 'Varchar',
		'BlogProfileSummary' => 'Text'
	);

	private static $has_one = array(
		'BlogProfileImage' => 'Image'
	);

	private static $belongs_many_many = array(
		'AuthoredPosts' => 'BlogPost'
	);

	public function onBeforeWrite() {
		// Generate a unique URL segment for the Member.
		$count = 1;

		$this->owner->URLSegment = $this->generateURLSegment();

		while(!$this->validURLSegment()) {
			$this->owner->URLSegment = preg_replace('/-[0-9]+$/', null, $this->owner->URLSegment) . '-' . $count;
			$count++;
		}
	}

	public function updateCMSFields(FieldList $fields) {
		$fields->removeByName('URLSegment');

		return $fields;
	}

	/**
	 * Generate a unique URL segment based on the Member's name.
	 * 
	 * @return string Generated URL segment.
	 */
	public function generateURLSegment() {
		$filter = URLSegmentFilter::create();
		$name = $this->owner->FirstName . ' ' . $this->owner->Surname;
		$urlSegment = $filter->filter($name);

		// Fallback to generic profile name if path is empty (= no valid, convertable characters)
		if(!$urlSegment || $urlSegment == '-' || $urlSegment == '-1') {
			$urlSegment = "profile-".$this->owner->ID;
		}

		return $urlSegment;
	}

	/**
	 * Returns TRUE if this object has a URL segment value that does not conflict with any other objects.
	 * 
	 * @return bool
	 */
	public function validURLSegment() { 
		$conflict = Member::get()->filter('URLSegment', $this->owner->URLSegment);

		// If the Member we're checking against is saved, exclude them from the check.
		// i.e. don't conflict against yourself.
		if ($this->owner->ID) {
			$conflict = $conflict->exclude('ID', $this->owner->ID);
		}

		return $conflict->count() == 0;
	}

}
