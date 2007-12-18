<?php

class ArchiveWidget extends Widget {
	static $db = array(
		"DisplayMode" => "Varchar"
	);
	
	static $defaults = array(
		"DisplayMode" => "month"
	);
	
	static $title = "Browse by Date";
	static $cmsTitle = "Blog Archive";
	static $description = "Show a list of months or years in which there are blog posts, and provide links to them.";
	
	function getBlogHolder() {
		$page = Director::currentPage();
		
		if($page->is_a("BlogHolder")) {
			return $page;
		} else if($page->is_a("BlogEntry") && $page->getParent()->is_a("BlogHolder")) {
			return $page->getParent();
		} else {
			return DataObject::get_one("BlogHolder");
		}
	}
	
		function getCMSFields() {
		return new FieldSet(
			new OptionsetField("DisplayMode",_t('ArchiveWidget.DispBY', "Display by"),array("month"=>_t('ArchiveWidget.MONTH',"month"),"year"=>_t('ArchiveWidget.YEAR', "year")))
		);
	}
	
	function Dates() {
				Requirements::css("blog/css/archivewidget.css");
		$results = new DataObjectSet();
		$blogHolder = $this->getBlogHolder();
		$id = $blogHolder->ID;
		
		if($this->DisplayMode == "month"){
			$sqlResults = DB::query("SELECT DISTINCT MONTH(`Date`) AS `Month`, YEAR(`Date`) AS `Year` FROM `SiteTree` NATURAL JOIN `BlogEntry` WHERE `ParentID` = $id ORDER BY `Date` DESC");	
		}else{
			$sqlResults = DB::query("SELECT DISTINCT YEAR(`Date`) AS `Year` FROM `SiteTree` NATURAL JOIN `BlogEntry` WHERE `ParentID` = $id ORDER BY `Date` DESC");
		}
		
		
		
		foreach($sqlResults as $sqlResult) {
			$date = new Date("Date");
			
			
			
			$month = ($this->DisplayMode == "month") ? (int)$sqlResult['Month'] : 1;
			
			$date->setValue(array(
				"Day" => 1,
				"Month" => $month, 
				"Year" => (int)$sqlResult['Year']
			));
			
			if($this->DisplayMode == "month"){
				$link = $blogHolder->Link() . $sqlResult['Year']. '/' . sprintf("%'02d", $sqlResult['Month']);
			}
			else{
				$link = $blogHolder->Link() . $sqlResult['Year'];
			}
			
			$results->push(new ArrayData(array(
				"Date" => $date,
				"Link" => $link
			)));
		}
		
		return $results;
	}
}

?>