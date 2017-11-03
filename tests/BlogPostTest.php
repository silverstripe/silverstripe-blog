<?php

class BlogPostTest extends SapphireTest
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
     * @dataProvider canViewProvider
     */
    public function testCanView($date, $user, $page, $canView, $stage)
    {
        $userRecord = $this->objFromFixture('Member', $user);
        $pageRecord = $this->objFromFixture('BlogPost', $page);
        if ($stage === 'Live') {
            $pageRecord->doPublish();
        }

        Versioned::reading_stage($stage);
        SS_Datetime::set_mock_now($date);

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
        return array(
            // Check this post given the date has passed
            array($someFutureDate, 'Editor', 'PostA', true, 'Stage'),
            array($someFutureDate, 'Contributor', 'PostA', true, 'Stage'),
            array($someFutureDate, 'BlogEditor', 'PostA', true, 'Stage'),
            array($someFutureDate, 'Writer', 'PostA', true, 'Stage'),

            // Check unpublished pages
            array($somePastDate, 'Editor', 'PostA', true, 'Stage'),
            array($somePastDate, 'Contributor', 'PostA', true, 'Stage'),
            array($somePastDate, 'BlogEditor', 'PostA', true, 'Stage'),
            array($somePastDate, 'Writer', 'PostA', true, 'Stage'),

            // Test a page that was authored by another user

            // Check this post given the date has passed
            array($someFutureDate, 'Editor', 'FirstBlogPost', true, 'Stage'),
            array($someFutureDate, 'Contributor', 'FirstBlogPost', true, 'Stage'),
            array($someFutureDate, 'BlogEditor', 'FirstBlogPost', true, 'Stage'),
            array($someFutureDate, 'Writer', 'FirstBlogPost', true, 'Stage'),

            // Check future pages in draft stage - users with "view draft pages" permission should
            // be able to see this, but visitors should not
            array($somePastDate, 'Editor', 'FirstBlogPost', true, 'Stage'),
            array($somePastDate, 'Contributor', 'FirstBlogPost', true, 'Stage'),
            array($somePastDate, 'BlogEditor', 'FirstBlogPost', true, 'Stage'),
            array($somePastDate, 'Writer', 'FirstBlogPost', true, 'Stage'),
            array($somePastDate, 'Visitor', 'FirstBlogPost', false, 'Stage'),

            // No future pages in live stage should be visible, even to users that can edit them (in draft)
            array($somePastDate, 'Editor', 'FirstBlogPost', false, 'Live'),
            array($somePastDate, 'Contributor', 'FirstBlogPost', false, 'Live'),
            array($somePastDate, 'BlogEditor', 'FirstBlogPost', false, 'Live'),
            array($somePastDate, 'Writer', 'FirstBlogPost', false, 'Live'),
            array($somePastDate, 'Visitor', 'FirstBlogPost', false, 'Live'),
        );
    }

    public function testCandidateAuthors()
    {
        $blogpost = $this->objFromFixture('BlogPost', 'PostC');

        $this->assertEquals(7, $blogpost->getCandidateAuthors()->count());

        //Set the group to draw Members from
        Config::inst()->update('BlogPost', 'restrict_authors_to_group', 'blogusers');

        $this->assertEquals(3, $blogpost->getCandidateAuthors()->count());

        // Test cms field is generated
        $fields = $blogpost->getCMSFields();
        $this->assertNotEmpty($fields->dataFieldByName('Authors'));
    }

    public function testCanViewFuturePost()
    {
        $blogPost = $this->objFromFixture('BlogPost', 'NullPublishDate');

        $editor = $this->objFromFixture('Member', 'BlogEditor');
        $this->assertTrue($blogPost->canView($editor));

        $visitor = $this->objFromFixture('Member', 'Visitor');
        $this->assertFalse($blogPost->canView($visitor));
    }

    /**
     * The purpose of getDate() is to act as a proxy for PublishDate in the default RSS
     * template, rather than copying the entire template.
     */
    public function testGetDate()
    {
        $blogPost = $this->objFromFixture('BlogPost', 'NullPublishDate');
        $this->assertNull($blogPost->getDate());

        $blogPost = $this->objFromFixture('BlogPost', 'PostA');
        $this->assertEquals('2012-01-09 15:00:00', $blogPost->getDate());
    }
}
