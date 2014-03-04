<?php

/**
 * @package blog
 * @subpackage tests
 */
class BlogEntryTest extends SapphireTest {
	static $fixture_file = 'blog/tests/BlogTest.yml';
	
	/**
	 * Tests BBCode functionality
	 */
	public function testBBCodeContent() {
		$tmpFlag = BlogEntry::$allow_wysiwyg_editing; 
		BlogEntry::$allow_wysiwyg_editing = false; 
		
		$entry = $this->objFromFixture('BlogEntry', 'testpost');
		$entry->Content = "[url=admin]the CMS[/url]";

		$this->assertEquals('<p><a href="admin">the CMS</a></p>', $entry->Content()->value);
		BlogEntry::$allow_wysiwyg_editing = $tmpFlag; 
	}

	/**
	 * Tests BlogEntry::Content method
	 */
	public function testContent() {
		$tmpFlag = BlogEntry::$allow_wysiwyg_editing; 
		BlogEntry::$allow_wysiwyg_editing = true; 
		
		$entry = $this->objFromFixture('BlogEntry', 'testpost');
		$entry->Content = '<a href="admin">the CMS</a>';

		$this->assertEquals('<a href="admin">the CMS</a>', $entry->Content());
		BlogEntry::$allow_wysiwyg_editing = $tmpFlag; 
	}
	
	/**
	 * Tests TagCollection parsing of tags
	 */
	public function testTagging() {
		$entry = new BlogEntry();
		$entry->Tags = 'damian,Bob, andrew , multiple words, thing,tag,item , Andrew';
		$tags = $entry->TagNames();
		ksort($tags);
		
		$this->assertEquals(array(
			'andrew' => 'Andrew',
			'bob' => 'Bob',
			'damian' => 'damian',
			'item' => 'item',
			'multiple words' => 'multiple words',
			'tag' => 'tag',
			'thing' => 'thing'
		), $tags);
	}
	
}
