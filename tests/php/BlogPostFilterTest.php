<?php

namespace SilverStripe\Blog\Tests;

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Security\Security;

/**
 * @coversDefaultClass \SilverStripe\Blog\Model\BlogPostFilter
 */
class BlogPostFilterTest extends SapphireTest
{
    /**
     * @var string
     */
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
     * Tests that unpublished articles are not returned
     * @covers ::augmentSQL
     */
    public function testFilter()
    {
        $member = Security::getCurrentUser();

        if ($member) {
            Security::setCurrentUser(null);
        }

        /**
         * @var Blog $blog
         */
        $blog = $this->objFromFixture(Blog::class, 'FirstBlog');

        $this->assertEquals(3, $blog->AllChildren()->Count(), 'Filtered blog posts');

        DBDatetime::set_mock_now('2020-01-01 00:00:00');

        $this->assertEquals(5, $blog->AllChildren()->Count(), 'Unfiltered blog posts');
    }
}
