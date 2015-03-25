<?php

class BlogCategoryTest extends FunctionalTest {
	
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



	public function testCanView() {
		$this->useDraftSite();

		$admin = $this->objFromFixture("Member", "admin");
		$editor = $this->objFromFixture('Member', 'editor');

		// The first blog can bew viewed by anybody
		// $category = $this->objFromFixture("BlogCategory", "firstcategory");
		// $this->assertTrue($category->canView($admin), "Admin should be able to view category.");
		// $this->assertTrue($category->canView($editor), "Editor should be able to view category.");

		$category = $this->objFromFixture("BlogCategory", "secondcategory");
		// $this->assertTrue($category->canView($admin), "Admin should be able to view category.");
		$this->assertFalse($category->canView($editor), "Editor should not be able to view category.");
	}



	public function testCanEdit() {
		$this->useDraftSite();

		$admin = $this->objFromFixture("Member", "admin");
		$editor = $this->objFromFixture('Member', 'editor');

		// The first blog can bew viewed by anybody
		$category = $this->objFromFixture("BlogCategory", "firstcategory");
		$this->assertTrue($category->canEdit($admin), "Admin should be able to edit category.");
		$this->assertTrue($category->canEdit($editor), "Editor should be able to edit category.");

		$category = $this->objFromFixture("BlogCategory", "secondcategory");
		$this->assertTrue($category->canEdit($admin), "Admin should be able to edit category.");
		$this->assertFalse($category->canEdit($editor), "Editor should not be able to edit category.");

		$category = $this->objFromFixture("BlogCategory", "thirdcategory");
		$this->assertTrue($category->canEdit($admin), "Admin should always be able to edit category.");
		$this->assertTrue($category->canEdit($editor), "Editor should be able to edit category.");
	}



	public function testCanCreate() {
		$this->useDraftSite();

		$admin = $this->objFromFixture("Member", "admin");
		$editor = $this->objFromFixture('Member', 'editor');

		// The first blog can bew viewed by anybody
		$category = singleton('BlogCategory');
		$this->assertTrue($category->canCreate($admin), "Admin should be able to create category.");
		$this->assertTrue($category->canCreate($editor), "Editor should be able to create category.");
	}



	public function testCanDelete() {
		$this->useDraftSite();

		$admin = $this->objFromFixture("Member", "admin");
		$editor = $this->objFromFixture('Member', 'editor');

		// The first blog can bew viewed by anybody
		$category = $this->objFromFixture("BlogCategory", "firstcategory");
		$this->assertTrue($category->canDelete($admin), "Admin should be able to delete category.");
		$this->assertTrue($category->canDelete($editor), "Editor should be able to category category.");

		$category = $this->objFromFixture("BlogCategory", "secondcategory");
		$this->assertTrue($category->canDelete($admin), "Admin should be able to delete category.");
		$this->assertFalse($category->canDelete($editor), "Editor should not be able to delete category.");

		$category = $this->objFromFixture("BlogCategory", "thirdcategory");
		$this->assertTrue($category->canDelete($admin), "Admin should always be able to delete category.");
		$this->assertTrue($category->canDelete($editor), "Editor should be able to delete category.");
	}

}
