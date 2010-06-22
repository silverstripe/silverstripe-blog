<?php
/**
 * @package blog
 * @subpackage tests
 */
class BlogTrackbackTest extends SapphireTest {
	static $fixture_file = 'blog/tests/BlogTest.yml';
	
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
	
	function testShouldTrackbackNotify() {
		$blog = $this->objFromFixture('BlogHolder', 'mainblog');
		$blog->TrackBacksEnabled = true;
		
		$entry = $this->objFromFixture('BlogEntry', 'testpost');
		$this->assertFalse($entry->ShouldTrackbackNotify());
		
		$entry = $this->objFromFixture('BlogEntry', 'testpost');
		$entry->TrackbackURL = '    ';
		$this->assertFalse($entry->ShouldTrackbackNotify());
		
		$entry = $this->objFromFixture('BlogEntry', 'testpost');
		$entry->TrackbackURL = 'someurl';
		$this->assertTrue($entry->ShouldTrackbackNotify());
	}
	
	function testTrackbackNotify() {
		$tmpServerClass = TrackBackDecorator::$trackback_server_class;
		TrackBackDecorator::$trackback_server_class = "TestTrackbackHTTPServer";
		
		$blog = $this->objFromFixture('BlogHolder', 'mainblog');
		$blog->TrackBacksEnabled = true;
		
		$entry = $this->objFromFixture('BlogEntry', 'testpost');
		$entry->TrackbackURL = 'testGoodTrackbackURL'; 
		$this->assertTrue($entry->trackbackNotify());		
		
		$entry->TrackbackURL = 'testBadTrackbackURL';
		$this->assertFalse($entry->trackbackNotify());
		
		$entry->TrackbackURL = 'testNonExistingTrackbackURL';
		$this->assertFalse($entry->trackbackNotify());
		
		TrackBackDecorator::$trackback_server_class = $tmpServerClass;
	}
}

class TestTrackbackHTTPServer extends TrackbackHTTPServer implements TestOnly {
	
	function request($url, $data) {
		if($url == 'testGoodTrackbackURL') {
			$response = $this->goodTrackback();
			$statusCode = '200';
		}
		else if($url == 'testBadTrackbackURL') {
			$response = $this->badTrackback(); 
			$statusCode = '200';
		}
		else {
			$response = $this->badTrackback(); 
			$statusCode = '404';
		}

		return new SS_HTTPResponse($response, $statusCode);
	}
	
	private function goodTrackback() {
		return "<?xml version=\"1.0\" encoding=\"utf-8\"?>
		<response>
			<error>0</error>
			<message></message>
		</response>"; 
	}
	
	private function badTrackback() {
		return "<?xml version=\"1.0\" encoding=\"utf-8\"?>
		<response>
			<error>1</error>
			<message>Some error text</message>
		</response>";
	}
}