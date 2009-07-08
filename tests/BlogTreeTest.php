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
		$this->assertNull($node->Entries());
		
		$node = $this->objFromFixture('BlogTree', 'levelb');
		$this->assertEquals($node->Entries()->Count(), 1);
		
		$node = $this->objFromFixture('BlogTree', 'levelba');
		$this->assertEquals($node->Entries()->Count(), 1);
	}
	
	
	
}

?>
