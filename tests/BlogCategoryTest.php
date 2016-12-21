<?php

use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Security\Member;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Dev\FunctionalTest;

/**
 * @mixin PHPUnit_Framework_TestCase
 */
class BlogCategoryTest extends FunctionalTest
{
    /**
     * @var string
     */
    public static $fixture_file = 'blog.yml';

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        DBDatetime::set_mock_now('2013-10-10 20:00:00');
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        DBDatetime::clear_mock_now();

        parent::tearDown();
    }

    /**
     * Tests that any blog posts returned from $category->BlogPosts() many_many are published,
     * both by normal 'save & publish' functionality and by publish date.
     */
    public function testBlogPosts()
    {
        $member = Member::currentUser();

        if ($member) {
            $member->logout();
        }

        $this->objFromFixture('BlogPost', 'FirstBlogPost');

        /**
         * @var BlogCategory $category
         */
        $category = $this->objFromFixture('BlogCategory', 'FirstCategory');

        $this->assertEquals(5, $category->BlogPosts()->count(), 'Category blog post count');
    }

    /**
     * @see https://github.com/silverstripe/silverstripe-blog/issues/376
     */
    public function testAllowMultibyteUrlSegment()
    {
        $blog = $this->objFromFixture('Blog', 'FirstBlog');
        $cat = new BlogCategory();
        $cat->BlogID = $blog->ID;
        $cat->Title = 'تست';
        $cat->write();
        // urlencoded
        $this->assertEquals('%D8%AA%D8%B3%D8%AA', $cat->URLSegment);
        $link = Controller::join_links($cat->Blog()->Link(), 'category', '%D8%AA%D8%B3%D8%AA');
        $this->assertEquals($link, $cat->getLink());
    }

    public function testCanView()
    {
        $this->useDraftSite();

        $this->objFromFixture('SilverStripe\\Security\\Member', 'Admin');

        $editor = $this->objFromFixture('SilverStripe\\Security\\Member', 'Editor');
        $category = $this->objFromFixture('BlogCategory', 'SecondCategory');

        $this->assertFalse($category->canView($editor), 'Editor should not be able to view category.');
    }

    /**
     * The first blog can be viewed by anybody.
     */
    public function testCanEdit()
    {
        $this->useDraftSite();

        $admin = $this->objFromFixture('SilverStripe\\Security\\Member', 'Admin');
        $editor = $this->objFromFixture('SilverStripe\\Security\\Member', 'Editor');

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

    public function testCanCreate()
    {
        $this->useDraftSite();

        $admin = $this->objFromFixture('SilverStripe\\Security\\Member', 'Admin');
        $editor = $this->objFromFixture('SilverStripe\\Security\\Member', 'Editor');

        $category = singleton('BlogCategory');

        $this->assertTrue($category->canCreate($admin), 'Admin should be able to create category.');
        $this->assertTrue($category->canCreate($editor), 'Editor should be able to create category.');
    }

    public function testCanDelete()
    {
        $this->useDraftSite();

        $admin = $this->objFromFixture('SilverStripe\\Security\\Member', 'Admin');
        $editor = $this->objFromFixture('SilverStripe\\Security\\Member', 'Editor');

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

    public function testDuplicateCategories() {
        $blog = new Blog();
        $blog->Title = 'Testing for duplicate categories';
        $blog->write();

        $category = new BlogCategory();
        $category->Title = 'Test';
        $category->BlogID = $blog->ID;
        $category->write();

        $category = new BlogCategory();
        $category->Title = 'Test';
        $category->BlogID = $blog->ID;
        try {
            $category->write();
            $this->fail('Duplicate BlogCategory written');
        } catch (ValidationException $e) {
            $codeList = $e->getResult()->codeList();
            $this->assertCount(1, $codeList);
            $this->assertEquals(BlogTag::DUPLICATE_EXCEPTION, $codeList[0]);
        }
    }
}
