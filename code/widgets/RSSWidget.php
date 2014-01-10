<?php

if (class_exists('Widget')) {
	
	/**
	 * Presents a list of items from an RSS feed url
	 * 
	 * @package blog
	 */
	class RSSWidget extends Widget {

		private static $db = array(
			"RSSTitle" => "Text",
			"RssUrl" => "Text",
			"NumberToShow" => "Int"
		);

		private static $defaults = array(
			"NumberToShow" => 10,
			"RSSTitle" => 'RSS Feed'
		);

		private static $cmsTitle = "RSS Feed";

		private static $description = "Downloads another page's RSS feed and displays items in a list.";

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
					new TextField("RssUrl", _t(
						'RSSWidget.URL',
						"URL of the other page's RSS feed.  Please make sure this URL points to an RSS feed."
					)),
					new NumericField("NumberToShow", _t('RSSWidget.NTS', "Number of Items to show"))
				)
			);
			
			$this->extend('updateCMSFields', $fields);
			
			return $fields;
		}
		
		function Title() {
			return ($this->RSSTitle) ? $this->RSSTitle : _t('RSSWidget.DEFAULTTITLE', 'RSS Feed');
		}
		
		function getFeedItems() {
			$output = new ArrayList();

			// Protection against infinite loops when an RSS widget pointing to this page is added to this page 
			if(stristr($_SERVER['HTTP_USER_AGENT'], 'SimplePie')) { 
				return $output;
			}
			
			if(!class_exists('SimplePie')) {
				throw new LogicException(
					'Please install the "simplepie/simplepie" library by adding it to the "require" '
					+ 'section of your composer.json'
				);
			}
			
			$t1 = microtime(true);
			$feed = new SimplePie();
			$feed->set_feed_url($this->AbsoluteRssUrl);
			$feed->set_cache_location(TEMP_FOLDER);
			$feed->init();
			if($items = $feed->get_items(0, $this->NumberToShow)) {
				foreach($items as $item) {
					
					// Cast the Date
					$date = new Date('Date');
					$date->setValue($item->get_date());

					// Cast the Title
					$title = new Text('Title');
					$title->setValue(html_entity_decode($item->get_title()));

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
