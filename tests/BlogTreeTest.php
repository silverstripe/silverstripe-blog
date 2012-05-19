<?php

class BlogTreeTest extends SapphireTest {
	static $fixture_file = 'blog/tests/BlogTreeTest.yml';

	function testGetAllBlogEntries() {
		$node = $this->objFromFixture('BlogTree', 'root');
		$this->assertEquals($node->Entries()->Count(), 3);
		
		$node = $this->objFromFixture('BlogTree', 'levela');
		$this->assertEquals($node->Entries()->Count(), 2);

		$node = $this->objFromFixture('BlogTree', 'levelaa');
		$this->assertEquals($node->Entries()->Count(), 2);
		
		$node = $this->objFromFixture('BlogTree', 'levelab');
		$this->assertEquals($node->Entries()->Count(), 0); // this is not null anymore, it returns a DataList with no elements
		
		$node = $this->objFromFixture('BlogTree', 'levelb');
		$this->assertEquals($node->Entries()->Count(), 1);
		
		$node = $this->objFromFixture('BlogTree', 'levelba');
		$this->assertEquals($node->Entries()->Count(), 1);
		
		$this->assertTrue($node->getCMSFields() instanceof FieldList);
	}
	
	function testEntriesByMonth() {
		$node = $this->objFromFixture('BlogTree', 'root');
		
		$entries = $node->Entries('', '', '2008-01');
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
		$node = $this->objFromFixture('BlogTree', 'root');
		
		$entries = $node->Entries('', '', '2008');
		$this->assertEquals($entries->Count(), 2);
		$expectedEntries = array(
			'test-post-2',
			'test-post-3'
		);
		
		foreach($entries as $entry) {
			$this->assertContains($entry->URLSegment, $expectedEntries);
		}
	}
	
	function testEntriesByTag() {
		$node = $this->objFromFixture('BlogTree', 'root');
		
		$entries = $node->Entries('', 'tag3', '');
		$this->assertEquals($entries->Count(), 2);
		$expectedEntries = array(
			'test-post-2',
			'test-post-3'
		);
		
		foreach($entries as $entry) {
			$this->assertContains($entry->URLSegment, $expectedEntries);
		}
	}
	
	function testLandingPageFreshness() {
		$node = $this->objFromFixture('BlogTree', 'root');
		$this->assertEquals($node->LandingPageFreshness, '7 DAYS');	
		$node = $this->objFromFixture('BlogTree', 'levela');
		$this->assertEquals($node->LandingPageFreshness, '2 DAYS');	
		$node = $this->objFromFixture('BlogTree', 'levelb');
		$this->assertEquals($node->LandingPageFreshness, '7 DAYS');
	}
	
	function testGettingAssociatedBlogTree() {
		$this->assertEquals(BlogTree::current($this->objFromFixture('BlogTree', 'root'))->Title, 'Root BlogTree');
		$this->assertEquals(BlogTree::current($this->objFromFixture('BlogHolder', 'levelaa_blog2'))->Title, 'Level AA Blog 2');
		$this->assertEquals(BlogTree::current($this->objFromFixture('BlogEntry', 'testpost3'))->Title, 'Level BA Blog');
	}
	
	function testGettingBlogHolderIDs() {
		$node = $this->objFromFixture('BlogTree', 'root');
	
		$expectedIds = array();
		$expectedIds[] = $this->objFromFixture('BlogHolder', 'levelaa_blog1')->ID;
		$expectedIds[] = $this->objFromFixture('BlogHolder', 'levelaa_blog2')->ID;
		$expectedIds[] = $this->objFromFixture('BlogHolder', 'levelab_blog')->ID;
		$expectedIds[] = $this->objFromFixture('BlogHolder', 'levelba_blog')->ID;
		
		foreach($node->BlogHolderIDs() as $holderId) {
			$this->assertContains($holderId, $expectedIds);
		}
		$this->assertEquals(count($node->BlogHolderIDs()), count($expectedIds));
	}
	
	function testBlogTreeURLFuctions() {
		
	}
}

?>
