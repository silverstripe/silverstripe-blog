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
        $userRecord = $this->objFromFixture('Member', $user);
        $pageRecord = $this->objFromFixture('SiteTree', $page);
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
        return array(
            // Check this post given the date has passed
            array($someFutureDate, 'Editor', 'PostA', true),
            array($someFutureDate, 'Contributor', 'PostA', true),
            array($someFutureDate, 'BlogEditor', 'PostA', true),
            array($someFutureDate, 'Writer', 'PostA', true),

            // Check unpublished pages
            array($somePastDate, 'Editor', 'PostA', true),
            array($somePastDate, 'Contributor', 'PostA', true),
            array($somePastDate, 'BlogEditor', 'PostA', true),
            array($somePastDate, 'Writer', 'PostA', true),

            // Test a page that was authored by another user

            // Check this post given the date has passed
            array($someFutureDate, 'Editor', 'FirstBlogPost', true),
            array($someFutureDate, 'Contributor', 'FirstBlogPost', true),
            array($someFutureDate, 'BlogEditor', 'FirstBlogPost', true),
            array($someFutureDate, 'Writer', 'FirstBlogPost', true),

            // Check future pages - non-editors shouldn't be able to see this
            array($somePastDate, 'Editor', 'FirstBlogPost', true),
            array($somePastDate, 'Contributor', 'FirstBlogPost', false),
            array($somePastDate, 'BlogEditor', 'FirstBlogPost', false),
            array($somePastDate, 'Writer', 'FirstBlogPost', false),
        );
    }

    public function testCandidateAuthors()
    {
        $blogpost = $this->objFromFixture('SiteTree', 'PostC');

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
        $blogPost = $this->objFromFixture('SiteTree', 'NullPublishDate');

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
        $blogPost = $this->objFromFixture('SiteTree', 'NullPublishDate');
        $this->assertNull($blogPost->getDate());

        $blogPost = $this->objFromFixture('SiteTree', 'PostA');
        $this->assertEquals('2012-01-09 15:00:00', $blogPost->getDate());
    }
}
