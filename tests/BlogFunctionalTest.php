<?php

class BlogFunctionalTest extends FunctionalTest
{
    protected static $fixture_file = 'BlogFunctionalTest.yml';

    protected static $use_draft_site = true;

    public function setUp()
    {
        Config::inst()->update('URLSegmentFilter', 'default_allow_multibyte', true);

        parent::setUp();

        i18n::set_locale('fa_IR');
    }

    public function testBlogWithMultibyteUrl()
    {
        $result = $this->get('آبید');

        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testMemberProfileWithMultibyteUrlAndName()
    {
        $result = $this->get('آبید/profile/عبّاس-آبان');

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertContains('My Blog Post', $result->getBody());
    }

    public function testMemberProfileWithMultibyteUrlAndEnglishName()
    {
        $result = $this->get('آبید/profile/bob-jones');

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertContains('My Blog Post', $result->getBody());
    }
}
