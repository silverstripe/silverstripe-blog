<?php

/**
 * @package blog
 */

/**
 * Blog tree is a way to group Blogs. It allows a tree of "Blog Holders".
 * Viewing branch nodes shows all blog entries from all blog holder children
 */

class BlogTree extends Page {

	private static $icon = "blog/images/blogtree-file.png";

	private static $description = "A grouping of blogs";

	private static $singular_name = 'Blog Tree Page';

	private static $plural_name = 'Blog Tree Pages';

	/**
	 * Default number of blog entries to show
	 *
	 * @var int
	 * @config
	 */
	private static $default_entries_limit = 10;

	private static $db = array(
		'Name' => 'Varchar(255)',
		'LandingPageFreshness' => 'Varchar',
	);

	private static $allowed_children = array(
		'BlogTree',
		'BlogHolder'
	);

	/*
	 * Finds the BlogTree object most related to the current page.
	 * - If this page is a BlogTree, use that
	 * - If this page is a BlogEntry, use the parent Holder
	 * - Otherwise, try and find a 'top-level' BlogTree
	 *
	 * @param $page allows you to force a specific page, otherwise,
	 * 				uses current
	 * @return BlogTree
	 */
	public static function current($page = null) {
		// extract page from current request if not specified
		if (!$page && Controller::has_curr()) {
			$controller = Controller::curr();
			if ($controller->hasMethod('data')) {
				$page = $controller->data();
			}
		}

		if ($page) {
			// If we _are_ a BlogTree, use us
			if ($page instanceof BlogTree) return $page;

			// If page is a virtual page use that
			if($page instanceof VirtualPage && $page->CopyContentFrom() instanceof BlogTree) {
				return $page;
			}
			
			// Or, if we a a BlogEntry underneath a BlogTree, use our parent
			if($page instanceof BlogEntry && $page->getParent() instanceof BlogTree) {
				return $page->getParent();
			}
		}

		// Try to find a top-level BlogTree
		$top = BlogTree::get()->filter("ParentID", 0)->first();
		if($top) return $top;

		// Try to find any BlogTree that is not inside another BlogTree
		$blogTrees = BlogTree::get();
		foreach($blogTrees as $tree) {
			if(!($tree->getParent() instanceof BlogTree)) return $tree;
		}

		// This shouldn't be possible, but assuming the above fails, just return anything you can get
		return $blogTrees->first();
	}

	/**
	 * Calculates number of months of landing page freshness to show
	 *
	 * @return int Number of months, if filtered
	 */
	public function getLandingPageFreshnessMonths() {
		$freshness = $this->LandingPageFreshness;
		
		// Substitute 'INHERIT' for parent freshness, if available
		if ($freshness === "INHERIT") {
			$freshness = (($parent = $this->getParent()) && $parent instanceof BlogTree)
				? $parent->getLandingPageFreshnessMonths()
				: null;
		}
		return $freshness;
	}

	/* ----------- CMS CONTROL -------------- */

	public function getSettingsFields() {
		$fields = parent::getSettingsFields();

		$fields->addFieldToTab(
			'Root.Settings',
			new DropdownField(
				'LandingPageFreshness',
				'When you first open the blog, how many entries should I show',
				array(
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
				)
			)
		);

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

	/**
	 * Build a list of all IDs for BlogHolders that are children of us
	 *
	 * @return array
	 */
	public function BlogHolderIDs() {
		$holderIDs = array();
		$this->loadDescendantBlogHolderIDListInto($holderIDs);
		return $holderIDs;
	}

	/**
	 * Get entries in this blog.
	 *
	 * @param string $limit Page size of paginated list
	 * @param string $tag Only get blog entries with this tag
	 * @param string $date Only get blog entries on this date - either a year, or a year-month eg '2008' or '2008-02'
	 * @param array $filters A list of DataList compatible filters
	 * @param mixed $where Raw SQL WHERE condition(s)
	 * @return PaginatedList The list of entries in a paginated list
	 */
	public function Entries($limit = '', $tag = '', $date = '', $filters = array(), $where = '') {
		// Filter by all current blog holder parents, if any are available
		$holderIDs = $this->BlogHolderIDs();
		if(empty($holderIDs)) return false;

		// Build filtered list
		$entries = BlogEntry::get()
			->filter('ParentID', $holderIDs)
			->sort($order = '"BlogEntry"."Date" DESC');

		// Apply where condition
		if($where) $entries = $entries->where($where);

		// Add tag condition
		if($tag) $entries = $entries->filter('Tags:PartialMatch', $tag);

		// Add date condition
		if($date && preg_match('/^(?<year>\d+)([-\\/](?<month>\d+))?/', $date, $matches)) {
			// Add year filter
			$yearExpression = DB::get_conn()->formattedDatetimeClause('"BlogEntry"."Date"', '%Y');
			$uintExpression = DB::get_schema()->dbDataType('unsigned integer');
			$entries = $entries->where(array(
				"CAST($yearExpression AS $uintExpression) = ?" => $matches['year']
			));

			// Add month filter
			if(!empty($matches['month'])) {
				$monthExpression = DB::get_conn()->formattedDatetimeClause('"BlogEntry"."Date"', '%m');
				$entries = $entries->where(array(
					"CAST($monthExpression AS $uintExpression) = ?" => $matches['month']
				));
			}
		}
		
		// Deprecate old $retrieveCallback parameter
		if($filters && (is_string($filters) || is_callable($filters))) {
			Deprecation::notice(
				'0.8',
				'$retrieveCallback parameter is deprecated. Use updateEntries in an extension instead.'
			);
			$callbackWhere = $entries->dataQuery()->query()->getWhere();
			return call_user_func($filters, 'BlogEntry', $callbackWhere, $limit, $order);
		}

		// Apply filters
		if($filters) $entries = $entries->filter($filters);

		// Extension point
		$this->extend('updateEntries', $entries, $limit, $tag, $date, $filters, $where);

		// Paginate results
    	$list = new PaginatedList($entries, Controller::curr()->request);
    	$list->setPageLength($limit);
    	return $list;
	}
}

class BlogTree_Controller extends Page_Controller {

	private static $allowed_actions = array(
		'index',
		'rss',
		'tag',
		'date'
	);

	private static $casting = array(
		'SelectedTag' => 'Text',
		'SelectedAuthor' => 'Text'
	);

	public function init() {
		parent::init();

		$this->IncludeBlogRSS();

		Requirements::themedCSS("blog","blog");
	}

	/**
	 * Determine selected BlogEntry items to show on this page
	 *
	 * @param int $limit
	 * @return PaginatedList
	 */
	public function BlogEntries($limit = null) {
		require_once('Zend/Date.php');
		$filter = array();

		// Defaults for limit
		if($limit === null) $limit = BlogTree::config()->default_entries_limit;

		// only use freshness if no action is present (might be displaying tags or rss)
		$landingPageFreshness = $this->getLandingPageFreshnessMonths();
		if ($landingPageFreshness && !$this->request->param('Action')) {
			$date = new Zend_Date(SS_Datetime::now()->getValue());
			$date->sub($landingPageFreshness, Zend_Date::MONTH);
			$date = $date->toString('YYYY-MM-dd');

			$filter["Date:GreaterThan"] = $date;
		}
		
		// Allow filtering by author field
		if($author = $this->SelectedAuthor()) {
			$filter['Author:PartialMatch'] = $author;
		}

		// Return filtered items
		return $this->Entries($limit, $this->SelectedTag(), $this->SelectedDate(), $filter);
	}

	/**
	 * This will create a <link> tag point to the RSS feed
	 */
	public function IncludeBlogRSS() {
		RSSFeed::linkToFeed($this->Link('rss'), _t('BlogHolder.RSSFEED',"RSS feed of these blogs"));
	}

	/**
	 * Get the rss feed for this blog holder's entries
	 */
	public function rss() {
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
	public function defaultAction($action) {
		if(stristr($_SERVER['HTTP_USER_AGENT'], 'SimplePie')) return $this->rss();

		return parent::defaultAction($action);
	}

	/**
	 * Return the currently viewing tag used in the template as $Tag
	 *
	 * @return string
	 */
	public function SelectedTag() {
		if ($this->request->latestParam('Action') == 'tag') {
			$tag = $this->request->latestParam('ID');
			return urldecode($tag);
		}
		return '';
	}

	/**
	 * Return the selected date from the blog tree
	 *
	 * @return string Date in format 'year-month', 'year', or false if not a date
	 */
	public function SelectedDate() {
		if($this->request->latestParam('Action') !== 'date') return false;

		// Check year
		$year = $this->request->latestParam('ID');
		if(!is_numeric($year)) return false;

		// Check month
		$month = $this->request->latestParam('OtherID');
		if(is_numeric($month) && $month < 13) {
			return $year . '-' . $month;
		} else {
			return $year;
		}
	}

	/**
	 * @return string
	 */
	public function SelectedAuthor() {
		if($author = $this->request->getVar('author')) {
			$hasAuthor = BlogEntry::get()
				->filter('Author:PartialMatch', $author)
				->Count();
			if($hasAuthor) return $author;
		}
	}

	/**
	 *
	 * @return string
	 */
	public function SelectedNiceDate(){
		$date = $this->SelectedDate();

		if(strpos($date, '-')) {
			$date = explode("-",$date);
			return date("F", mktime(0, 0, 0, $date[1], 1, date('Y'))). " " .date("Y", mktime(0, 0, 0, date('m'), 1, $date[0]));

		} else {
			return date("Y", mktime(0, 0, 0, date('m'), 1, $date));
		}
	}
}
