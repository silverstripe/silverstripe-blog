<?php
/**
 * @package blog
 * @subpackage tests
 */
class BlogHolderFunctionalTest extends FunctionalTest {
	
	static $fixture_file = 'blog/tests/BlogHolderFunctionalTest.yml';
	static $origlThemes;
	
	function setUp() {
		parent::setUp();
		self::$origlThemes = SSViewer::current_theme();
		SSViewer::set_theme(null);
		
		$blogHolder = $this->objFromFixture('BlogHolder', 'blogholder');
		$blogHolder->publish('Stage', 'Live');
		$blogEntry = $this->objFromFixture('BlogEntry', 'entry1');
		$blogEntry->publish('Stage', 'Live');
	}
	
	function tearDown(){
		SSViewer::set_theme(self::$origlThemes);
		parent::tearDown();
	}
	
	function testFrontendBlogPostRequiresPermission() {
		// get valid SecurityID (from comments form, would usually be copy/pasted)
		$blogEntry = $this->objFromFixture('BlogEntry', 'entry1');
		$response = $this->get($blogEntry->RelativeLink());
		$securityID = Session::get('SecurityID');
		
		// without login
		$data = array(
			'Title'=>'Disallowed',
			'Author'=>'Disallowed',
			'BlogPost'=>'Disallowed',
			'action_postblog' => 'Post blog entry',
			'SecurityID' => $securityID
		);
		$response = $this->post('blog/BlogEntryForm', $data);
		$this->assertFalse(DataObject::get_one('BlogEntry', sprintf("\"Title\" = 'Disallowed'")));
		
		// with login
		$blogEditor = $this->objFromFixture('Member', 'blog_editor');
		$this->session()->inst_set('loggedInAs', $blogEditor->ID);
		Permission::flush_permission_cache();
		$data = array(
			'Title'=>'Allowed',
			'Author'=>'Allowed',
			'BlogPost'=>'Allowed',
			'action_postblog' => 'Post blog entry',
			'SecurityID' => $securityID
		);
		$response = $this->post('blog/BlogEntryForm', $data);

		$this->assertType('BlogEntry', DataObject::get_one('BlogEntry', sprintf("\"Title\" = 'Allowed'")));
	}
}
