<?php

namespace SilverStripe\Blog\Tests;

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Blog\Model\BlogCategory;
use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\Control\Controller;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

/**
 * @mixin PHPUnit_Framework_TestCase
 */
class BlogCategoryTest extends FunctionalTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'blog.yml';

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
        $member = Security::getCurrentUser();

        if ($member) {
            Security::setCurrentUser(null);
        }

        $this->objFromFixture(BlogPost::class, 'FirstBlogPost');

        /**
         * @var BlogCategory $category
         */
        $category = $this->objFromFixture(BlogCategory::class, 'FirstCategory');

        $this->assertEquals(5, $category->BlogPosts()->count(), 'Category blog post count');
    }

    /**
     * @see https://github.com/silverstripe/silverstripe-blog/issues/376
     */
    public function testAllowMultibyteUrlSegment()
    {
        /** @var Blog $blog */
        $blog = $this->objFromFixture(Blog::class, 'FirstBlog');

        $cat = new BlogCategory();
        $cat->Title = 'تست';
        $cat->write();


        // urlencoded
        $this->assertEquals('%D8%AA%D8%B3%D8%AA', $cat->URLSegment);
        $expectedLink = Controller::join_links($blog->Link('category'), '%D8%AA%D8%B3%D8%AA');
        $actualLink = $blog->Categories(false)->byID($cat->ID)->getLink();
        $this->assertEquals($expectedLink, $actualLink);
    }

    public function testCanView()
    {
        $this->useDraftSite();

        $this->objFromFixture(Member::class, 'Admin');

        $editor = $this->objFromFixture(Member::class, 'Editor');
        /** @var Blog $secondBlog */
        $secondBlog = $this->objFromFixture(Blog::class, 'SecondBlog');
        $category = $secondBlog->Categories(false)->find('URLSegment', 'second-category');
        $this->assertFalse($category->canView($editor), 'Editor should not be able to view category.');
    }

    /**
     * The first blog can be viewed by anybody.
     */
    public function testCanEdit()
    {
        $this->useDraftSite();

        $admin = $this->objFromFixture(Member::class, 'Admin');
        $editor = $this->objFromFixture(Member::class, 'Editor');

        /** @var Blog $firstBlog */
        $firstBlog = $this->objFromFixture(Blog::class, 'FirstBlog');
        $firstCategory = $firstBlog->Categories(false)->find('URLSegment', 'first-category');

        $this->assertTrue($firstCategory->canEdit($admin), 'Admin should be able to edit category.');
        $this->assertTrue($firstCategory->canEdit($editor), 'Editor should be able to edit category.');

        /** @var Blog $secondBlog */
        $secondBlog = $this->objFromFixture(Blog::class, 'SecondBlog');
        $secondCategory = $secondBlog->Categories(false)->find('URLSegment', 'second-category');

        $this->assertTrue($secondCategory->canEdit($admin), 'Admin should be able to edit category.');
        $this->assertFalse($secondCategory->canEdit($editor), 'Editor should not be able to edit category.');

        /** @var Blog $secondBlog */
        $thirdBlog = $this->objFromFixture(Blog::class, 'ThirdBlog');
        $thirdCategory = $thirdBlog->Categories(false)->find('URLSegment', 'third-category');

        $this->assertTrue($thirdCategory->canEdit($admin), 'Admin should always be able to edit category.');
        $this->assertTrue($thirdCategory->canEdit($editor), 'Editor should be able to edit category.');
    }

    public function testCanCreate()
    {
        $this->useDraftSite();

        $admin = $this->objFromFixture(Member::class, 'Admin');
        $editor = $this->objFromFixture(Member::class, 'Editor');

        $category = BlogCategory::singleton();

        $this->assertTrue($category->canCreate($admin), 'Admin should be able to create category.');
        $this->assertTrue($category->canCreate($editor), 'Editor should be able to create category.');
    }

    public function testCanDelete()
    {
        $this->useDraftSite();

        $admin = $this->objFromFixture(Member::class, 'Admin');
        $editor = $this->objFromFixture(Member::class, 'Editor');

        /** @var Blog $firstBlog */
        $firstBlog = $this->objFromFixture(Blog::class, 'FirstBlog');
        $firstCategory = $firstBlog->Categories(false)->find('URLSegment', 'first-category');

        $this->assertTrue($firstCategory->canDelete($admin), 'Admin should be able to delete category.');
        $this->assertTrue($firstCategory->canDelete($editor), 'Editor should be able to category category.');

        /** @var Blog $secondBlog */
        $secondBlog = $this->objFromFixture(Blog::class, 'SecondBlog');
        $secondCategory = $secondBlog->Categories(false)->find('URLSegment', 'second-category');

        $this->assertTrue($secondCategory->canDelete($admin), 'Admin should be able to delete category.');
        $this->assertFalse($secondCategory->canDelete($editor), 'Editor should not be able to delete category.');

        /** @var Blog $secondBlog */
        $thirdBlog = $this->objFromFixture(Blog::class, 'ThirdBlog');
        $thirdCategory = $thirdBlog->Categories(false)->find('URLSegment', 'third-category');

        $this->assertTrue($thirdCategory->canDelete($admin), 'Admin should always be able to delete category.');
        $this->assertTrue($thirdCategory->canDelete($editor), 'Editor should be able to delete category.');
    }

    public function testDuplicateCategories()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('A blog category already exists with that name.');

        $blog = new Blog();
        $blog->Title = 'Testing for duplicate categories';
        $blog->write();

        $category = new BlogCategory();
        $category->Title = 'Test';
        $category->URLSegment = 'test';
        $category->write();

        $category = new BlogCategory();
        $category->Title = 'Test';
        $category->URLSegment = 'test';
        $category->write();
    }
}
