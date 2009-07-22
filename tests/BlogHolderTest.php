<?php

class BlogHolderTest extends SapphireTest {
	static $fixture_file = 'blog/tests/BlogTest.yml';

	function testGetAllBlogEntries() {
		$mainblog = $this->objFromFixture('BlogHolder', 'mainblog');
		
		$this->assertEquals($mainblog->Entries()->Count(), 3);
	}
	
	function testEntriesByMonth() {
		$mainblog = $this->objFromFixture('BlogHolder', 'mainblog');
		
		$entries = $mainblog->Entries('', '', '2008-01');
		$this->assertEquals($entries->Count(), 2);
		$expectedEntries = array(
			'test-post-2',
			'test-post-3'
		);
		
		foreach($entries as $entry) {
			$this->assertContains($entry->URLSegment, $expectedEntries);
		}
	}
	
	function textEntriesByYear() {
		$mainblog = $this->objFromFixture('BlogHolder', 'mainblog');
		
		$entries = $mainblog->Entries('', '', '2007');
		$this->assertEquals($entries->Count(), 1);
		$expectedEntries = array(
			'test-post'
		);
		
		foreach($entries as $entry) {
			$this->assertContains($entry->URLSegment, $expectedEntries);
		}
	}
	
	function testEntriesByTag() {
		$mainblog = $this->objFromFixture('BlogHolder', 'mainblog');
		
		$entries = $mainblog->Entries('', 'tag1');
		$this->assertEquals($entries->Count(), 2);
		$expectedEntries = array(
			'test-post',
			'test-post-3'
		);
		
		foreach($entries as $entry) {
			$this->assertContains($entry->URLSegment, $expectedEntries);
		}
	}
	
	function testcheckAccessAction() {
		$blogHolder = new BlogHolder_Controller();

		$this->assertTrue($blogHolder->checkAccessAction('2009'));
		$this->assertTrue($blogHolder->checkAccessAction('0001'));
		$this->assertTrue($blogHolder->checkAccessAction('12345'));
		
		$this->assertFalse($blogHolder->checkAccessAction('209'));
		$this->assertFalse($blogHolder->checkAccessAction('123A'));
		$this->assertFalse($blogHolder->checkAccessAction('ab01a'));
	}
}

?>
