<?php

class TagCloudWidget extends Widget {
	static $db = array(
		"Title" => "Varchar",
		"Limit" => "Int",
		"Sortby" => "Varchar"
	);
	
	static $has_one = array();
	
	static $has_many = array();
	
	static $many_many = array();
	
	static $belongs_many_many = array();
	
	static $defaults = array(
		"Title" => "Tag Cloud",
		"Limit" => "0",
		"Sortby" => "alphabet"
	);
	
	static $cmsTitle = "Tag Cloud";
	static $description = "Shows a tag cloud of tags on your blog.";
	
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
			new TextField("Title", _t("TagCloudWidget.TILE", "Title")),
			new TextField("Limit", _t("TagCloudWidget.LIMIT", "Limit number of tags")),
			new OptionsetField("Sortby",_t("TagCloudWidget.SORTBY","Sort by"),array("alphabet"=>_t("TagCloudWidget.SBAL", "alphabet"),"frequency"=>_t("TagCloudWidget.SBFREQ", "frequency")))
		);
	}
	
	function Title() {
		return $this->Title ? $this->Title : 'Tag Cloud';
	}
	
	function TagsCollection() {
		Requirements::css("blog/css/tagcloud.css");
		
		$allTags = array();
		$max = 0;
		$blogHolder = $this->getBlogHolder();
		
		$entries = $blogHolder->Entries();
		
		if($entries) {
			foreach($entries as $entry) {
				$theseTags = split(" *, *", strtolower(trim($entry->Tags)));
				foreach($theseTags as $tag) {
					if($tag != "") {
						$allTags[$tag] = isset($allTags[$tag]) ? $allTags[$tag] + 1 : 1; //getting the count into key => value map
						$max = ($allTags[$tag] > $max) ? $allTags[$tag] : $max;
					}
				}
			}
		
			if($allTags) {		
				//TODO: move some or all of the sorts to the database for more efficiency
				if($this->Limit > 0){
					uasort($allTags, array($this, "column_sort_by_popularity"));	//sort by popularity
					$allTags = array_slice($allTags, 0, $this->Limit);
				 }
				 if($this->Sortby == "alphabet"){
					$this->natksort($allTags);
				 }
				
				$sizes = array();	
				foreach($allTags as $tag => $count){
					$sizes[$count] = true;
				}
				$numsizes = count($sizes)-1; //Work out the number of different sizes
				if($numsizes > 5){$numsizes = 5;}
				foreach($allTags as $tag => $count) {
				
					$popularity = floor($count / $max * $numsizes);
					
					switch($popularity) {
						case 0:
							$class = "not-popular";
							break;
						case 1:
							$class = "not-very-popular";
							break;
						case 2:
							$class = "somewhat-popular";
							break;
						case 3:
							$class = "popular";
							break;
						case 4:
							$class = "very-popular";
							break;
						case 5:
							$class = "ultra-popular";
							break;
						default:
							$class = "broken";
							break;
					}
					
					$allTags[$tag] = array(
						"Tag" => $tag,
						"Count" => $count,
						"Class" => $class,
						"Link" => $blogHolder->Link() . 'tag/' . urlencode($tag)		
					);
				}
			}
			
			$output = new DataObjectSet();
			foreach($allTags as $tag => $fields) {
				$output->push(new ArrayData($fields));
			}
		return $output;	
		}
		
		return;		
	}
	
	/**
	 * Helper method to compare 2 Vars to work out the results.
	 * @param mixed
	 * @param mixed
	 * @return int
	 */
	private function column_sort_by_popularity($a, $b){
		if($a == $b) {
			$result  = 0;
		} 
		else {
			$result = $b - $a;
		}
		return $result;
	}

	private function natksort(&$aToBeSorted) {
		$aResult = array();
		$aKeys = array_keys($aToBeSorted);
		natcasesort($aKeys);
		foreach ($aKeys as $sKey) {
		    $aResult[$sKey] = $aToBeSorted[$sKey];
		}
		$aToBeSorted = $aResult;

		return true;
	}
}



?>
