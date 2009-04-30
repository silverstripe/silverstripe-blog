<?php

class BlogTreeTest extends SapphireTest {
	static $fixture_file = 'blog/tests/BlogTreeTest.yml';

	function testGetAllBlogEntries() {
		$node = $this->fixture->objFromFixture('BlogTree', 'root');
		$this->assertEquals($node->Entries()->Count(), 3);
		
		$node = $this->fixture->objFromFixture('BlogTree', 'levela');
		$this->assertEquals($node->Entries()->Count(), 2);

		$node = $this->fixture->objFromFixture('BlogTree', 'levelaa');
		$this->assertEquals($node->Entries()->Count(), 2);
		
		$node = $this->fixture->objFromFixture('BlogTree', 'levelab');
		$this->assertNull($node->Entries());
		
		$node = $this->fixture->objFromFixture('BlogTree', 'levelb');
		$this->assertEquals($node->Entries()->Count(), 1);
		
		$node = $this->fixture->objFromFixture('BlogTree', 'levelba');
		$this->assertEquals($node->Entries()->Count(), 1);
	}
	
	
	
}

?>
