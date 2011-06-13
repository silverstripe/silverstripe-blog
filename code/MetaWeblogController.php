<?php

require_once(BASE_PATH . '/blog/thirdparty/xmlrpc/xmlrpc.php');
require_once(BASE_PATH . '/blog/thirdparty/xmlrpc/xmlrpcs.php');
require_once(BASE_PATH . '/blog/thirdparty/xmlrpc/xmlrpc_wrappers.php');

/**
 * MetaWeblogController provides the MetaWeblog API for SilverStripe blogs.
 */
class MetaWeblogController extends Controller {	
	function index($request) {
		
		// Create an xmlrpc server, and set up the method calls
		$service = new xmlrpc_server(array(
			"blogger.getUsersBlogs" => array(
				"function" => array($this, "getUsersBlogs")
			),
			"metaWeblog.getRecentPosts" => array(
				'function' => array($this, 'getRecentPosts')
			),
			'metaWeblog.getCategories' => array(
				'function' => array($this, 'getCategories')
			)
		), false);
		
		// Use nice php functions, and call the service
		$service->functions_parameters_type = 'phpvals';
		$service->service();
		
		// Tell SilverStripe not to try render a template
		return false;
	}
	
	/**
	 * Get a list of BlogHolders the user has access to.
	 */
	function getUsersBlogs($appkey, $username, $password) {
		$member = MemberAuthenticator::authenticate(array(
				'Email' => $username, 
				'Password' => $password,
		));
		
		// TODO Throw approriate error.
		if(!$member) die();
	
		$blogholders = DataObject::get('BlogHolder');
		
		$response = array();
		
		foreach($blogholders as $bh) {
			if(!$bh->canAddChildren($member)) continue;
			
			$bgarr = array();
			$bgarr['url'] = $bh->AbsoluteLink();
			$bgarr['blogid'] = (int) $bh->ID;
			$bgarr['blogname'] = $bh->Title;
			
			$response[] = $bgarr;
		}
		
		return $response;
	}
	
	/**
	 * Get the most recent posts on a blog.
	 */
	function getRecentPosts($blogid, $username, $password, $numberOfPosts) {
		$member = MemberAuthenticator::authenticate(array(
				'Email' => $username, 
				'Password' => $password,
		));
		
		// TODO Throw approriate error.
		if(!$member) die();
		
		$posts = DataObject::get('BlogEntry', '"ParentID" = ' . (int) $blogid, '"Date" DESC');
		
		$res = array();
		$postsSoFar = 0;
		
		foreach($posts as $post) {
			if(!$post->canEdit($member)) continue;
		
			$parr = array();
			
			$parr['title'] = $post->Title;
			$parr['link'] = $post->AbsoluteLink();
			$parr['description'] = $post->Content;
			$parr['postid'] = (int) $post->ID;
			
			$res[] = $parr;
			
			if(++$postsSoFar >= $numberOfPosts) break;
		}
		
		return $res;
	}
	
	function getCategories() {
		//TODO dummy function
		return array();
	}
}

?>
