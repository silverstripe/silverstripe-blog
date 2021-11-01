<?php

namespace SilverStripe\Blog\Tests;

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\Blog\Widgets\BlogArchiveWidget;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\SS_List;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Widgets\Model\Widget;

class BlogArchiveWidgetTest extends SapphireTest
{
    protected static $fixture_file = 'BlogArchiveWidgetTest.yml';

    protected function setUp(): void
    {
        if (!class_exists(Widget::class)) {
            self::$fixture_file = null;
            parent::setUp();
            $this->markTestSkipped('Test requires silverstripe/widgets to be installed.');
        }

        DBDatetime::set_mock_now('2017-09-20 12:00:00');

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        DBDatetime::clear_mock_now();
    }

    public function testArchiveMonthlyFromStage()
    {
        $widgetA = $this->objFromFixture(BlogArchiveWidget::class, 'archive-monthly-a');
        $archiveA = $widgetA->getArchive();

        $this->assertInstanceOf(SS_List::class, $archiveA);
        $this->assertCount(3, $archiveA);
        $this->assertListContains([
            ['Title' => 'August 2017'],
            ['Title' => 'September 2017'],
            ['Title' => 'May 2015'],
        ], $archiveA);

        $widgetB = $this->objFromFixture(BlogArchiveWidget::class, 'archive-monthly-b');
        $archiveB = $widgetB->getArchive();

        $this->assertInstanceOf(SS_List::class, $archiveB);
        $this->assertCount(2, $archiveB);
        $this->assertListContains([
            ['Title' => 'March 2016'],
            ['Title' => 'June 2016'],
        ], $archiveB);
    }

    public function testArchiveMonthlyFromLive()
    {
        $original = Versioned::get_stage();

        $this->objFromFixture(BlogPost::class, 'post-b')->publishRecursive();
        $this->objFromFixture(BlogArchiveWidget::class, 'archive-monthly-a')->publishRecursive();
        Versioned::set_stage(Versioned::LIVE);

        $widget = $this->objFromFixture(BlogArchiveWidget::class, 'archive-monthly-a');
        $archive = $widget->getArchive();

        $this->assertCount(1, $archive);
        $this->assertListContains([
            ['Title' => 'August 2017'],
        ], $archive);

        if ($original) {
            Versioned::set_stage($original);
        }
    }

    public function testArchiveYearly()
    {
        $widgetA = $this->objFromFixture(BlogArchiveWidget::class, 'archive-yearly-a');
        $archiveA = $widgetA->getArchive();

        $this->assertInstanceOf(SS_List::class, $archiveA);
        $this->assertCount(2, $archiveA);
        $this->assertListContains([
            ['Title' => '2017'],
            ['Title' => '2015'],
        ], $archiveA);

        $widgetB = $this->objFromFixture(BlogArchiveWidget::class, 'archive-yearly-b');
        $archiveB = $widgetB->getArchive();

        $this->assertInstanceOf(SS_List::class, $archiveB);
        $this->assertCount(1, $archiveB);
        $this->assertListContains([
            ['Title' => '2016'],
        ], $archiveB);
    }

    public function testArchiveMonthlyWithNewPostsAdded()
    {
        $original = Versioned::get_stage();
        Versioned::set_stage('Stage');

        $widget = $this->objFromFixture(BlogArchiveWidget::class, 'archive-monthly-a');
        $archive = $widget->getArchive();

        $this->assertCount(3, $archive, 'Three months are shown in the blog archive list from fixtures');

        DBDatetime::set_mock_now('2018-01-01 12:00:00');

        $newPost = new BlogPost;
        $newPost->ParentID = $this->objFromFixture(Blog::class, 'blog-a')->ID;
        $newPost->Title = 'My new blog post';
        $newPost->PublishDate = '2018-01-01 08:00:00'; // Same day as the mocked now, but slightly earlier
        $newPost->write();

        $archive = $widget->getArchive();

        $this->assertCount(4, $archive, 'Four months are shown in the blog archive list after new post added');

        if ($original) {
            Versioned::set_stage($original);
        }
    }
}
