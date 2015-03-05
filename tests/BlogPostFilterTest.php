<?php

class BlogPostFilterTest extends SapphireTest {
	
	static $fixture_file = "blog.yml";

	public function setUp() {
		SS_Datetime::set_mock_now("2013-10-10 20:00:00");
		parent::setUp();
	}

	public function tearDown() {
		SS_Datetime::clear_mock_now();
		parent::tearDown();
	}

	public function testFilter() {
		$member = Member::currentUser();
		if($member) $member->logout();

		$count = BlogPost::get()->count();
		$this->assertEquals(3, $count, "Filtered blog posts");

		SS_Datetime::set_mock_now("2020-01-01 00:00:00");
		$count = BlogPost::get()->count();
		$this->assertEquals(5, $count, "Unfiltered blog posts");
	}

}
