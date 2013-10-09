<?php

class BlogTest extends SapphireTest {

	static $fixture_file = "blog.yml";

	public function setUp() {
		SS_Datetime::set_mock_now("2013-10-10 20:00:00");
		parent::setUp();
	}
	
	public function testGetExcludedSiteTreeClassNames() {
		$member = Member::currentUser();
		if($member) $member->logout();

		$blog = $this->objFromFixture("Blog", 'firstblog');

		Config::inst()->update("BlogPost", "show_in_sitetree", true);
		$classes = $blog->getExcludedSiteTreeClassNames();
		$this->assertEquals(0, count($classes), "No classes should be hidden.");

		Config::inst()->update("BlogPost", "show_in_sitetree", false);
		$classes = $blog->getExcludedSiteTreeClassNames();
		$this->assertEquals(1, count($classes), "BlogPost class should be hidden.");
	}



	public function testGetArchivedBlogPosts() {
		$member = Member::currentUser();
		if($member) $member->logout();

		$blog = $this->objFromFixture("Blog", "firstblog");

		// Test yearly
		$archive = $blog->getArchivedBlogPosts(2013);
		$this->assertEquals(2, $archive->count(), "Incorrect Yearly Archive count for 2013");
		$this->assertEquals("First post", $archive->first()->Title, "Incorrect First Blog post");
		$this->assertEquals("Second post", $archive->last()->Title, "Incorrect Last Blog post");

		// Test monthly
		$archive = $blog->getArchivedBlogPosts(2013, 10);
		$this->assertEquals(1, $archive->count(), "Incorrect monthly acrhive count.");

		// Test daily
		$archive = $blog->getArchivedBlogPosts(2013, 10, 01);
		$this->assertEquals(1, $archive->count(), "Incorrect daily archive count.");
	}

}