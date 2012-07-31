<?php

require_once(BASE_PATH . '/blog/thirdparty/xmlrpc/xmlrpc.php');
require_once(BASE_PATH . '/blog/thirdparty/xmlrpc/xmlrpcs.php');
require_once(BASE_PATH . '/blog/thirdparty/xmlrpc/xmlrpc_wrappers.php');

/**
 * MetaWeblogController provides the MetaWeblog API for SilverStripe blogs.
 */
class MetaWeblogController extends Controller {

        static $MODERATE_PUBLISHING = false;
        static $RESTRICT_POST_TO_OWNER = false;
        
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
			),
                        'metaWeblog.newPost' => array(
                            'function' => array($this, 'newPost')
                        )
		), false);
		
		// Use nice php functions, and call the service
		$service->functions_parameters_type = 'phpvals';
		$service->service();
		
		// Tell SilverStripe not to try render a template
		return false;
	}

        /**
         * Authenticate the user.
         *
         * If blogid is not null, make sure that the user is the author of the blog
         *
         * @param <string> $username
         * @param <string> $password
         * @param <int> $blogid
         * @return <DataObject> $member
         */
        private function authenticate($username, $password, $blogid=null) {

            $member = MemberAuthenticator::authenticate(array(
                        'Email' => $username,
                        'Password' => $password,
                    ));

            if (!$member){
                Debug::log(Debug::text("Authentication Failed for " . $username));
                return false;
            }

            Session::set("loggedInAs",$member->ID);

            if ($blogid && self::$RESTRICT_POST_TO_OWNER) {
                $blogHolder = DataObject::get_one("BlogHolder", "`BlogHolder`.`ID` = " . (int) $blogid);

                if ($blogHolder->OwnerID == $member->ID){
                    return $member;
                }
            }else {
                return $member;
            }

            return false;
        }
	
	/**
	 * Get a list of BlogHolders the user has access to.
	 */
	function getUsersBlogs($appkey, $username, $password) {
		$member = $this->authenticate($username, $password);
		
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
		$member = $this->authenticate($username, $password);
		
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


        /**
         * Post a new Blog entry onto a Blog
         *
         * @param <int> $blogid
         * @param <string> $username
         * @param <string> $password
         * @param <array> $struct
         * @param <boolean> $publish
         * @return Boolean
         */
        public function newPost($blogid, $username, $password, $struct, $publish) {
            $member = $this->authenticate($username, $password, $blogid);

            // TODO Throw approriate error.
            if (!$member) die();

            $blogEntry = new BlogEntry();
            $blogEntry->setField("Title", $struct['title']);
            $blogEntry->setField("Content", $struct['description']);
            $blogEntry->setField("ParentID", $blogid);

            $blogEntry->write();

            if($publish && !self::$MODERATE_PUBLISHING){
                $blogEntry->doPublish();
            }

            return true;
        }
}

?>
