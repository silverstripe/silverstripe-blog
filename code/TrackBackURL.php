<?php
class TrackBackURL extends DataObject {
	
	static $db = array(
		'URL' => 'Varchar(2048)',
		'Pung' => 'Boolean(0)'
	);
	
	static $has_one = array(
		'BlogEntry' => 'BlogEntry'
	);

	function getCMSFields_forPopup() {

		return new FieldList(
			new TextField('URL'),
			new ReadonlyField('Pung', 'Pung?')
		); 
	}
	
	/**
	 * Return a human-reable string indicate whether the url has been pung or not
	 * Also update the url if it's duplicate
	 * @return string - 'Yes' or 'No'
	 */
	function IsPung() {
		if($this->Pung) return _t('TrackBackULR.YES', 'Yes'); 
		
		if($this->isDuplicate(true)) {
			$this->Pung = true; 
			$this->write();
			
			return _t('TrackBackULR.YES', 'Yes'); 
		}
		
		return _t('TrackBackULR.NO', 'No');
	}
	
	/**
	 * Check if there is a duplication, based on the associcated blog entry and the url. 
	 * If onPung is set, it returns true only when the duplicated record that has Pung = true
	 * @param boolean 
	 * @return boolean
	 */ 
	function isDuplicate($onPung = false) {
		$where = "\"BlogEntryID\" = {$this->BlogEntryID} AND \"URL\" = '{$this->URL}' AND \"TrackBackURL\".\"ID\" <> {$this->ID}"; 
		if($onPung) $where .= " AND \"Pung\" = 1"; 

		if(DataObject::get_one($this->ClassName, $where)) {
			return true;
		}
		
		return false; 
	}
}