<?php
/**
 * @package blog
 * @subpackage tests
 */
class BlogEntryTest extends SapphireTest {
	static $fixture_file = 'blog/tests/BlogTest.yml';
	
	function testBBCodeContent() {
		$tmpFlag = BlogEntry::$allow_wysiwyg_editing; 
		BlogEntry::$allow_wysiwyg_editing = false; 
		
		$entry = $this->objFromFixture('BlogEntry', 'testpost');
		$entry->Content = "[url=admin]the CMS[/url]";

		$this->assertEquals('<p><a href="admin">the CMS</a></p>', $entry->Content()->value);
		BlogEntry::$allow_wysiwyg_editing = $tmpFlag; 
	}
	
	function testContent() {
		$tmpFlag = BlogEntry::$allow_wysiwyg_editing; 
		BlogEntry::$allow_wysiwyg_editing = true; 
		
		$entry = $this->objFromFixture('BlogEntry', 'testpost');
		$entry->Content = '<a href="admin">the CMS</a>';

		$this->assertEquals('<a href="admin">the CMS</a>', $entry->Content());
		BlogEntry::$allow_wysiwyg_editing = $tmpFlag; 
	}
	
}