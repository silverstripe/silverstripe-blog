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
	
	function testTrackback() {
		$blog = $this->objFromFixture('BlogHolder', 'mainblog');
		$blog->TrackBacksEnabled = true; 
		$blog->write(); 
		
		$entry = $this->objFromFixture('BlogEntry', 'testpost');
		$response = $entry->trackbackping();
	
		$this->assertContains("<error>1</error>", $response);
		
		$_POST['url'] = 'test trackback post url';
		$_POST['title'] = 'test trackback post title';
		$_POST['excerpt'] = 'test trackback post excerpt';
		$_POST['blog_name'] = 'test trackback blog name';
	
		$response = $entry->trackbackping();
		$this->assertContains("<error>0</error>", $response);
		
		$trackback = DataObject::get_one('TrackBackPing');
		$this->assertEquals('test trackback post url', $trackback->Url); 
		$this->assertEquals('test trackback post title', $trackback->Title); 
		$this->assertEquals('test trackback post excerpt', $trackback->Excerpt); 
		$this->assertEquals('test trackback blog name', $trackback->BlogName); 
		
		unset($_POST); 
	}
}