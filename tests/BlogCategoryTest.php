<?php

class BlogCategoryTest extends SapphireTest {
	
	static $fixture_file = "blog.yml";

	public function setUp() {
		SS_Datetime::set_mock_now("2013-10-10 20:00:00");
		parent::setUp();
	}


	/**
	 * Tests that any blog posts returned from $category->BlogPosts() many_many are published,
	 * both by normal 'save & publish' functionality and by publish date.
	**/
	public function testBlogPosts() {
		// Ensure the user is not logged in as admin (or anybody)
		$member = Member::currentUser();
		if($member) $member->logout();

		$post = $this->objFromFixture("BlogPost", "blogpost1");
		$category = $this->objFromFixture("BlogCategory", "firstcategory");
		$this->assertEquals(1, $category->BlogPosts()->count(), "Category blog post count");
	}

}
