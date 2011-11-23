<?php

/**
 * A simple widget that just shows a link
 * to this website's blog RSS, with an RSS
 * icon.
 * 
 * @package blog
 */
class SubscribeRSSWidget extends Widget {
	
	static $title = 'Subscribe via RSS';
	
	static $cmsTitle = 'Subscribe via RSS widget';
	
	static $description = 'Shows a link allowing a user to subscribe to this blog via RSS.';

	function Title() {
		return i18n::_t('SubscribeRSSWidget.ss.SUBSCRIBESHORTTITLE', SubscribeRSSWidget::$title);
	}
	
	function CmsTitle() {
		return i18n::_t('SubscribeRSSWidget.SINGULARNAME', SubscribeRSSWidget::$cmsTitle);
	}
	
	function Description() {
		return i18n::_t('SubscribeRSSWidget.ss.DESCRIPTION', SubscribeRSSWidget::$description);
	}
	
	/**
	 * Return an absolute URL based on the BlogHolder
	 * that this widget is located on.
	 * 
	 * @return string
	 */
	function RSSLink() {
		Requirements::themedCSS('subscribersswidget');
		$container = BlogTree::current();
		if ($container) return $container->Link() . 'rss';
	}
}

?>
