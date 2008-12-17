<?php

class RSSWidget extends Widget {
	static $db = array(
		"RSSTitle" => "Text",
		"RssUrl" => "Text",
		"NumberToShow" => "Int"
	);
	
	static $defaults = array(
		"NumberToShow" => 10,
		"RSSTitle" => 'RSS Feed'
	);
	static $cmsTitle = "RSS Feed";
	static $description = "Shows the latest entries of a RSS feed.";
	
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
		return new FieldSet(
			new TextField("RSSTitle", _t('RSSWidget.CT', "Custom title for the feed")),
			new TextField("RssUrl", _t('RSSWidget.URL', "URL of RSS Feed")),
			new NumericField("NumberToShow", _t('RSSWidget.NTS', "Number of Items to show"))
		);
	}
	function Title() {
		return ($this->RSSTitle) ? $this->RSSTitle : 'RSS Feed';
	}
	
	function FeedItems() {
		$output = new DataObjectSet();
		
		include_once(Director::getAbsFile(SAPPHIRE_DIR . '/thirdparty/simplepie/SimplePie.php'));
		
		$t1 = microtime(true);
		$this->feed = new SimplePie($this->AbsoluteRssUrl, TEMP_FOLDER);
		$this->feed->init();
		if($items = $this->feed->get_items(0, $this->NumberToShow)) {
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

?>