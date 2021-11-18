<?php

namespace SilverStripe\Blog\Tests;

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Blog\Model\BlogCategory;
use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\Blog\Model\BlogTag;
use SilverStripe\Control\Controller;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

class BlogCategoryTest extends FunctionalTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'blog.yml';

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        DBDatetime::set_mock_now('2013-10-10 20:00:00');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
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
        $blog = $this->objFromFixture(Blog::class, 'FirstBlog');
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

        $this->objFromFixture(Member::class, 'Admin');

        $editor = $this->objFromFixture(Member::class, 'Editor');
        $category = $this->objFromFixture(BlogCategory::class, 'SecondCategory');

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

        $category = $this->objFromFixture(BlogCategory::class, 'FirstCategory');

        $this->assertTrue($category->canEdit($admin), 'Admin should be able to edit category.');
        $this->assertTrue($category->canEdit($editor), 'Editor should be able to edit category.');

        $category = $this->objFromFixture(BlogCategory::class, 'SecondCategory');

        $this->assertTrue($category->canEdit($admin), 'Admin should be able to edit category.');
        $this->assertFalse($category->canEdit($editor), 'Editor should not be able to edit category.');

        $category = $this->objFromFixture(BlogCategory::class, 'ThirdCategory');

        $this->assertTrue($category->canEdit($admin), 'Admin should always be able to edit category.');
        $this->assertTrue($category->canEdit($editor), 'Editor should be able to edit category.');
    }

    public function testCanCreate()
    {
        $this->useDraftSite();

        $admin = $this->objFromFixture(Member::class, 'Admin');
        $editor = $this->objFromFixture(Member::class, 'Editor');

        $category = singleton(BlogCategory::class);

        $this->assertTrue($category->canCreate($admin), 'Admin should be able to create category.');
        $this->assertTrue($category->canCreate($editor), 'Editor should be able to create category.');
    }

    public function testCanDelete()
    {
        $this->useDraftSite();

        $admin = $this->objFromFixture(Member::class, 'Admin');
        $editor = $this->objFromFixture(Member::class, 'Editor');

        $category = $this->objFromFixture(BlogCategory::class, 'FirstCategory');

        $this->assertTrue($category->canDelete($admin), 'Admin should be able to delete category.');
        $this->assertTrue($category->canDelete($editor), 'Editor should be able to category category.');

        $category = $this->objFromFixture(BlogCategory::class, 'SecondCategory');
        $this->assertTrue($category->canDelete($admin), 'Admin should be able to delete category.');
        $this->assertFalse($category->canDelete($editor), 'Editor should not be able to delete category.');

        $category = $this->objFromFixture(BlogCategory::class, 'ThirdCategory');
        $this->assertTrue($category->canDelete($admin), 'Admin should always be able to delete category.');
        $this->assertTrue($category->canDelete($editor), 'Editor should be able to delete category.');
    }

    public function testDuplicateCategories()
    {
        $blog = new Blog();
        $blog->Title = 'Testing for duplicate categories';
        $blog->write();

        $category = new BlogCategory();
        $category->Title = 'Test';
        $category->BlogID = $blog->ID;
        $category->URLSegment = 'test';
        $category->write();

        $category = new BlogCategory();
        $category->Title = 'Test';
        $category->URLSegment = 'test';
        $category->BlogID = $blog->ID;
        try {
            $category->write();
            $this->fail('Duplicate BlogCategory written');
        } catch (ValidationException $e) {
            $messages = $e->getResult()->getMessages();
            $this->assertCount(1, $messages);
            $this->assertEquals(BlogTag::DUPLICATE_EXCEPTION, $messages[0]['messageType']);
        }
    }
}
