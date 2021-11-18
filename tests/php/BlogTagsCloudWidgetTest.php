<?php

namespace SilverStripe\Blog\Tests;

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Blog\Widgets\BlogTagsCloudWidget;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Widgets\Model\Widget;

class BlogTagsCloudWidgetTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'blog.yml';

    public function testGetCMSFields()
    {
        if (!class_exists(Widget::class)) {
            $this->markTestSkipped('Widgets module not installed');
        }

        $widget = new BlogTagsCloudWidget();
        $fields = $widget->getCMSFields();
        $names = [];
        foreach ($fields as $field) {
            array_push($names, $field->getName());
        }

        $expected = ['Title', 'Enabled', 'BlogID'];
        $this->assertEquals($expected, $names);
    }

    public function testGetTags()
    {
        if (!class_exists(Widget::class)) {
            $this->markTestSkipped('Widgets module not installed');
        }
        $widget = new BlogTagsCloudWidget();
        $blog = $this->objFromFixture(Blog::class, 'FourthBlog');
        $widget->BlogID = $blog->ID;
        $widget->write();
        $tags = $widget->getTags()->toArray();

        $tag = $tags[0];
        $this->assertEquals('Cat', $tag->TagName);
        $this->assertEquals(Controller::join_links(Director::baseURL(), 'fourth-blog/tag/cat'), $tag->Link);
        $this->assertEquals(2, $tag->TagCount);
        $this->assertEquals(5, $tag->NormalizedTag);

        $tag = $tags[1];
        $this->assertEquals('Cool', $tag->TagName);
        $this->assertEquals(Controller::join_links(Director::baseURL(), 'fourth-blog/tag/cool'), $tag->Link);
        $this->assertEquals(3, $tag->TagCount);
        $this->assertEquals(8, $tag->NormalizedTag);

        $tag = $tags[2];
        $this->assertEquals('Kiwi', $tag->TagName);
        $this->assertEquals(Controller::join_links(Director::baseURL(), 'fourth-blog/tag/kiwi'), $tag->Link);
        $this->assertEquals(1, $tag->TagCount);
        $this->assertEquals(3, $tag->NormalizedTag);

        $tag = $tags[3];
        $this->assertEquals('Popular', $tag->TagName);
        $this->assertEquals(Controller::join_links(Director::baseURL(), 'fourth-blog/tag/popular'), $tag->Link);
        $this->assertEquals(4, $tag->TagCount);
        $this->assertEquals(10, $tag->NormalizedTag);
    }
}
