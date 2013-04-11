<?php
if(class_exists('Widget')) {
	class RSSWidget extends Widget {
		static $db = array(
			"RSSTitle" => "Text",
			"RssUrl" => "Text",
			"NumberToShow" => "Int"
		);
		
		static $has_one = array();
		
		static $has_many = array();
		
		static $many_many = array();
		
		static $belongs_many_many = array();
		
		static $defaults = array(
			"NumberToShow" => 10,
			"RSSTitle" => 'RSS Feed'
		);
		static $cmsTitle = "RSS Feed";
		static $description = "Downloads another page's RSS feed and displays items in a list.";
		
		/**
		 * If the RssUrl is relative, convert it to absolute with the
		 * current baseURL to avoid confusing simplepie.
		 * Passing relative URLs to simplepie will result
		 * in strange DNS lookups and request timeouts.
		 * 
		 * @return string
		 */
		function getAbsoluteRssUrl() {
			$urlParts = parse_url($this->RssUrl);
			if(!isset($urlParts['host']) || !$urlParts['host']) {
				return Director::absoluteBaseURL() . $this->RssUrl;
			} else {
				return $this->RssUrl;
			}
		}
		
		function getCMSFields() {
			$fields = parent::getCMSFields(); 
			
			$fields->merge(
				new FieldList(
					new TextField("RSSTitle", _t('RSSWidget.CT', "Custom title for the feed")),
					new TextField("RssUrl", _t('RSSWidget.URL', "URL of the other page's RSS feed.  Please make sure this URL points to an RSS feed.")),
					new NumericField("NumberToShow", _t('RSSWidget.NTS', "Number of Items to show"))
				)
			);
			
			$this->extend('updateCMSFields', $fields);
			
			return $fields;
		}
		function Title() {
			return ($this->RSSTitle) ? $this->RSSTitle : 'RSS Feed';
		}
		
		function getFeedItems() {
			$output = new ArrayList();

			// Protection against infinite loops when an RSS widget pointing to this page is added to this page 
			if(stristr($_SERVER['HTTP_USER_AGENT'], 'SimplePie')) { 
				return $output;
			}
			
			include_once(Director::getAbsFile(SAPPHIRE_DIR . '/thirdparty/simplepie/simplepie.inc'));
			
			$t1 = microtime(true);
			$feed = new SimplePie($this->AbsoluteRssUrl, TEMP_FOLDER);
			$feed->init();
			if($items = $feed->get_items(0, $this->NumberToShow)) {
				foreach($items as $item) {
					
					// Cast the Date
					$date = new Date('Date');
					$date->setValue($item->get_date());

					// Cast the Title
					$title = new Text('Title');
					$title->setValue($item->get_title());

					$output->push(new ArrayData(array(
						'Title' => $title,
						'Date' => $date,
						'Link' => $item->get_link()
					)));
				}
				return $output;
			}
		}
	}

}