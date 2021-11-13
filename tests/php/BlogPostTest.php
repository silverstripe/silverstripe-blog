<?php

namespace SilverStripe\Blog\Tests;

use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Security\Member;
use SilverStripe\Versioned\Versioned;

class BlogPostTest extends SapphireTest
{
    protected static $fixture_file = 'blog.yml';

    protected function tearDown(): void
    {
        DBDatetime::clear_mock_now();
        parent::tearDown();
    }

    /**
     * @dataProvider canViewProvider
     */
    public function testCanView($date, $user, $page, $canView, $stage)
    {
        $userRecord = $this->objFromFixture(Member::class, $user);
        $pageRecord = $this->objFromFixture(BlogPost::class, $page);
        DBDatetime::set_mock_now($date);
        if ($stage === 'Live') {
            $pageRecord->publishSingle();
        }

        Versioned::set_stage($stage);
        $this->assertEquals($canView, $pageRecord->canView($userRecord));
    }

    /**
     * @return array Format:
     *  - mock now date
     *  - user role (see fixture)
     *  - blog post fixture ID
     *  - expected result
     *  - versioned stage
     */
    public function canViewProvider()
    {
        $someFutureDate = '2013-10-10 20:00:00';
        $somePastDate = '2009-10-10 20:00:00';
        return [
            // Check this post given the date has passed
            [$someFutureDate, 'Editor', 'PostA', true, 'Stage'],
            [$someFutureDate, 'Contributor', 'PostA', true, 'Stage'],
            [$someFutureDate, 'BlogEditor', 'PostA', true, 'Stage'],
            [$someFutureDate, 'Writer', 'PostA', true, 'Stage'],

            // Check unpublished pages
            [$somePastDate, 'Editor', 'PostA', true, 'Stage'],
            [$somePastDate, 'Contributor', 'PostA', true, 'Stage'],
            [$somePastDate, 'BlogEditor', 'PostA', true, 'Stage'],
            [$somePastDate, 'Writer', 'PostA', true, 'Stage'],


            // Test a page that was authored by another user

            // Check this post given the date has passed
            [$someFutureDate, 'Editor', 'FirstBlogPost', true, 'Stage'],
            [$someFutureDate, 'Contributor', 'FirstBlogPost', true, 'Stage'],
            [$someFutureDate, 'BlogEditor', 'FirstBlogPost', true, 'Stage'],
            [$someFutureDate, 'Writer', 'FirstBlogPost', true, 'Stage'],

            // Check future pages in draft stage - users with "view draft pages" permission should
            // be able to see this, but visitors should not
            [$somePastDate, 'Editor', 'FirstBlogPost', true, 'Stage'],
            [$somePastDate, 'Contributor', 'FirstBlogPost', true, 'Stage'],
            [$somePastDate, 'BlogEditor', 'FirstBlogPost', true, 'Stage'],
            [$somePastDate, 'Writer', 'FirstBlogPost', true, 'Stage'],
            [$somePastDate, 'Visitor', 'FirstBlogPost', false, 'Stage'],

            // No future pages in live stage should be visible, even to users that can edit them (in draft)
            [$somePastDate, 'Editor', 'FirstBlogPost', false, 'Live'],
            [$somePastDate, 'Contributor', 'FirstBlogPost', false, 'Live'],
            [$somePastDate, 'BlogEditor', 'FirstBlogPost', false, 'Live'],
            [$somePastDate, 'Writer', 'FirstBlogPost', false, 'Live'],
            [$somePastDate, 'Visitor', 'FirstBlogPost', false, 'Live'],
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

    public function testMinutesToRead()
    {
        /** @var BlogPost $blogPost */
        $blogPost = $this->objFromFixture(BlogPost::class, 'FirstBlogPost');

        // over 400 words, should take slightly longer than 2 minutes
        $this->assertEquals(2, $blogPost->MinutesToRead());

        $blogPost = $this->objFromFixture(BlogPost::class, 'SecondBlogPost');

        // over 200 words, should take slighter longer than 1 minute
        $this->assertEquals(1, $blogPost->MinutesToRead());

        $blogPost = $this->objFromFixture(BlogPost::class, 'ThirdBlogPost');
        // less than 200 words, should take less than a minute thus return an integer of 0 (zero)
        $this->assertEquals(0, $blogPost->MinutesToRead());

        $this->expectException(\InvalidArgumentException::class);
        $blogPost->MinutesToRead('not-a-number');
    }

    /**
     * @param string $type
     * @param string $expected
     * @dataProvider monthlyArchiveLinkProvider
     * @group wip
     */
    public function testGetMonthlyArchiveLink($type, $expected)
    {
        /** @var BlogPost $blogPost */
        $blogPost = $this->objFromFixture(BlogPost::class, 'FirstBlogPost');

        $archiveLink = $blogPost->getMonthlyArchiveLink($type);
        $this->assertStringContainsString('archive/', $archiveLink);
        $this->assertStringEndsWith($expected, $archiveLink);
    }

    /**
     * @return array[]
     */
    public function monthlyArchiveLinkProvider()
    {
        return [
            ['day', '/2013/10/1'],
            ['month', '/2013/10'],
            ['year', '/2013'],
        ];
    }

    public function testGetYearlyArchiveLink()
    {
        /** @var BlogPost $blogPost */
        $blogPost = $this->objFromFixture(BlogPost::class, 'FirstBlogPost');

        $archiveLink = $blogPost->getYearlyArchiveLink();
        $this->assertStringContainsString('archive/', $archiveLink);
        $this->assertStringEndsWith('/2013', $archiveLink);
    }
}
