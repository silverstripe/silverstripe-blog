<?php
/**
 * @package blog
 * @subpackage tests
 */
class BlogTrackbackTest extends SapphireTest {
	static $fixture_file = 'blog/tests/BlogTrackbackTest.yml';
	
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
	
	function testTrackbackNotify() {
		$tmpServerClass = TrackBackDecorator::$trackback_server_class;
		TrackBackDecorator::$trackback_server_class = "TestTrackbackHTTPServer";
			
		$blog = $this->objFromFixture('BlogHolder', 'mainblog');
		$blog->TrackBacksEnabled = true;
		$blog->write(); 
		
		$entry = $this->objFromFixture('BlogEntry', 'testpost');
		$this->assertTrue($entry->trackbackNotify('testGoodTrackbackURL'));		
		$this->assertFalse($entry->trackbackNotify('testBadTrackbackURL'));
		$this->assertFalse($entry->trackbackNotify('testNonExistingTrackbackURL'));
		
		TrackBackDecorator::$trackback_server_class = $tmpServerClass;
	}

	function testOnBeforePublish() {
		$tmpServerClass = TrackBackDecorator::$trackback_server_class;
		TrackBackDecorator::$trackback_server_class = "TestTrackbackHTTPServer";
			
		$blog = $this->objFromFixture('BlogHolder', 'mainblog');
		$blog->TrackBacksEnabled = true;
		$blog->write(); 
		
		$entry1 = $this->objFromFixture('BlogEntry', 'testpost');
		$entry1->doPublish(); 
		$this->assertEquals(2, $entry1->TrackBackURLs()->Count());
		
		$this->assertEquals(array('testGoodTrackbackURL' => 1), $entry1->TrackBackURLs()->map('URL', 'Pung')->toArray());
		
		$entry2 = $this->objFromFixture('BlogEntry', 'testpost2');
		$entry2->doPublish(); 
		$this->assertEquals(4, $entry2->TrackBackURLs()->Count());
		$this->assertEquals(array('testBadTrackbackURL' => 0, 'testGoodTrackbackURL2' => 1, 'noneExistingURL' => 0, 'testGoodTrackbackURL3' => 1), $entry2->TrackBackURLs()->map('URL', 'Pung')->toArray());

		TrackBackDecorator::$trackback_server_class = $tmpServerClass;
	}
	
	function testDuplicateIsTrackBackURL() {
		$url1 = $this->objFromFixture('TrackBackURL', 'goodTrackBackURL1');
		$urlDup = $this->objFromFixture('TrackBackURL', 'dupTrackBackURL');
		
		$url2 = $this->objFromFixture('TrackBackURL', 'goodTrackBackURL2');
		$this->assertFalse($url2->isDuplicate());
		$this->assertFalse($url2->isDuplicate(true));
		
		$this->assertTrue($urlDup->isDuplicate());
		$this->assertFalse($urlDup->isDuplicate(true));
		
		$url1->Pung = true;
		$url1->write(); 
		$this->assertTrue($urlDup->isDuplicate(true));
	
	
	}
}

class TestTrackbackHTTPServer extends TrackbackHTTPServer implements TestOnly {
	
	function request($url, $data) {
		if(in_array($url, array('testGoodTrackbackURL', 'testGoodTrackbackURL2', 'testGoodTrackbackURL3'))) {
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