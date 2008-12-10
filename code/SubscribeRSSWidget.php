<?php

/**
 * A simple widget that just shows a link
 * to this website's blog RSS, with an RSS
 * icon.
 * 
 * @package blog
 */
class SubscribeRSSWidget extends Widget {
	
	static $db = array(
		'Title' => 'Varchar'
	);
	
	static $title = 'Subscribe via RSS';
	
	static $cmsTitle = 'Subscribe via RSS widget';
	
	static $description = 'Shows a link allowing a user to subscribe to this blog via RSS.';

	/**
	 * Get the BlogHolder instance that this widget
	 * is located on.
	 *
	 * @return BlogHolder
	 */
	function getBlogHolder() {
		$page = Director::currentPage();
		
		if($page instanceof BlogHolder) {
			return $page;
		} elseif(($page instanceof BlogEntry) && ($page->getParent() instanceof BlogHolder)) {
			return $page->getParent();
		} else {
			return DataObject::get_one('BlogHolder');
		}
	}
	
	/**
	 * Return an absolute URL based on the BlogHolder
	 * that this widget is located on.
	 * 
	 * @return string
	 */
	function RSSLink() {
		Requirements::themedCSS('subscribersswidget');
		$blogHolder = $this->getBlogHolder();
		if($blogHolder) {
			return $blogHolder->Link() . 'rss';
		}
	}
	
}

?>