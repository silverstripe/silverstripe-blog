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

		/**
		 * List of popularity classes in order of least to most popular
		 *
		 * @config
		 * @var array
		 */
		private static $popularities = array(
			'not-popular',
			'not-very-popular',
			'somewhat-popular',
			'popular',
			'very-popular',
			'ultra-popular'
		);

		public function getCMSFields() {
			
			$this->beforeUpdateCMSFields(function($fields) {
				$fields->merge(
					new FieldList(
						new TextField("Title", _t("TagCloudWidget.TILE", "Title")),
						new TextField("Limit", _t("TagCloudWidget.LIMIT", "Limit number of tags")),
						new OptionsetField(
							"Sortby",
							_t("TagCloudWidget.SORTBY", "Sort by"),
							array(
								"alphabet" => _t("TagCloudWidget.SBAL", "alphabet"),
								"frequency" => _t("TagCloudWidget.SBFREQ", "frequency")
							)
						)
					)
				);
			});

			return parent::getCMSFields();
		}

		function Title() {
			return $this->Title ? $this->Title : _t('TagCloudWidget.DEFAULTTITLE', 'Tag Cloud');
		}
		
		/**
		 * Current BlogTree used as the container for this tagcloud.
		 * Used by {@link TagCloudWidgetTest} for testing
		 * 
		 * @var BlogTree
		 */
		public static $container = null;

		/**
		 * Return all sorted tags in the system
		 * 
		 * @return ArrayList
		 */
		function getTagsCollection() {
			Requirements::themedCSS("tagcloud");

			// Ensure there is a valid BlogTree with entries
			$container = BlogTree::current(self::$container);
			if(	!$container
				|| !($entries = $container->Entries())
				|| $entries->count() == 0
			) return null;

			// Extract all tags from each entry
			$tagCounts = array(); // Mapping of tag => frequency
			$tagLabels = array(); // Mapping of tag => label
			foreach($entries as $entry) {
				$theseTags = $entry->TagNames();
				foreach($theseTags as $tag => $tagLabel) {
					$tagLabels[$tag] = $tagLabel;
					//getting the count into key => value map
					$tagCounts[$tag] = isset($tagCounts[$tag]) ? $tagCounts[$tag] + 1 : 1;
				}
			}
			if(empty($tagCounts)) return null;
			$minCount = min($tagCounts);
			$maxCount = max($tagCounts);

			// Apply sorting mechanism
			if($this->Sortby == "alphabet") {
				// Sort by name
				ksort($tagCounts);
			} else {
				 // Sort by frequency
				uasort($tagCounts, function($a, $b) {
					return $b - $a;
				});
			}
			
			// Apply limiting
			if($this->Limit > 0) $tagCounts = array_slice($tagCounts, 0, $this->Limit, true);

			// Calculate buckets of popularities
			$numsizes = count(array_unique($tagCounts)); //Work out the number of different sizes
			$popularities = self::config()->popularities;
			$buckets = count($popularities);

			// If there are more frequencies than buckets, divide frequencies into buckets
			if ($numsizes > $buckets) $numsizes = $buckets;
			
			// Adjust offset to use central buckets (if using a subset of available buckets)
			$offset = round(($buckets - $numsizes)/2);

			$output = new ArrayList();
			foreach($tagCounts as $tag => $count) {
				
				// Find position of $count in the selected range, adjusted for bucket range used
				if($maxCount == $minCount) {
					$popularity = $offset;
				} else {
					$popularity = round(
						($count-$minCount) / ($maxCount-$minCount) * ($numsizes-1)
					) + $offset;
				}
				$class = $popularities[$popularity];

				$output->push(new ArrayData(array(
					"Tag" => $tagLabels[$tag],
					"Count" => $count,
					"Class" => $class,
					"Link" => Controller::join_links($container->Link('tag'), urlencode($tag))
				)));
			}
			return $output;
		}
	}
}
