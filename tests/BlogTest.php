<?php

class BlogTest extends SapphireTest {

	static $fixture_file = "blog.yml";

	public function setUp() {
		parent::setUp();
		Config::nest();
		SS_Datetime::set_mock_now("2013-10-10 20:00:00");
		$this->objFromFixture("Blog", "firstblog")->publish("Stage", "Live");
	}

	public function tearDown() {
		SS_Datetime::clear_mock_now();
		Config::unnest();
		parent::tearDown();
	}
	
	public function testGetExcludedSiteTreeClassNames() {
		$member = Member::currentUser();
		if($member) $member->logout();

		$blog = $this->objFromFixture("Blog", 'firstblog');

		Config::inst()->update("BlogPost", "show_in_sitetree", true);
		$classes = $blog->getExcludedSiteTreeClassNames();
		$this->assertNotContains('BlogPost', $classes, "BlogPost class should be hidden.");

		Config::inst()->update("BlogPost", "show_in_sitetree", false);
		$classes = $blog->getExcludedSiteTreeClassNames();
		$this->assertContains('BlogPost', $classes, "BlogPost class should be hidden.");
	}



	public function testGetArchivedBlogPosts() {
		$member = Member::currentUser();
		if($member) $member->logout();

		$blog = $this->objFromFixture("Blog", "firstblog");

		// Test yearly
		$archive = $blog->getArchivedBlogPosts(2013);
		$this->assertEquals(2, $archive->count(), "Incorrect Yearly Archive count for 2013");
		$this->assertEquals("First post", $archive->first()->Title, "Incorrect First Blog post");
		$this->assertEquals("Second post", $archive->last()->Title, "Incorrect Last Blog post");

		// Test monthly
		$archive = $blog->getArchivedBlogPosts(2013, 10);
		$this->assertEquals(1, $archive->count(), "Incorrect monthly acrhive count.");

		// Test daily
		$archive = $blog->getArchivedBlogPosts(2013, 10, 01);
		$this->assertEquals(1, $archive->count(), "Incorrect daily archive count.");
	}


	public function testArchiveLinks() {
		$blog = $this->objFromFixture("Blog", "firstblog");

		// Test valid links
		$archiveLink = Controller::join_links($blog->Link("archive"), 2013, 10, 01);
		$response = Director::test($archiveLink);
		$this->assertEquals(200, $response->getStatusCode(), "HTTP Status should be 200");

		 $archiveLink = Controller::join_links($blog->Link("archive"), 2013, 10);
		 $response = Director::test($archiveLink);
		 $this->assertEquals(200, $response->getStatusCode(), "HTTP Status should be 200");

		 $archiveLink = Controller::join_links($blog->Link("archive"), 2013);
		 $response = Director::test($archiveLink);
		 $this->assertEquals(200, $response->getStatusCode(), "HTTP Status should be 200");

		 $archiveLink = Controller::join_links($blog->Link("archive"), 2011, 10, 01);
		 $response = Director::test($archiveLink); // No posts on this date, but a valid entry.
		 $this->assertEquals(200, $response->getStatusCode(), "HTTP Status should be 200");


		 // Test invalid links & dates
		 $response = Director::test($blog->Link("archive")); // 404 when no date is set
		 $this->assertEquals(404, $response->getStatusCode(), "HTTP Status should be 404");

		 // Invalid year
		 $archiveLink = Controller::join_links($blog->Link("archive"), "invalid-year");
		 $response = Director::test($archiveLink); // 404 when an invalid yer is set
		 $this->assertEquals(404, $response->getStatusCode(), "HTTP Status should be 404");

		 // Invalid month
		 $archiveLink = Controller::join_links($blog->Link("archive"), "2013", "99");
		 $response = Director::test($archiveLink); // 404 when an invalid month is set
		 $this->assertEquals(404, $response->getStatusCode(), "HTTP Status should be 404");

		 // Invalid day
		 $archiveLink = Controller::join_links($blog->Link("archive"), "2013", "10", "99");
		 $response = Director::test($archiveLink); // 404 when an invalid day is set
		 $this->assertEquals(404, $response->getStatusCode(), "HTTP Status should be 404");

	}

	public function testRoles() {
		$blog = $this->objFromFixture('Blog', 'fourthblog');
		$blog2 = $this->objFromFixture('Blog', 'firstblog');
		$postA = $this->objFromFixture('BlogPost', 'post-a');
		$postB = $this->objFromFixture('BlogPost', 'post-b');
		$postC = $this->objFromFixture('BlogPost', 'post-c');
		$editor = $this->objFromFixture('Member', 'blogeditor');
		$writer = $this->objFromFixture('Member', 'writer');
		$contributor = $this->objFromFixture('Member', 'contributor');
		$visitor = $this->objFromFixture('Member', 'visitor');

		// Check roleof
		$this->assertEquals('Editor', $blog->RoleOf($editor));
		$this->assertEquals('Contributor', $blog->RoleOf($contributor));
		$this->assertEquals('Writer', $blog->RoleOf($writer));
		$this->assertEmpty($blog->RoleOf($visitor));
		$this->assertEquals('Author', $postA->RoleOf($writer));
		$this->assertEquals('Author', $postA->RoleOf($contributor));
		$this->assertEquals('Editor', $postA->RoleOf($editor));
		$this->assertEmpty($postA->RoleOf($visitor));

		// Check that editors have all permissions on their own blog
		$this->assertTrue($blog->canEdit($editor));
		$this->assertFalse($blog2->canEdit($editor));
		$this->assertTrue($blog->canAddChildren($editor));
		$this->assertFalse($blog2->canAddChildren($editor));
		$this->assertTrue($postA->canEdit($editor));
		$this->assertTrue($postB->canEdit($editor));
		$this->assertTrue($postC->canEdit($editor));
		$this->assertTrue($postA->canPublish($editor));
		$this->assertTrue($postB->canPublish($editor));
		$this->assertTrue($postC->canPublish($editor));

		// check rights of writers
		$this->assertFalse($blog->canEdit($writer));
		$this->assertFalse($blog2->canEdit($writer));
		$this->assertTrue($blog->canAddChildren($writer));
		$this->assertFalse($blog2->canAddChildren($writer));
		$this->assertTrue($postA->canEdit($writer));
		$this->assertFalse($postB->canEdit($writer));
		$this->assertTrue($postC->canEdit($writer));
		$this->assertTrue($postA->canPublish($writer));
		$this->assertFalse($postB->canPublish($writer));
		$this->assertTrue($postC->canPublish($writer));

		// Check rights of contributors
		$this->assertFalse($blog->canEdit($contributor));
		$this->assertFalse($blog2->canEdit($contributor));
		$this->assertTrue($blog->canAddChildren($contributor));
		$this->assertFalse($blog2->canAddChildren($contributor));
		$this->assertTrue($postA->canEdit($contributor));
		$this->assertFalse($postB->canEdit($contributor));
		$this->assertTrue($postC->canEdit($contributor));
		$this->assertFalse($postA->canPublish($contributor));
		$this->assertFalse($postB->canPublish($contributor));
		$this->assertFalse($postC->canPublish($contributor));

		// Check rights of non-cms user
		$this->assertFalse($blog->canEdit($visitor));
		$this->assertFalse($blog2->canEdit($visitor));
		$this->assertFalse($blog->canAddChildren($visitor));
		$this->assertFalse($blog2->canAddChildren($visitor));
		$this->assertFalse($postA->canEdit($visitor));
		$this->assertFalse($postB->canEdit($visitor));
		$this->assertFalse($postC->canEdit($visitor));
		$this->assertFalse($postA->canPublish($visitor));
		$this->assertFalse($postB->canPublish($visitor));
		$this->assertFalse($postC->canPublish($visitor));
	}

}