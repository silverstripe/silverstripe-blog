<?php

/**
 * @mixin PHPUnit_Framework_TestCase
 */
class BlogTagTest extends FunctionalTest
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

        SS_Datetime::set_mock_now('2013-10-10 20:00:00');
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        SS_Datetime::clear_mock_now();

        parent::tearDown();
    }

    /**
     * Tests that any blog posts returned from $tag->BlogPosts() many_many are published, both by
     * normal 'save & publish' functionality and by publish date.
     */
    public function testBlogPosts()
    {
        $member = Member::currentUser();

        if ($member) {
            $member->logout();
        }

        $this->objFromFixture('BlogPost', 'FirstBlogPost');

        /**
         * @var BlogTag $tag
         */
        $tag = $this->objFromFixture('BlogTag', 'FirstTag');

        $this->assertEquals(1, $tag->BlogPosts()->count(), 'Tag blog post count');
    }

    /**
     * The first blog can be viewed by anybody.
     */
    public function testCanView()
    {
        $this->useDraftSite();

        $admin = $this->objFromFixture('Member', 'Admin');
        $editor = $this->objFromFixture('Member', 'Editor');

        $tag = $this->objFromFixture('BlogTag', 'FirstTag');

        $this->assertTrue($tag->canView($admin), 'Admin should be able to view tag.');
        $this->assertTrue($tag->canView($editor), 'Editor should be able to view tag.');

        $tag = $this->objFromFixture('BlogTag', 'SecondTag');

        $this->assertTrue($tag->canView($admin), 'Admin should be able to view tag.');
        $this->assertFalse($tag->canView($editor), 'Editor should not be able to view tag.');
    }

    public function testCanEdit()
    {
        $this->useDraftSite();

        $admin = $this->objFromFixture('Member', 'Admin');
        $editor = $this->objFromFixture('Member', 'Editor');

        $tag = $this->objFromFixture('BlogTag', 'FirstTag');

        $this->assertTrue($tag->canEdit($admin), 'Admin should be able to edit tag.');
        $this->assertTrue($tag->canEdit($editor), 'Editor should be able to edit tag.');

        $tag = $this->objFromFixture('BlogTag', 'SecondTag');

        $this->assertTrue($tag->canEdit($admin), 'Admin should be able to edit tag.');
        $this->assertFalse($tag->canEdit($editor), 'Editor should not be able to edit tag.');

        $tag = $this->objFromFixture('BlogTag', 'ThirdTag');

        $this->assertTrue($tag->canEdit($admin), 'Admin should always be able to edit tags.');
        $this->assertTrue($tag->canEdit($editor), 'Editor should be able to edit tag.');
    }

    public function testCanCreate()
    {
        $this->useDraftSite();

        $admin = $this->objFromFixture('Member', 'Admin');
        $editor = $this->objFromFixture('Member', 'Editor');

        $tag = singleton('BlogTag');

        $this->assertTrue($tag->canCreate($admin), 'Admin should be able to create tag.');
        $this->assertTrue($tag->canCreate($editor), 'Editor should be able to create tag.');
    }

    public function testCanDelete()
    {
        $this->useDraftSite();

        $admin = $this->objFromFixture('Member', 'Admin');
        $editor = $this->objFromFixture('Member', 'Editor');

        $tag = $this->objFromFixture('BlogTag', 'FirstTag');

        $this->assertTrue($tag->canDelete($admin), 'Admin should be able to delete tag.');
        $this->assertTrue($tag->canDelete($editor), 'Editor should be able to delete tag.');

        $tag = $this->objFromFixture('BlogTag', 'SecondTag');

        $this->assertTrue($tag->canDelete($admin), 'Admin should be able to delete tag.');
        $this->assertFalse($tag->canDelete($editor), 'Editor should not be able to delete tag.');

        $tag = $this->objFromFixture('BlogTag', 'ThirdTag');

        $this->assertTrue($tag->canDelete($admin), 'Admin should always be able to delete tags.');
        $this->assertTrue($tag->canDelete($editor), 'Editor should be able to delete tag.');
    }

    public function testDuplicateTagsForURLSegment() {
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
        $this->assertEquals('cat-test-0', $tag2->URLSegment);

    }

    public function testDuplicateTags() {
        $blog = new Blog();
        $blog->Title = 'Testing for duplicate tags';
        $blog->write();

        $tag = new BlogTag();
        $tag->Title = 'Test';
        $tag->BlogID = $blog->ID;
        $tag->write();

        $tag = new BlogTag();
        $tag->Title = 'Test';
        $tag->BlogID = $blog->ID;
        try {
            $tag->write();
            $this->fail('Duplicate BlogTag written');
        } catch (ValidationException $e) {
            $codeList = $e->getResult()->codeList();
            $this->assertCount(1, $codeList);
            $this->assertEquals(BlogTag::DUPLICATE_EXCEPTION, $codeList[0]);
        }
    }

}
