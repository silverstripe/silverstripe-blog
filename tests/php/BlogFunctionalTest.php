<?php

namespace SilverStripe\Blog\Tests;

use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\i18n\i18n;
use SilverStripe\View\Parsers\URLSegmentFilter;

class BlogFunctionalTest extends FunctionalTest
{
    protected static $fixture_file = 'BlogFunctionalTest.yml';

    protected static $use_draft_site = true;

    protected function setUp(): void
    {
        Config::modify()->set(URLSegmentFilter::class, 'default_allow_multibyte', true);
        i18n::set_locale('fa_IR');

        parent::setUp();
    }

    public function testBlogWithMultibyteUrl()
    {
        $result = $this->get(rawurlencode('آبید'));

        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testMemberProfileWithMultibyteUrlAndName()
    {
        $result = $this->get(rawurlencode('آبید') . '/profile/' . rawurlencode('عبّاس-آبان'));

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertStringContainsString('My Blog Post', $result->getBody());
    }

    public function testMemberProfileWithMultibyteUrlAndEnglishName()
    {
        $result = $this->get(rawurlencode('آبید') . '/profile/bob-jones');

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertStringContainsString('My Blog Post', $result->getBody());
    }
}
