<?php

/**
 * @mixin PHPUnit_Framework_TestCase
 */
class BlogCategoryTest extends FunctionalTest {
	/**
	 * @var string
	 */
	static $fixture_file = 'blog.yml';

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		SS_Datetime::set_mock_now('2013-10-10 20:00:00');
	}

	/**
	 * {@inheritdoc}
	 */
	public function tearDown() {
		SS_Datetime::clear_mock_now();

		parent::tearDown();
	}

	/**
	 * Tests that any blog posts returned from $category->BlogPosts() many_many are published,
	 * both by normal 'save & publish' functionality and by publish date.
	 */
	public function testBlogPosts() {
		$member = Member::currentUser();

		if($member) {
			$member->logout();
		}

		$this->objFromFixture('BlogPost', 'FirstBlogPost');

		/**
		 * @var BlogCategory $category
		 */
		$category = $this->objFromFixture('BlogCategory', 'FirstCategory');

		$this->assertEquals(1, $category->BlogPosts()->count(), 'Category blog post count');
	}

	public function testCanView() {
		$this->useDraftSite();

		$this->objFromFixture('Member', 'Admin');

		$editor = $this->objFromFixture('Member', 'Editor');
		$category = $this->objFromFixture('BlogCategory', 'SecondCategory');

		$this->assertFalse($category->canView($editor), 'Editor should not be able to view category.');
	}

	/**
	 * The first blog can be viewed by anybody.
	 */
	public function testCanEdit() {
		$this->useDraftSite();

		$admin = $this->objFromFixture('Member', 'Admin');
		$editor = $this->objFromFixture('Member', 'Editor');

		$category = $this->objFromFixture('BlogCategory', 'FirstCategory');

		$this->assertTrue($category->canEdit($admin), 'Admin should be able to edit category.');
		$this->assertTrue($category->canEdit($editor), 'Editor should be able to edit category.');

		$category = $this->objFromFixture('BlogCategory', 'SecondCategory');

		$this->assertTrue($category->canEdit($admin), 'Admin should be able to edit category.');
		$this->assertFalse($category->canEdit($editor), 'Editor should not be able to edit category.');

		$category = $this->objFromFixture('BlogCategory', 'ThirdCategory');

		$this->assertTrue($category->canEdit($admin), 'Admin should always be able to edit category.');
		$this->assertTrue($category->canEdit($editor), 'Editor should be able to edit category.');
	}

	public function testCanCreate() {
		$this->useDraftSite();

		$admin = $this->objFromFixture('Member', 'Admin');
		$editor = $this->objFromFixture('Member', 'Editor');

		$category = singleton('BlogCategory');

		$this->assertTrue($category->canCreate($admin), 'Admin should be able to create category.');
		$this->assertTrue($category->canCreate($editor), 'Editor should be able to create category.');
	}

	public function testCanDelete() {
		$this->useDraftSite();

		$admin = $this->objFromFixture('Member', 'Admin');
		$editor = $this->objFromFixture('Member', 'Editor');

		$category = $this->objFromFixture('BlogCategory', 'FirstCategory');

		$this->assertTrue($category->canDelete($admin), 'Admin should be able to delete category.');
		$this->assertTrue($category->canDelete($editor), 'Editor should be able to category category.');

		$category = $this->objFromFixture('BlogCategory', 'SecondCategory');
		$this->assertTrue($category->canDelete($admin), 'Admin should be able to delete category.');
		$this->assertFalse($category->canDelete($editor), 'Editor should not be able to delete category.');

		$category = $this->objFromFixture('BlogCategory', 'ThirdCategory');
		$this->assertTrue($category->canDelete($admin), 'Admin should always be able to delete category.');
		$this->assertTrue($category->canDelete($editor), 'Editor should be able to delete category.');
	}
}
