<?php

class BlogArchiveWidgetTest extends SapphireTest
{
    protected static $fixture_file = 'BlogArchiveWidgetTest.yml';

    public function setUp()
    {
        if (!class_exists('Widget')) {
            self::$fixture_file = null;
            parent::setUp();
            $this->markTestSkipped('Test requires silverstripe/widgets to be installed.');
        }

        SS_Datetime::set_mock_now('2017-09-20 12:00:00');

        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();

        SS_Datetime::clear_mock_now();
    }

    public function testArchiveMonthlyFromStage()
    {
        $widget = $this->objFromFixture('BlogArchiveWidget', 'archive-monthly');
        $archive = $widget->getArchive();

        $this->assertInstanceOf('SS_List', $archive);
        $this->assertCount(3, $archive);
        $this->assertDOSContains(array(
            array('Title' => 'August 2017'),
            array('Title' => 'September 2017'),
            array('Title' => 'May 2015'),
        ), $archive);
    }

    public function testArchiveMonthlyFromLive()
    {
        $original = Versioned::current_stage();

        $this->objFromFixture('BlogPost', 'post-b')->doPublish();
        Versioned::reading_stage('Live');

        $widget = $this->objFromFixture('BlogArchiveWidget', 'archive-monthly');
        $archive = $widget->getArchive();

        $this->assertCount(1, $archive);
        $this->assertDOSContains(array(
            array('Title' => 'August 2017'),
        ), $archive);

        Versioned::reading_stage($original);
    }

    public function testArchiveYearly()
    {
        $widget = $this->objFromFixture('BlogArchiveWidget', 'archive-yearly');
        $archive = $widget->getArchive();

        $this->assertInstanceOf('SS_List', $archive);
        $this->assertCount(2, $archive);
        $this->assertDOSContains(array(
            array('Title' => '2017'),
            array('Title' => '2015'),
        ), $archive);
    }

    public function testArchiveMonthlyWithNewPostsAdded()
    {
        $original = Versioned::current_stage();
        Versioned::reading_stage('Stage');

        $widget = $this->objFromFixture('BlogArchiveWidget', 'archive-monthly');
        $archive = $widget->getArchive();

        $this->assertCount(3, $archive, 'Three months are shown in the blog archive list from fixtures');

        SS_Datetime::set_mock_now('2018-01-01 12:00:00');

        $newPost = new BlogPost;
        $newPost->ParentID = $this->objFromFixture('Blog', 'my-blog')->ID;
        $newPost->Title = 'My new blog post';
        $newPost->PublishDate = '2018-01-01 08:00:00'; // Same day as the mocked now, but slightly earlier
        $newPost->write();

        $archive = $widget->getArchive();

        $this->assertCount(4, $archive, 'Four months are shown in the blog archive list after new post added');

        Versioned::reading_stage($original);
    }
}
