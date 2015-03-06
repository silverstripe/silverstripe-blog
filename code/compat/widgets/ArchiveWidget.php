<?php

if(!class_exists('Widget')) return;

/**
 * @deprecated since version 2.0
 */
class ArchiveWidget extends BlogArchiveWidget implements MigratableObject {

	private static $db = array(
		'DisplayMode' => 'Varchar'
	);

	private static $only_available_in = array('none');

	public function canCreate($member = null) {
		// Deprecated
		return false;
	}
	
	public function up() {
		if($this->DisplayMode) {
			$this->ArchiveType = ($this->DisplayMode === 'year') ? 'Yearly' : 'Monthly';
		}
		$this->ClassName = 'BlogArchiveWidget';
		$this->write();
	}
}
