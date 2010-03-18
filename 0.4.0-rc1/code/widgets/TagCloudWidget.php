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

	static $popularities = array( 'not-popular', 'not-very-popular', 'somewhat-popular', 'popular', 'very-popular', 'ultra-popular' );
	
	function getCMSFields() {
		$fields = new FieldSet(
			new TextField("Title", _t("TagCloudWidget.TILE", "Title")),
			new TextField("Limit", _t("TagCloudWidget.LIMIT", "Limit number of tags")),
			new OptionsetField("Sortby",_t("TagCloudWidget.SORTBY","Sort by"),array("alphabet"=>_t("TagCloudWidget.SBAL", "alphabet"),"frequency"=>_t("TagCloudWidget.SBFREQ", "frequency")))
		);
		
		$this->extend('updateCMSFields', $fields);
		
		return $fields;
	}
	
	function Title() {
		return $this->Title ? $this->Title : 'Tag Cloud';
	}
	
	function TagsCollection() {
		Requirements::css("blog/css/tagcloud.css");
		
		$allTags = array();
		$max = 0;
		$container = BlogTree::current();
		
		$entries = $container->Entries();
		
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
					$allTags = array_slice($allTags, 0, $this->Limit,true);
				 }
				 if($this->Sortby == "alphabet"){
					$this->natksort($allTags);
				 }
				
				$sizes = array();	
				foreach ($allTags as $tag => $count) $sizes[$count] = true;
				
				$offset = 0; 
				$numsizes = count($sizes)-1; //Work out the number of different sizes
				$buckets = count(self::$popularities)-1;
				
				// If there are more frequencies then buckets, divide frequencies into buckets
				if ($numsizes > $buckets) {
					$numsizes = $buckets;
				}
				// Otherwise center use central buckets
				else {
					$offset   = round(($buckets-$numsizes)/2);
				}
				
				foreach($allTags as $tag => $count) {
					$popularity = round($count / $max * $numsizes) + $offset; $popularity=min($buckets,$popularity);
					$class = self::$popularities[$popularity];
					
					$allTags[$tag] = array(
						"Tag" => $tag,
						"Count" => $count,
						"Class" => $class,
						"Link" => $container->Link('tag') . '/' . urlencode($tag)		
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
