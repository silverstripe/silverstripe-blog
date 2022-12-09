<?php

namespace SilverStripe\Blog\Tests;

use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\i18n\i18n;
use SilverStripe\View\Parsers\URLSegmentFilter;

class BlogFunctionalTest extends FunctionalTest
{
    protected static $fixture_file = 'BlogFunctionalTest.yml';

    protected function setUp(): void
    {
        Config::modify()->set(URLSegmentFilter::class, 'default_allow_multibyte', true);
        i18n::set_locale('fa_IR');

        parent::setUp();
    }

    public function testBlogWithMultibyteUrl()
    {
        $this->logInWithPermission('VIEW_DRAFT_CONTENT');
        $result = $this->get(rawurlencode('آبید') . '?stage=Stage');

        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testMemberProfileWithMultibyteUrlAndName()
    {
        $this->logInWithPermission('VIEW_DRAFT_CONTENT');
        $result = $this->get(rawurlencode('آبید') . '/profile/' . rawurlencode('عبّاس-آبان') . '?stage=Stage');

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertStringContainsString('My Blog Post', $result->getBody());
    }

    public function testMemberProfileWithMultibyteUrlAndEnglishName()
    {
        $this->logInWithPermission('VIEW_DRAFT_CONTENT');
        $result = $this->get(rawurlencode('آبید') . '/profile/bob-jones' . '?stage=Stage');

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertStringContainsString('My Blog Post', $result->getBody());
    }
}
