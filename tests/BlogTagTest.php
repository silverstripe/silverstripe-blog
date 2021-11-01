<?php

namespace SilverStripe\Blog\Tests;

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\Blog\Model\BlogTag;
use SilverStripe\Control\Controller;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

class BlogTagTest extends FunctionalTest
{
    protected static $fixture_file = 'blog.yml';

    protected function setUp(): void
    {
        parent::setUp();

        DBDatetime::set_mock_now('2013-10-10 20:00:00');
    }

    protected function tearDown(): void
    {
        DBDatetime::clear_mock_now();

        parent::tearDown();
    }

    /**
     * Tests that any blog posts returned from $tag->BlogPosts() many_many are published, both by
     * normal 'save & publish' functionality and by publish date.
     */
    public function testBlogPosts()
    {
        $member = Security::getCurrentUser();

        if ($member) {
            Security::setCurrentUser(null);
        }

        $this->objFromFixture(BlogPost::class, 'FirstBlogPost');

        /**
         * @var BlogTag $tag
         */
        $tag = $this->objFromFixture(BlogTag::class, 'FirstTag');

        $this->assertEquals(1, $tag->BlogPosts()->count(), 'Tag blog post count');
    }

    /**
     * @see https://github.com/silverstripe/silverstripe-blog/issues/376
     */
    public function testAllowMultibyteUrlSegment()
    {
        $blog = $this->objFromFixture(Blog::class, 'FirstBlog');
        $tag = new BlogTag();
        $tag->BlogID = $blog->ID;
        $tag->Title = 'تست';
        $tag->write();
        // urlencoded
        $this->assertEquals('%D8%AA%D8%B3%D8%AA', $tag->URLSegment);
        $link = Controller::join_links($tag->Blog()->Link(), 'tag', '%D8%AA%D8%B3%D8%AA');
        $this->assertEquals($link, $tag->getLink());
    }

    /**
     * The first blog can be viewed by anybody.
     */
    public function testCanView()
    {
        $this->useDraftSite();

        $admin = $this->objFromFixture(Member::class, 'Admin');
        $editor = $this->objFromFixture(Member::class, 'Editor');

        $tag = $this->objFromFixture(BlogTag::class, 'FirstTag');

        $this->assertTrue($tag->canView($admin), 'Admin should be able to view tag.');
        $this->assertTrue($tag->canView($editor), 'Editor should be able to view tag.');

        $tag = $this->objFromFixture(BlogTag::class, 'SecondTag');

        $this->assertTrue($tag->canView($admin), 'Admin should be able to view tag.');
        $this->assertFalse($tag->canView($editor), 'Editor should not be able to view tag.');
    }

    public function testCanEdit()
    {
        $this->useDraftSite();

        $admin = $this->objFromFixture(Member::class, 'Admin');
        $editor = $this->objFromFixture(Member::class, 'Editor');

        $tag = $this->objFromFixture(BlogTag::class, 'FirstTag');

        $this->assertTrue($tag->canEdit($admin), 'Admin should be able to edit tag.');
        $this->assertTrue($tag->canEdit($editor), 'Editor should be able to edit tag.');

        $tag = $this->objFromFixture(BlogTag::class, 'SecondTag');

        $this->assertTrue($tag->canEdit($admin), 'Admin should be able to edit tag.');
        $this->assertFalse($tag->canEdit($editor), 'Editor should not be able to edit tag.');

        $tag = $this->objFromFixture(BlogTag::class, 'ThirdTag');

        $this->assertTrue($tag->canEdit($admin), 'Admin should always be able to edit tags.');
        $this->assertTrue($tag->canEdit($editor), 'Editor should be able to edit tag.');
    }

    public function testCanCreate()
    {
        $this->useDraftSite();

        $admin = $this->objFromFixture(Member::class, 'Admin');
        $editor = $this->objFromFixture(Member::class, 'Editor');

        $tag = singleton(BlogTag::class);

        $this->assertTrue($tag->canCreate($admin), 'Admin should be able to create tag.');
        $this->assertTrue($tag->canCreate($editor), 'Editor should be able to create tag.');
    }

    public function testCanDelete()
    {
        $this->useDraftSite();

        $admin = $this->objFromFixture(Member::class, 'Admin');
        $editor = $this->objFromFixture(Member::class, 'Editor');

        $tag = $this->objFromFixture(BlogTag::class, 'FirstTag');

        $this->assertTrue($tag->canDelete($admin), 'Admin should be able to delete tag.');
        $this->assertTrue($tag->canDelete($editor), 'Editor should be able to delete tag.');

        $tag = $this->objFromFixture(BlogTag::class, 'SecondTag');

        $this->assertTrue($tag->canDelete($admin), 'Admin should be able to delete tag.');
        $this->assertFalse($tag->canDelete($editor), 'Editor should not be able to delete tag.');

        $tag = $this->objFromFixture(BlogTag::class, 'ThirdTag');

        $this->assertTrue($tag->canDelete($admin), 'Admin should always be able to delete tags.');
        $this->assertTrue($tag->canDelete($editor), 'Editor should be able to delete tag.');
    }

    public function testDuplicateTagsForURLSegment()
    {
        $blog = new Blog();
        $blog->Title = 'Testing for duplicates blog';
        $blog->write();
        $tag1 = new BlogTag();
        $tag1->Title = 'cat-test';
        $tag1->BlogID = $blog->ID;
        $tag1->write();
        $this->assertEquals('cat-test', $tag1->URLSegment);

        $tag2 = new BlogTag();
        $tag2->Title = 'cat test';
        $tag2->BlogID = $blog->ID;
        $tag2->write();
        $this->assertEquals('cat-test-1', $tag2->URLSegment);
    }

    public function testDuplicateTags()
    {
        $blog = new Blog();
        $blog->Title = 'Testing for duplicate tags';
        $blog->write();

        $tag = new BlogTag();
        $tag->Title = 'Test';
        $tag->BlogID = $blog->ID;
        $tag->URLSegment = 'test';
        $tag->write();

        $tag = new BlogTag();
        $tag->Title = 'Test';
        $tag->URLSegment = 'test';
        $tag->BlogID = $blog->ID;
        try {
            $tag->write();
            $this->fail('Duplicate BlogTag written');
        } catch (ValidationException $e) {
            $messages = $e->getResult()->getMessages();
            $this->assertCount(1, $messages);
            $this->assertEquals(BlogTag::DUPLICATE_EXCEPTION, $messages[0]['messageType']);
        }
    }

    public function testBlogTagUrlSegmentsAreAutomaticallyUpdated()
    {
        $tag = new BlogTag;
        $tag->Title = "a test";
        $tag->write();
        $this->assertEquals($tag->URLSegment, "a-test");

        $tag->Title = "another test";
        $tag->write();
        $this->assertEquals($tag->URLSegment, "another-test");
    }
}
