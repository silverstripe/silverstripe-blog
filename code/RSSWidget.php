<?php

class RSSWidget extends Widget {
	static $db = array(
		"CustomTitle" => "Text",
		"RssUrl" => "Text",
		"NumberToShow" => "Int",
	);
	
	static $defaults = array(
		"NumberToShow" => 10
	);
	
	static $title = "RSS Feed";
	static $cmsTitle = "RSS Feed";
	static $description = "Shows the latest entries of a RSS feed.";
	
	function getCMSFields() {
		return new FieldSet(
			new TextField("CustomTitle","Custom title for the feed"),
			new TextField("RssUrl", "URL of RSS Feed"),
			new NumericField("NumberToShow", "Number of Items to show")
		);
	}
	
	function Title() {
		$this->feed = new SimplePie($this->RssUrl);
		$this->feed->init();
		return ($this->CustomTitle) ? $this->CustomTitle : $this->feed->get_feed_title();
	}
	
	function FeedItems() {
		$output = new DataObjectSet();
		foreach($this->feed->get_items(0, $this->NumberToShow) as $item) {
			$output->push(new ArrayData(array(
				"Title" => $item->get_title(),
				"Link" => $item->get_link()
			)));
		}
		return $output;
	}
}

?>