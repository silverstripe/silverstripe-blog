<?php

class BlogTagTest extends FunctionalTest {
	
	static $fixture_file = "blog.yml";

	public function setUp() {
		SS_Datetime::set_mock_now("2013-10-10 20:00:00");
		parent::setUp();
	}

	public function tearDown() {
		SS_Datetime::clear_mock_now();
		parent::tearDown();
	}

	/**
	 * Tests that any blog posts returned from $tag->BlogPosts() many_many are published,
	 * both by normal 'save & publish' functionality and by publish date.
	**/
	public function testBlogPosts() {
		// Ensure the user is not logged in as admin (or anybody)
		$member = Member::currentUser();
		if($member) $member->logout();

		$post = $this->objFromFixture("BlogPost", "blogpost1");
		$tag = $this->objFromFixture("BlogTag", "firsttag");
		$this->assertEquals(1, $tag->BlogPosts()->count(), "Tag blog post count");
	}



	public function testCanView() {
		$this->useDraftSite();

		$admin = $this->objFromFixture("Member", "admin");
		$editor = $this->objFromFixture('Member', 'editor');

		// The first blog can bew viewed by anybody
		$tag = $this->objFromFixture("BlogTag", "firsttag");
		$this->assertTrue($tag->canView($admin), "Admin should be able to view tag.");
		$this->assertTrue($tag->canView($editor), "Editor should be able to view tag.");

		$tag = $this->objFromFixture("BlogTag", "secondtag");
		$this->assertTrue($tag->canView($admin), "Admin should be able to view tag.");
		$this->assertFalse($tag->canView($editor), "Editor should not be able to view tag.");
	}



	public function testCanEdit() {
		$this->useDraftSite();

		$admin = $this->objFromFixture("Member", "admin");
		$editor = $this->objFromFixture('Member', 'editor');

		// The first blog can bew viewed by anybody
		$tag = $this->objFromFixture("BlogTag", "firsttag");
		$this->assertTrue($tag->canEdit($admin), "Admin should be able to edit tag.");
		$this->assertTrue($tag->canEdit($editor), "Editor should be able to edit tag.");

		$tag = $this->objFromFixture("BlogTag", "secondtag");
		$this->assertTrue($tag->canEdit($admin), "Admin should be able to edit tag.");
		$this->assertFalse($tag->canEdit($editor), "Editor should not be able to edit tag.");

		$tag = $this->objFromFixture("BlogTag", "thirdtag");
		$this->assertTrue($tag->canEdit($admin), "Admin should always be able to edit tags.");
		$this->assertTrue($tag->canEdit($editor), "Editor should be able to edit tag.");
	}



	public function testCanCreate() {
		$this->useDraftSite();

		$admin = $this->objFromFixture("Member", "admin");
		$editor = $this->objFromFixture('Member', 'editor');

		// The first blog can bew viewed by anybody
		$tag = singleton("BlogTag");
		$this->assertTrue($tag->canCreate($admin), "Admin should be able to create tag.");
		$this->assertTrue($tag->canCreate($editor), "Editor should be able to create tag.");
	}



	public function testCanDelete() {
		$this->useDraftSite();

		$admin = $this->objFromFixture("Member", "admin");
		$editor = $this->objFromFixture('Member', 'editor');

		// The first blog can bew viewed by anybody
		$tag = $this->objFromFixture("BlogTag", "firsttag");
		$this->assertTrue($tag->canDelete($admin), "Admin should be able to delete tag.");
		$this->assertTrue($tag->canDelete($editor), "Editor should be able to delete tag.");

		$tag = $this->objFromFixture("BlogTag", "secondtag");
		$this->assertTrue($tag->canDelete($admin), "Admin should be able to delete tag.");
		$this->assertFalse($tag->canDelete($editor), "Editor should not be able to delete tag.");

		$tag = $this->objFromFixture("BlogTag", "thirdtag");
		$this->assertTrue($tag->canDelete($admin), "Admin should always be able to delete tags.");
		$this->assertTrue($tag->canDelete($editor), "Editor should be able to delete tag.");
	}

}
