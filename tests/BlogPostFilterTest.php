<?php

use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Security\Member;
use SilverStripe\Dev\SapphireTest;

/**
 * @mixin PHPUnit_Framework_TestCase
 */
class BlogPostFilterTest extends SapphireTest
{
    /**
     * @var string
     */
    public static $fixture_file = 'blog.yml';

    public function setUp()
    {
        parent::setUp();

        DBDatetime::set_mock_now('2013-10-10 20:00:00');
    }

    public function tearDown()
    {
        DBDatetime::clear_mock_now();

        parent::tearDown();
    }

    public function testFilter()
    {
        $member = Member::currentUser();

        if ($member) {
            $member->logout();
        }

        /**
         * @var Blog $blog
         */
        $blog = $this->objFromFixture('Blog', 'FirstBlog');

        $this->assertEquals(3, $blog->AllChildren()->Count(), 'Filtered blog posts');

        DBDatetime::set_mock_now('2020-01-01 00:00:00');

        $this->assertEquals(5, $blog->AllChildren()->Count(), 'Unfiltered blog posts');
    }
}
