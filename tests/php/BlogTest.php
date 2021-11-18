<?php

namespace SilverStripe\Blog\Tests;

use SilverStripe\Control\HTTPResponse_Exception;
use SilverStripe\Blog\Model\Blog;
use SilverStripe\Blog\Model\BlogController;
use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Session;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\SS_List;
use SilverStripe\Security\Member;

class BlogTest extends SapphireTest
{
    protected static $fixture_file = 'blog.yml';

    protected function setUp(): void
    {
        parent::setUp();

        DBDatetime::set_mock_now('2013-10-10 20:00:00');

        /**
         * @var Blog $blog
         */
        $blog = $this->objFromFixture(Blog::class, 'FirstBlog');
        $blog->publishRecursive();
    }

    protected function tearDown(): void
    {
        DBDatetime::clear_mock_now();

        parent::tearDown();
    }

    public function testGetExcludedSiteTreeClassNames()
    {
        $this->logOut();

        /**
         * @var Blog $blog
         */
        $blog = $this->objFromFixture(Blog::class, 'FirstBlog');

        Config::inst()->update(BlogPost::class, 'show_in_sitetree', true);
        $classes = $blog->getExcludedSiteTreeClassNames();

        $this->assertNotContains(BlogPost::class, $classes, 'BlogPost class should be hidden.');

        Config::inst()->update(BlogPost::class, 'show_in_sitetree', false);
        $classes = $blog->getExcludedSiteTreeClassNames();

        $this->assertContains(BlogPost::class, $classes, 'BlogPost class should be hidden.');
    }

    public function testGetArchivedBlogPosts()
    {
        $this->logOut();

        /**
         * @var Blog $blog
         */
        $blog = $this->objFromFixture(Blog::class, 'FirstBlog');

        $archive = $blog->getArchivedBlogPosts(2013);

        $this->assertEquals(2, $archive->count(), 'Incorrect Yearly Archive count for 2013');
        $this->assertEquals('First Post', $archive->first()->Title, 'Incorrect First Blog post');
        $this->assertEquals('Second Post', $archive->last()->Title, 'Incorrect Last Blog post');

        $archive = $blog->getArchivedBlogPosts(2013, 10);

        $this->assertEquals(1, $archive->count(), 'Incorrect monthly archive count.');

        $archive = $blog->getArchivedBlogPosts(2013, 10, 01);

        $this->assertEquals(1, $archive->count(), 'Incorrect daily archive count.');
    }

    public function testArchiveLinks()
    {
        /**
         * @var Blog $blog
         */
        $blog = $this->objFromFixture(Blog::class, 'FirstBlog');

        $link = Controller::join_links($blog->Link('archive'), '2013', '10', '01');

        $this->assertEquals(200, $this->getStatusOf($link), 'HTTP Status should be 200');

        $link = Controller::join_links($blog->Link('archive'), '2013', '10');

        $this->assertEquals(200, $this->getStatusOf($link), 'HTTP Status should be 200');

        $link = Controller::join_links($blog->Link('archive'), '2013');

        $this->assertEquals(200, $this->getStatusOf($link), 'HTTP Status should be 200');

        $link = Controller::join_links($blog->Link('archive'), '2011', '10', '01');

        $this->assertEquals(200, $this->getStatusOf($link), 'HTTP Status should be 200');

        $link = Controller::join_links($blog->Link('archive'));
        $this->assertEquals(200, $this->getStatusOf($link), 'HTTP Status should be 200');

        $link = Controller::join_links($blog->Link('archive'), 'invalid-year');

        $this->assertEquals(404, $this->getStatusOf($link), 'HTTP Status should be 404');

        $link = Controller::join_links($blog->Link('archive'), '2013', '99');

        $this->assertEquals(404, $this->getStatusOf($link), 'HTTP Status should be 404');

        $link = Controller::join_links($blog->Link('archive'), '2013', '10', '99');

        $this->assertEquals(404, $this->getStatusOf($link), 'HTTP Status should be 404');
    }

    /*
     * Test archive year
     */
    public function testArchiveYear()
    {
        $blog = $this->objFromFixture(Blog::class, 'FirstBlog');
        $controller = new BlogController($blog);
        $this->requestURL($controller, 'first-post/archive/');
        $this->assertEquals(2013, $controller->getArchiveYear(), 'getArchiveYear should return 2013');
    }

    /**
     * @param string $link
     *
     * @return int
     */
    protected function getStatusOf($link)
    {
        return Director::test($link)->getStatusCode();
    }

    public function testRoles()
    {
        /**
         * @var Blog $firstBlog
         */
        $firstBlog = $this->objFromFixture(Blog::class, 'FirstBlog');

        /**
         * @var Blog $fourthBlog
         */
        $fourthBlog = $this->objFromFixture(Blog::class, 'FourthBlog');

        /**
         * @var BlogPost $postA
         */
        $postA = $this->objFromFixture(BlogPost::class, 'PostA');

        /**
         * @var BlogPost $postB
         */
        $postB = $this->objFromFixture(BlogPost::class, 'PostB');

        /**
         * @var BlogPost $postC
         */
        $postC = $this->objFromFixture(BlogPost::class, 'PostC');

        /**
         * @var Member $editor
         */
        $editor = $this->objFromFixture(Member::class, 'BlogEditor');

        /**
         * @var Member $writer
         */
        $writer = $this->objFromFixture(Member::class, 'Writer');

        /**
         * @var Member $contributor
         */
        $contributor = $this->objFromFixture(Member::class, 'Contributor');

        /**
         * @var Member $visitor
         */
        $visitor = $this->objFromFixture(Member::class, 'Visitor');

        $this->assertEquals('Editor', $fourthBlog->RoleOf($editor));
        $this->assertEquals('Contributor', $fourthBlog->RoleOf($contributor));
        $this->assertEquals('Writer', $fourthBlog->RoleOf($writer));
        $this->assertEmpty($fourthBlog->RoleOf($visitor));
        $this->assertEquals('Author', $postA->RoleOf($writer));
        $this->assertEquals('Author', $postA->RoleOf($contributor));
        $this->assertEquals('Editor', $postA->RoleOf($editor));
        $this->assertEmpty($postA->RoleOf($visitor));

        // Test RoleOf with string values given
        $this->assertEquals('Editor', $fourthBlog->RoleOf((string)(int)$editor->ID));
        $this->assertEquals('Contributor', $fourthBlog->RoleOf((string)(int)$contributor->ID));
        $this->assertEquals('Writer', $fourthBlog->RoleOf((string)(int)$writer->ID));
        $this->assertEmpty($fourthBlog->RoleOf((string)(int)$visitor->ID));
        $this->assertEquals('Author', $postA->RoleOf((string)(int)$writer->ID));
        $this->assertEquals('Author', $postA->RoleOf((string)(int)$contributor->ID));
        $this->assertEquals('Editor', $postA->RoleOf((string)(int)$editor->ID));
        $this->assertEmpty($postA->RoleOf((string)(int)$visitor->ID));

        // Test RoleOf with int values given
        $this->assertEquals('Editor', $fourthBlog->RoleOf((int)$editor->ID));
        $this->assertEquals('Contributor', $fourthBlog->RoleOf((int)$contributor->ID));
        $this->assertEquals('Writer', $fourthBlog->RoleOf((int)$writer->ID));
        $this->assertEmpty($fourthBlog->RoleOf((int)$visitor->ID));
        $this->assertEquals('Author', $postA->RoleOf((int)$writer->ID));
        $this->assertEquals('Author', $postA->RoleOf((int)$contributor->ID));
        $this->assertEquals('Editor', $postA->RoleOf((int)$editor->ID));
        $this->assertEmpty($postA->RoleOf((int)$visitor->ID));

        $this->assertTrue($fourthBlog->canEdit($editor));
        $this->assertFalse($firstBlog->canEdit($editor));
        $this->assertTrue($fourthBlog->canAddChildren($editor));
        $this->assertFalse($firstBlog->canAddChildren($editor));
        $this->assertTrue($postA->canEdit($editor));
        $this->assertTrue($postB->canEdit($editor));
        $this->assertTrue($postC->canEdit($editor));
        $this->assertTrue($postA->canPublish($editor));
        $this->assertTrue($postB->canPublish($editor));
        $this->assertTrue($postC->canPublish($editor));

        $this->assertFalse($fourthBlog->canEdit($writer));
        $this->assertFalse($firstBlog->canEdit($writer));
        $this->assertTrue($fourthBlog->canAddChildren($writer));
        $this->assertFalse($firstBlog->canAddChildren($writer));
        $this->assertTrue($postA->canEdit($writer));
        $this->assertFalse($postB->canEdit($writer));
        $this->assertTrue($postC->canEdit($writer));
        $this->assertTrue($postA->canPublish($writer));
        $this->assertFalse($postB->canPublish($writer));
        $this->assertTrue($postC->canPublish($writer));

        $this->assertFalse($fourthBlog->canEdit($contributor));
        $this->assertFalse($firstBlog->canEdit($contributor));
        $this->assertTrue($fourthBlog->canAddChildren($contributor));
        $this->assertFalse($firstBlog->canAddChildren($contributor));
        $this->assertTrue($postA->canEdit($contributor));
        $this->assertFalse($postB->canEdit($contributor));
        $this->assertTrue($postC->canEdit($contributor));
        $this->assertFalse($postA->canPublish($contributor));
        $this->assertFalse($postB->canPublish($contributor));
        $this->assertFalse($postC->canPublish($contributor));

        $this->assertFalse($fourthBlog->canEdit($visitor));
        $this->assertFalse($firstBlog->canEdit($visitor));
        $this->assertFalse($fourthBlog->canAddChildren($visitor));
        $this->assertFalse($firstBlog->canAddChildren($visitor));
        $this->assertFalse($postA->canEdit($visitor));
        $this->assertFalse($postB->canEdit($visitor));
        $this->assertFalse($postC->canEdit($visitor));
        $this->assertFalse($postA->canPublish($visitor));
        $this->assertFalse($postB->canPublish($visitor));
        $this->assertFalse($postC->canPublish($visitor));
    }

    public function testFilteredCategoriesRoot()
    {
        $blog = $this->objFromFixture(Blog::class, 'FirstBlog');
        $controller = new BlogController($blog);
        $this->requestURL($controller, 'first-post');
        $this->assertIDsEquals(
            $blog->AllChildren()->column('ID'),
            $controller->PaginatedList()->column('ID')
        );
    }

    public function testFilteredCategoriesRSS()
    {
        $blog = $this->objFromFixture(Blog::class, 'FirstBlog');
        $controller = new BlogController($blog);
        $this->requestURL($controller, 'first-post/rss');
        $this->assertIDsEquals(
            $blog->AllChildren()->column('ID'),
            $controller->PaginatedList()->column('ID')
        );
    }

    public function testFilteredCategoriesTags()
    {
        $blog = $this->objFromFixture(Blog::class, 'FirstBlog');
        $controller = new BlogController($blog);

        // Posts
        $firstPostID = $this->idFromFixture(BlogPost::class, 'FirstBlogPost');
        $firstFuturePostID = $this->idFromFixture(BlogPost::class, 'FirstFutureBlogPost');
        $secondFuturePostID = $this->idFromFixture(BlogPost::class, 'SecondFutureBlogPost');

        // Request first tag
        $this->requestURL($controller, 'first-post/tag/first-tag');
        $this->assertIDsEquals(
            [$firstPostID, $firstFuturePostID, $secondFuturePostID],
            $controller->PaginatedList()
        );
    }

    public function testFilteredCategoriesArchive()
    {
        $blog = $this->objFromFixture(Blog::class, 'FirstBlog');
        $controller = new BlogController($blog);

        // Posts
        $firstPostID = $this->idFromFixture(BlogPost::class, 'FirstBlogPost');
        $secondPostID = $this->idFromFixture(BlogPost::class, 'SecondBlogPost');
        $secondFuturePostID = $this->idFromFixture(BlogPost::class, 'SecondFutureBlogPost');

        // Request 2013 posts
        $this->requestURL($controller, 'first-post/archive/2013');
        $this->assertIDsEquals(
            [$firstPostID, $secondPostID, $secondFuturePostID],
            $controller->PaginatedList()
        );
    }

    public function testDisabledProfiles()
    {
        $this->expectException(HTTPResponse_Exception::class);
        $this->expectExceptionCode(404);
        Config::modify()->set(BlogController::class, 'disable_profiles', true);

        $controller = BlogController::create();
        $controller->setRequest(Controller::curr()->getRequest());
        $controller->profile();
    }

    /**
     * Mock a request against a given controller
     *
     * @param ContentController $controller
     * @param string $url
     */
    protected function requestURL(ContentController $controller, $url)
    {
        $request = new HTTPRequest('get', $url);
        $request->match('$URLSegment//$Action/$ID/$OtherID');
        $request->shift();
        $session = new Session(null);
        $session->start($request);
        $request->setSession($session);
        $controller->doInit();
        $controller->handleRequest($request);
        $session->clearAll();
        $session->destroy();
    }

    /**
     * Assert these id lists match
     *
     * @param array|SS_List $left
     * @param array|SS_List $right
     */
    protected function assertIDsEquals($left, $right)
    {
        if ($left instanceof SS_List) {
            $left = $left->column('ID');
        }
        if ($right instanceof SS_List) {
            $right = $right->column('ID');
        }
        asort($left);
        asort($right);
        $this->assertEquals(array_values($left), array_values($right));
    }
}
