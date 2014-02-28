<?php

class TagCloudWidgetTest extends SapphireTest {
	
	public function setUp() {
		parent::setUp();
		
		if(!class_exists('Widget')) return;
		
		// holder
		$holder = new BlogHolder();
		$holder->Title = 'Holder';
		$holder->write();
		TagCloudWidget::$container = $holder;
		
		// Save all pages
		$page = new BlogEntry();
		$page->Tags = 'Ultra, Very, Popular, Somewhat, NotVery, NotPopular';
		$page->ParentID = $holder->ID;
		$page->write();
		$page = new BlogEntry();
		$page->Tags = 'Ultra, Very, Popular, Somewhat, NotVery';
		$page->ParentID = $holder->ID;
		$page->write();
		$page = new BlogEntry();
		$page->Tags = 'Ultra, Very, Popular, Somewhat';
		$page->ParentID = $holder->ID;
		$page->write();
		$page = new BlogEntry();
		$page->Tags = 'Ultra, Very, Popular';
		$page->ParentID = $holder->ID;
		$page->write();
		$page = new BlogEntry();
		$page->Tags = 'Ultra, Very, Popular';
		$page->ParentID = $holder->ID;
		$page->write();
		$page = new BlogEntry();
		$page->Tags = 'Ultra, Very';
		$page->ParentID = $holder->ID;
		$page->write();
		$page = new BlogEntry();
		$page->Tags = 'Ultra';
		$page->ParentID = $holder->ID;
		$page->write();
		$page = new BlogEntry();
		$page->Tags = '';
		$page->ParentID = $holder->ID;
		$page->write();
	}
	
	public function tearDown() {
		parent::tearDown();
		
		if(!class_exists('Widget')) return;
		
		TagCloudWidget::$container = null;
	}
	
	/**
	 * Test that tags are correctly extracted from a blog tree
	 */
	public function testGetTags() {
		
		if(!class_exists('Widget')) $this->markTestSkipped('This test requires the Widget module');
		
		// Test sorting by alphabetic
		$widget = new TagCloudWidget();
		$widget->Sortby = 'alphabet';
		$tags = $widget->getTagsCollection()->toNestedArray();
		$this->assertEquals($tags[0]['Tag'], 'NotPopular');
		$this->assertEquals($tags[0]['Class'], 'not-popular');
		$this->assertEquals($tags[0]['Count'], 1);
		$this->assertEquals($tags[3]['Tag'], 'Somewhat');
		$this->assertEquals($tags[3]['Class'], 'somewhat-popular');
		$this->assertEquals($tags[3]['Count'], 3);
		$this->assertEquals($tags[5]['Tag'], 'Very');
		$this->assertEquals($tags[5]['Class'], 'very-popular');
		$this->assertEquals($tags[5]['Count'], 6);
		
		// Test sorting by frequency
		$widget = new TagCloudWidget();
		$widget->Sortby = 'frequency';
		$tags = $widget->getTagsCollection()->toNestedArray();
		$this->assertEquals($tags[0]['Tag'], 'Ultra');
		$this->assertEquals($tags[0]['Class'], 'ultra-popular');
		$this->assertEquals($tags[0]['Count'], 7);
		$this->assertEquals($tags[3]['Tag'], 'Somewhat');
		$this->assertEquals($tags[3]['Class'], 'somewhat-popular');
		$this->assertEquals($tags[3]['Count'], 3);
		$this->assertEquals($tags[5]['Tag'], 'NotPopular');
		$this->assertEquals($tags[5]['Class'], 'not-popular');
		$this->assertEquals($tags[5]['Count'], 1);
	}

}
