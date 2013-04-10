<?php 

/**
 * @package blog
 */

/**
 * Blog tree is a way to group Blogs. It allows a tree of "Blog Holders". 
 * Viewing branch nodes shows all blog entries from all blog holder children
 */

class BlogTree extends Page {

	static $icon = "blog/images/blogtree-file.png";

	static $description = "A grouping of blogs";
	
	static $singular_name = 'Blog Tree Page';
	
	static $plural_name = 'Blog Tree Pages';
	
	// Default number of blog entries to show
	static $default_entries_limit = 10;
	
	static $db = array(
		'InheritSideBar' => 'Boolean',
		'LandingPageFreshness' => 'Varchar',
	);
	
	static $defaults = array(
		'InheritSideBar' => True
	);
	
	static $has_one = array();

	static $has_many = array();
	
	static $allowed_children = array(
		'BlogTree', 'BlogHolder'
	);

	

	/*
	 * Finds the BlogTree object most related to the current page.
	 * - If this page is a BlogTree, use that
	 * - If this page is a BlogEntry, use the parent Holder
	 * - Otherwise, try and find a 'top-level' BlogTree
	 *
	 * @param $page allows you to force a specific page, otherwise,
	 * 				uses current
	 */
	static function current($page = null) {
		
		if (!$page) {
			$controller = Controller::curr();
			if($controller) $page = $controller->data();
		}
		
		// If we _are_ a BlogTree, use us
		if ($page instanceof BlogTree) return $page;
		
		// Or, if we a a BlogEntry underneath a BlogTree, use our parent
		if($page->is_a("BlogEntry")) {
			$parent = $page->getParent();
			if($parent instanceof BlogTree) return $parent;
		}
		
		// Try to find a top-level BlogTree
		$top = DataObject::get_one('BlogTree', "\"ParentID\" = '0'");
		if($top) return $top;
		
		// Try to find any BlogTree that is not inside another BlogTree
		if($blogTrees=DataObject::get('BlogTree')) foreach($blogTrees as $tree) {
			if(!($tree->getParent() instanceof BlogTree)) return $tree;
		}
		
		// This shouldn't be possible, but assuming the above fails, just return anything you can get
		return $blogTrees->first();
	}

	/* ----------- ACCESSOR OVERRIDES -------------- */
	
	public function getLandingPageFreshness() {
		$freshness = $this->getField('LandingPageFreshness');
		// If we want to inherit freshness, try that first
		if ($freshness == "INHERIT" && $this->getParent()) $freshness = $this->getParent()->LandingPageFreshness;
		// If we don't have a parent, or the inherited result was still inherit, use default
		if ($freshness == "INHERIT") $freshness = '';
		return $freshness;
	}
	
	function SideBar() {
		if($this->InheritSideBar && $this->getParent()) {
			if (method_exists($this->getParent(), 'SideBar')) return $this->getParent()->SideBar();
		}
		
		if($this->SideBarID){
			return DataObject::get_by_id('WidgetArea', $this->SideBarID);
			// @todo: This segfaults - investigate why then fix: return $this->getComponent('SideBar');
		}
	}
	
	/* ----------- CMS CONTROL -------------- */
	
	function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->addFieldToTab('Root.Main', new DropdownField('LandingPageFreshness', 'When you first open the blog, how many entries should I show', array( 
 			"" => "All entries", 
			"1" => "Last month's entries", 
			"2" => "Last 2 months' entries", 
			"3" => "Last 3 months' entries", 
			"4" => "Last 4 months' entries", 
			"5" => "Last 5 months' entries", 
			"6" => "Last 6 months' entries", 
			"7" => "Last 7 months' entries", 
			"8" => "Last 8 months' entries", 
			"9" => "Last 9 months' entries", 
			"10" => "Last 10 months' entries", 
			"11" => "Last 11 months' entries", 
			"12" => "Last year's entries", 
			"INHERIT" => "Take value from parent Blog Tree"
		)), "Content"); 
 		if(class_exists('WidgetArea')) {
 			$fields->addFieldToTab("Root.Widgets", new CheckboxField("InheritSideBar", 'Inherit Sidebar From Parent'));
			$fields->addFieldToTab("Root.Widgets", new WidgetAreaEditor("SideBar"));
 		}
		
		
		return $fields;
	}
		
	/* ----------- New accessors -------------- */
	
	public function loadDescendantBlogHolderIDListInto(&$idList) {
		if ($children = $this->AllChildren()) {
			foreach($children as $child) {
				if(in_array($child->ID, $idList)) continue;
				
				if($child instanceof BlogHolder) {
					$idList[] = $child->ID; 
				} elseif($child instanceof BlogTree) {
					$child->loadDescendantBlogHolderIDListInto($idList);
				}                             
			}
		}
	}
	
	// Build a list of all IDs for BlogHolders that are children of us
	public function BlogHolderIDs() {
		$holderIDs = array();
		$this->loadDescendantBlogHolderIDListInto($holderIDs);
		return $holderIDs;
	}
		
	/**
	 * Get entries in this blog.
	 * @param string limit A clause to insert into the limit clause.
	 * @param string tag Only get blog entries with this tag
	 * @param string date Only get blog entries on this date - either a year, or a year-month eg '2008' or '2008-02'
	 * @param callback retrieveCallback A function to call with pagetype, filter and limit for custom blog sorting or filtering
	 * @param string $where
	 * @return DataObjectSet
	 */
	public function Entries($limit = '', $tag = '', $date = '', $retrieveCallback = null, $filter = '') {
		
		$tagCheck = '';
		$dateCheck = '';
		
		if($tag) {
			$SQL_tag = Convert::raw2sql($tag);
			$tagCheck = "AND \"BlogEntry\".\"Tags\" LIKE '%$SQL_tag%'";
		}

		if($date) {
			// Some systems still use the / seperator for date presentation
			if( strpos($date, '-') ) $seperator = '-';
			elseif( strpos($date, '/') ) $seperator = '/';
			
			if(isset($seperator) && !empty($seperator)) {
				// The 2 in the explode argument will tell it to only create 2 elements
				// i.e. in this instance the $year and $month fields respectively
				list($year,$month) = explode( $seperator, $date, 2);
				
				$year = (int)$year;
				$month = (int)$month;

				if($year && $month) {
					if(method_exists(DB::getConn(), 'formattedDatetimeClause')) {
						$db_date=DB::getConn()->formattedDatetimeClause('"BlogEntry"."Date"', '%m');
						$dateCheck = "AND CAST($db_date AS " . DB::getConn()->dbDataType('unsigned integer') . ") = $month AND " . DB::getConn()->formattedDatetimeClause('"BlogEntry"."Date"', '%Y') . " = '$year'";
					} else {
						$dateCheck = "AND MONTH(\"BlogEntry\".\"Date\") = '$month' AND YEAR(\"BlogEntry\".\"Date\") = '$year'";
					}
				}
			} else {
				$year = (int) $date;
				if($year) {
					if(method_exists(DB::getConn(), 'formattedDatetimeClause')) {
						$dateCheck = "AND " . DB::getConn()->formattedDatetimeClause('"BlogEntry"."Date"', '%Y') . " = '$year'";
					} else {
						$dateCheck = "AND YEAR(\"BlogEntry\".\"Date\") = '$year'";
					}
				}
			}
		}

		// Build a list of all IDs for BlogHolders that are children of us
		$holderIDs = $this->BlogHolderIDs();
		
		// If no BlogHolders, no BlogEntries. So return false
		if(empty($holderIDs)) return false;
		
		// Otherwise, do the actual query
		if($filter) $filter .= ' AND ';
		$filter .= '"SiteTree"."ParentID" IN (' . implode(',', $holderIDs) . ") $tagCheck $dateCheck";

		$order = '"BlogEntry"."Date" DESC';

		// By specifying a callback, you can alter the SQL, or sort on something other than date.
		if($retrieveCallback) return call_user_func($retrieveCallback, 'BlogEntry', $filter, $limit, $order);

		$entries = BlogEntry::get()->where($filter)->sort($order);

    	$list = new PaginatedList($entries, Controller::curr()->request);
    	$list->setPageLength($limit);
    	return $list;
	}
}

class BlogTree_Controller extends Page_Controller {
	
	static $allowed_actions = array(
		'index',
		'rss',
		'tag',
		'date'
	);
	
	function init() {
		parent::init();
		
		$this->IncludeBlogRSS();
		
		Requirements::themedCSS("blog","blog");
	}

	function BlogEntries($limit = null) {
		require_once('Zend/Date.php');
		
		if($limit === null) $limit = BlogTree::$default_entries_limit;

		// only use freshness if no action is present (might be displaying tags or rss)
		if ($this->LandingPageFreshness && !$this->request->param('Action')) {
			$d = new Zend_Date(SS_Datetime::now()->getValue());
			$d->sub($this->LandingPageFreshness, Zend_Date::MONTH);
			$date = $d->toString('YYYY-MM-dd');
			
			$filter = "\"BlogEntry\".\"Date\" > '$date'";
		} else {
			$filter = '';
		}
		// allow filtering by author field and some blogs have an authorID field which
		// may allow filtering by id
		if(isset($_GET['author']) && isset($_GET['authorID'])) {
			$author = Convert::raw2sql($_GET['author']);
			$id = Convert::raw2sql($_GET['authorID']);
			
			$filter .= " \"BlogEntry\".\"Author\" LIKE '". $author . "' OR \"BlogEntry\".\"AuthorID\" = '". $id ."'";
		}
		else if(isset($_GET['author'])) {
			$filter .=  " \"BlogEntry\".\"Author\" LIKE '". Convert::raw2sql($_GET['author']) . "'";
		}
		else if(isset($_GET['authorID'])) {
			$filter .=  " \"BlogEntry\".\"AuthorID\" = '". Convert::raw2sql($_GET['authorID']). "'";
		}

		$date = $this->SelectedDate();
		
		return $this->Entries($limit, $this->SelectedTag(), ($date) ? $date : '', null, $filter);
	}

	/**
	 * This will create a <link> tag point to the RSS feed
	 */
	function IncludeBlogRSS() {
		RSSFeed::linkToFeed($this->Link('rss'), _t('BlogHolder.RSSFEED',"RSS feed of these blogs"));
	}
	
	/**
	 * Get the rss feed for this blog holder's entries
	 */
	function rss() {
		global $project_name;

		$blogName = $this->Title;
		$altBlogName = $project_name . ' blog';
		
		$entries = $this->Entries(20);

		if($entries) {
			$rss = new RSSFeed($entries, $this->Link('rss'), ($blogName ? $blogName : $altBlogName), "", "Title", "RSSContent");
			return $rss->outputToBrowser();
		}
	}
	
	/**
	 * Protection against infinite loops when an RSS widget pointing to this page is added to this page
	 */
	function defaultAction($action) {
		if(stristr($_SERVER['HTTP_USER_AGENT'], 'SimplePie')) return $this->rss();
		
		return parent::defaultAction($action);
	}
	
	/**
	 * Return the currently viewing tag used in the template as $Tag 
	 *
	 * @return String
	 */
	function SelectedTag() {
		return ($this->request->latestParam('Action') == 'tag') ? Convert::raw2xml($this->request->latestParam('ID')) : '';
	}
	
	/**
	 * Return the selected date from the blog tree
	 *
	 * @return Date
	 */
	function SelectedDate() {
		if($this->request->latestParam('Action') == 'date') {
			$year = $this->request->latestParam('ID');
			$month = $this->request->latestParam('OtherID');
	
			if(is_numeric($year) && is_numeric($month) && $month < 13) {
		
				$date = $year .'-'. $month;
				return $date;
				
			} else {
				
				if(is_numeric($year)) return $year;	
			}
		}
			
		return false;
	}

	/**
	 * @return String
	 */
	function SelectedAuthor() {
		if($this->request->getVar('author')) {
			$hasAuthor = BlogEntry::get()->filter('Author', $this->request->getVar('author'))->Count();
			return $hasAuthor ? Convert::raw2xml($this->request->getVar('author')) : null;
		} elseif($this->request->getVar('authorID')) {
			$hasAuthor = BlogEntry::get()->filter('AuthorID', $this->request->getVar('authorID'))->Count();
			if($hasAuthor) {
				$member = Member::get()->byId($this->request->getVar('authorID'));
				if($member) {
					if($member->hasMethod('BlogAuthorTitle')) {
						return Convert::raw2xml($member->BlogAuthorTitle);
					} else {
						return Convert::raw2xml($member->Title);
					}
				} else {
					return null;
				}
			}
		}
	}
	
	function SelectedNiceDate(){
		$date = $this->SelectedDate();
		
		if(strpos($date, '-')) {
			$date = explode("-",$date);
			return date("F", mktime(0, 0, 0, $date[1], 1, date('Y'))). " " .date("Y", mktime(0, 0, 0, date('m'), 1, $date[0]));
		
		} else {
			return date("Y", mktime(0, 0, 0, date('m'), 1, $date));
		}
	}
}
