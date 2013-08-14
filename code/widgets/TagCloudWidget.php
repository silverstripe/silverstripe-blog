<?php

if(class_exists('Widget')) {
	
	/**
	 * A list of tags associated with blog posts
	 * 
	 * @package blog
	 */
	class TagCloudWidget extends Widget {
		
		private static $db = array(
			"Title" => "Varchar",
			"Limit" => "Int",
			"Sortby" => "Varchar"
		);

		private static $defaults = array(
			"Title" => "Tag Cloud",
			"Limit" => "0",
			"Sortby" => "alphabet"
		);

		private static $cmsTitle = "Tag Cloud";
		private static $description = "Shows a tag cloud of tags on your blog.";

		private static $popularities = array( 'not-popular', 'not-very-popular', 'somewhat-popular', 'popular', 'very-popular', 'ultra-popular' );

		function getCMSFields() {
			$fields = parent::getCMSFields(); 

			$fields->merge(

				new FieldList(
					new TextField("Title", _t("TagCloudWidget.TILE", "Title")),
					new TextField("Limit", _t("TagCloudWidget.LIMIT", "Limit number of tags")),
					new OptionsetField("Sortby",_t("TagCloudWidget.SORTBY","Sort by"),array("alphabet"=>_t("TagCloudWidget.SBAL", "alphabet"),"frequency"=>_t("TagCloudWidget.SBFREQ", "frequency")))
				)
			);

			$this->extend('updateCMSFields', $fields);

			return $fields;
		}

		function Title() {
			return $this->Title ? $this->Title : _t('TagCloudWidget.DEFAULTTITLE', 'Tag Cloud');
		}

		function getTagsCollection() {
			Requirements::themedCSS("tagcloud");

			$allTags = array();
			$max = 0;
			$container = BlogTree::current();

			$entries = $container->Entries();

			if($entries) {
				foreach($entries as $entry) {
					$theseTags = preg_split(" *, *", mb_strtolower(trim($entry->Tags)));
					foreach($theseTags as $tag) {
						if($tag != "") {
							$allTags[$tag] = isset($allTags[$tag]) ? $allTags[$tag] + 1 : 1; //getting the count into key => value map
							$max = ($allTags[$tag] > $max) ? $allTags[$tag] : $max;
						}
					}
				}

				if($allTags) {		
					//TODO: move some or all of the sorts to the database for more efficiency
					if($this->Limit > 0) $allTags = array_slice($allTags, 0, $this->Limit, true);

					if($this->Sortby == "alphabet"){
						$this->natksort($allTags);
					} else{
						uasort($allTags, array($this, "column_sort_by_popularity")); // sort by frequency
					}

					$sizes = array();	
					foreach ($allTags as $tag => $count) $sizes[$count] = true;

					$offset = 0; 
					$numsizes = count($sizes)-1; //Work out the number of different sizes
					$buckets = count(self::$popularities)-1;

					// If there are more frequencies than buckets, divide frequencies into buckets
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

				$output = new ArrayList();
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
}
