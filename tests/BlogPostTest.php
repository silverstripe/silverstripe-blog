<?php

namespace SilverStripe\Blog\Tests;

use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Security\Member;

class BlogPostTest extends SapphireTest
{
    /**
     * {@inheritDoc}
     * @var string
     */
    protected static $fixture_file = 'blog.yml';

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        DBDatetime::clear_mock_now();
        parent::tearDown();
    }

    /**
     * @dataProvider canViewProvider
     */
    public function testCanView($date, $user, $page, $canView)
    {
        $userRecord = $this->objFromFixture(Member::class, $user);
        $pageRecord = $this->objFromFixture(BlogPost::class, $page);
        DBDatetime::set_mock_now($date);
        $this->assertEquals($canView, $pageRecord->canView($userRecord));
    }

    /**
     * @return array
     */
    public function canViewProvider()
    {
        $someFutureDate = '2013-10-10 20:00:00';
        $somePastDate = '2009-10-10 20:00:00';
        return [
            // Check this post given the date has passed
            [$someFutureDate, 'Editor', 'PostA', true],
            [$someFutureDate, 'Contributor', 'PostA', true],
            [$someFutureDate, 'BlogEditor', 'PostA', true],
            [$someFutureDate, 'Writer', 'PostA', true],

            // Check unpublished pages
            [$somePastDate, 'Editor', 'PostA', true],
            [$somePastDate, 'Contributor', 'PostA', true],
            [$somePastDate, 'BlogEditor', 'PostA', true],
            [$somePastDate, 'Writer', 'PostA', true],

            // Test a page that was authored by another user

            // Check this post given the date has passed
            [$someFutureDate, 'Editor', 'FirstBlogPost', true],
            [$someFutureDate, 'Contributor', 'FirstBlogPost', true],
            [$someFutureDate, 'BlogEditor', 'FirstBlogPost', true],
            [$someFutureDate, 'Writer', 'FirstBlogPost', true],

            // Check future pages - non-editors shouldn't be able to see this
            [$somePastDate, 'Editor', 'FirstBlogPost', true],
            [$somePastDate, 'Contributor', 'FirstBlogPost', false],
            [$somePastDate, 'BlogEditor', 'FirstBlogPost', false],
            [$somePastDate, 'Writer', 'FirstBlogPost', false],
        ];
    }

    public function testCandidateAuthors()
    {
        $blogpost = $this->objFromFixture(BlogPost::class, 'PostC');

        $this->assertEquals(7, $blogpost->getCandidateAuthors()->count());

        //Set the group to draw Members from
        Config::inst()->update(BlogPost::class, 'restrict_authors_to_group', 'blogusers');

        $this->assertEquals(3, $blogpost->getCandidateAuthors()->count());

        // Test cms field is generated
        $fields = $blogpost->getCMSFields();
        $this->assertNotEmpty($fields->dataFieldByName('Authors'));
    }

    public function testCanViewFuturePost()
    {
        $blogPost = $this->objFromFixture(BlogPost::class, 'NullPublishDate');

        $editor = $this->objFromFixture(Member::class, 'BlogEditor');
        $this->assertTrue($blogPost->canView($editor));

        $visitor = $this->objFromFixture(Member::class, 'Visitor');
        $this->assertFalse($blogPost->canView($visitor));
    }

    /**
     * The purpose of getDate() is to act as a proxy for PublishDate in the default RSS
     * template, rather than copying the entire template.
     */
    public function testGetDate()
    {
        $blogPost = $this->objFromFixture(BlogPost::class, 'NullPublishDate');
        $this->assertNull($blogPost->getDate());

        $blogPost = $this->objFromFixture(BlogPost::class, 'PostA');
        $this->assertEquals('2012-01-09 15:00:00', $blogPost->getDate());
    }
}
