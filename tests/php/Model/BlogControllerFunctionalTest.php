<?php

namespace SilverStripe\Blog\Tests\Model;

use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\i18n\i18n;
use SilverStripe\View\Parsers\URLSegmentFilter;

class BlogControllerFunctionalTest extends FunctionalTest
{
    protected static $fixture_file = 'BlogControllerFunctionalTest.yml';

    protected function setUp(): void
    {
        Config::modify()->set(URLSegmentFilter::class, 'default_allow_multibyte', true);
        i18n::set_locale('fa_IR');

        parent::setUp();
    }

    public function testGetCategoriesWithMultibyteUrl()
    {
        $this->logInWithPermission('VIEW_DRAFT_CONTENT');
        $result = $this->get('my-blog/category/' . rawurlencode('آبید') . '?stage=Stage');

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertStringContainsString('آبید', $result->getBody());
    }

    public function testGetTagsWithMultibyteUrl()
    {
        $this->logInWithPermission('VIEW_DRAFT_CONTENT');
        $result = $this->get('my-blog/tag/' . rawurlencode('برتراند')  . '?stage=Stage');

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertStringContainsString('برتراند', $result->getBody());
    }
}
