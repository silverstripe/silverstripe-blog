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
		$this->feed = new SimplePie($this->RssUrl);
		$this->feed->init();
		if($items = $this->feed->get_items(0, $this->NumberToShow)) {
			foreach($items as $item) {
				$output->push(new ArrayData(array(
					"Title" => $item->get_title(),
					"Link" => $item->get_link()
				)));
			}
			return $output;
		}
	}
}

?>