<?php

class BlogTagTest extends SapphireTest {
	
	static $fixture_file = "blog.yml";

	public function setUp() {
		SS_Datetime::set_mock_now("2013-10-10 20:00:00");
		parent::setUp();
	}

	public function testBlogPosts() {
		$member = Member::currentUser();
		if($member) $member->logout();

		$post = $this->objFromFixture("BlogPost", "blogpost1");
		$tag = $this->objFromFixture("BlogTag", "firsttag");
		$posts = $tag->BlogPosts();
		$this->assertEquals(1, $posts->count(), "Tag blog post count");
	}

}
