<?php

namespace SilverStripe\Blog\Tests;

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

    protected function setUp()
    {
        if (!class_exists(Widget::class)) {
            self::$fixture_file = null;
            parent::setUp();
            $this->markTestSkipped('Test requires silverstripe/widgets to be installed.');
        }

        DBDatetime::set_mock_now('2017-09-20 00:00:00');

        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();

        DBDatetime::clear_mock_now();
    }

    public function testArchiveMonthlyFromStage()
    {
        $widget = $this->objFromFixture(BlogArchiveWidget::class, 'archive-monthly');
        $archive = $widget->getArchive();

        $this->assertInstanceOf(SS_List::class, $archive);
        $this->assertCount(3, $archive);
        $this->assertDOSContains([
            ['Title' => 'August 2017'],
            ['Title' => 'September 2017'],
            ['Title' => 'May 2015'],
        ], $archive);
    }

    public function testArchiveMonthlyFromLive()
    {
        $original = Versioned::get_stage();

        $this->objFromFixture(BlogPost::class, 'post-b')->publishRecursive();
        Versioned::set_stage(Versioned::LIVE);

        $widget = $this->objFromFixture(BlogArchiveWidget::class, 'archive-monthly');
        $archive = $widget->getArchive();

        $this->assertCount(1, $archive);
        $this->assertDOSContains([
            ['Title' => 'August 2017'],
        ], $archive);

        if ($original) {
            Versioned::set_stage($original);
        }
    }

    public function testArchiveYearly()
    {
        $widget = $this->objFromFixture(BlogArchiveWidget::class, 'archive-yearly');
        $archive = $widget->getArchive();

        $this->assertInstanceOf(SS_List::class, $archive);
        $this->assertCount(2, $archive);
        $this->assertDOSContains([
            ['Title' => '2017'],
            ['Title' => '2015'],
        ], $archive);
    }
}
