<?php

class BlogTagsCloudWidgetTest extends SapphireTest {

    /**
     * @var string
     */
    public static $fixture_file = 'blog.yml';

    public function testGetCMSFields() {
        if (!class_exists('Widget')) {
            $this->markTestSkipped('Widgets module not installed');
        }

        $widget = new BlogTagsCloudWidget();
        $fields = $widget->getCMSFields();
        $names = array();
        foreach ($fields as $field) {
            array_push($names, $field->getName());
        }

        $expected = array('Title', 'Enabled', 'BlogID');
        $this->assertEquals($expected, $names);
    }

    public function testGetTags() {
        if (!class_exists('Widget')) {
            $this->markTestSkipped('Widgets module not installed');
        }
        $widget = new BlogTagsCloudWidget();
        $blog = $this->objFromFixture('Blog', 'FourthBlog');
        $widget->BlogID = $blog->ID;
        $widget->write();
        $tags = $widget->getTags()->toArray();

        $tag = $tags[0];
        $this->assertEquals('Cat', $tag->TagName);
        $this->assertEquals('/fourth-blog/tag/cat', $tag->Link);
        $this->assertEquals(2, $tag->TagCount);
        $this->assertEquals(5, $tag->NormalizedTag);

        $tag = $tags[1];
        $this->assertEquals('Cool', $tag->TagName);
        $this->assertEquals('/fourth-blog/tag/cool', $tag->Link);
        $this->assertEquals(3, $tag->TagCount);
        $this->assertEquals(8, $tag->NormalizedTag);

        $tag = $tags[2];
        $this->assertEquals('Kiwi', $tag->TagName);
        $this->assertEquals('/fourth-blog/tag/kiwi', $tag->Link);
        $this->assertEquals(1, $tag->TagCount);
        $this->assertEquals(3, $tag->NormalizedTag);

        $tag = $tags[3];
        $this->assertEquals('Popular', $tag->TagName);
        $this->assertEquals('/fourth-blog/tag/popular', $tag->Link);
        $this->assertEquals(4, $tag->TagCount);
        $this->assertEquals(10, $tag->NormalizedTag);
    }
}
