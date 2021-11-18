<?php

namespace SilverStripe\Blog\Tests\Model;

use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\i18n\i18n;
use SilverStripe\View\Parsers\URLSegmentFilter;

class BlogControllerFunctionalTest extends FunctionalTest
{
    protected static $fixture_file = 'BlogControllerFunctionalTest.yml';

    protected static $use_draft_site = true;

    protected function setUp(): void
    {
        Config::modify()->set(URLSegmentFilter::class, 'default_allow_multibyte', true);
        i18n::set_locale('fa_IR');

        parent::setUp();
    }

    public function testGetCategoriesWithMultibyteUrl()
    {
        $result = $this->get('my-blog/category/' . rawurlencode('آبید'));

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertStringContainsString('آبید', $result->getBody());
    }

    public function testGetTagsWithMultibyteUrl()
    {
        $result = $this->get('my-blog/tag/' . rawurlencode('برتراند'));

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertStringContainsString('برتراند', $result->getBody());
    }
}
