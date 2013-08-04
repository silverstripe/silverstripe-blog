<?php

/**
 * Blog Holder
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael String <micmania@hotmail.co.uk>
**/
class Blog extends Page {

	private static $db = array(
		"PostsPerPage" => "Int",
	);

	private static $has_many = array(
		"Tags" => "BlogTag",
		"Categories" => "BlogCategory",
	);
	
	private static $allowed_children = array(
		"BlogPost",
	);

	private static $extensions = array(
		"BlogFilter",
	);

	/**
	 * Whether or not to show BlogPost's in a GridField. Usually this will be
	 * the case when they're hidden from the SiteTree.
	 *
	 * @var boolean
	**/
	private static $show_posts_in_gridfield = true;



	public function getCMSFields() {
		$fields = parent::getCMSFields();
		if($this->config()->get("show_posts_in_gridfield")) {
			$gridField = new GridField(
				"BlogPost",
				_t("Blog.FieldLabels.BlogPosts", "Blog Posts"), 
				$this->AllChildrenIncludingDeleted(),
				GridFieldConfig_SiteTree::create()
			);
			$fields->addFieldToTab("Root.BlogPosts", $gridField);
		}

		// Create categories and tag config
		$config = GridFieldConfig_RecordEditor::create();
		$config->removeComponentsByType("GridFieldAddNewButton");
		$config->addComponent(new GridFieldAddByDBField("buttons-before-left"));

		$categories = GridField::create(
			"Categories",
			_t("Blog.FieldLabels.Categories", "Categories"),
			$this->Categories(),
			$config
		);

		$tags = GridField::create(
			"Tags",
			_t("Blog.FieldLabels.Tags", "Tags"),
			$this->Tags(),
			$config
		);

		$fields->addFieldsToTab("Root.BlogOptions", array(
			$categories,
			$tags
		));

		return $fields;
	}


	public function getSettingsFields() {
		$fields = parent::getSettingsFields();
		$fields->addFieldToTab("Root.Settings", NumericField::create("PostsPerPage", _t("Blog.FieldLabels.POSTSPERPAGE", "Posts Per Page")));
		return $fields;
	}



	/**
	 * Loops through subclasses of BlogPost and checks whether they have been configured
	 * to be hidden. If so, then they will be excluded from the SiteTree.
	 *
	 * @return array
	**/
	public function getExcludedSiteTreeClassNames() {
		$classes = array();
		$tmpClasses = ClassInfo::subClassesFor("BlogPost");
		foreach($tmpClasses as $class) {
			if(!Config::inst()->get($class, "show_in_site_tree")) {
				$classes[$class] = $class;
			}
		}
		return $classes;
	}



	/**
	 * Return blogs posts
	 *
	 * @return DataList of BlogPost objects
	**/
	public function getBlogPosts() {
		return BlogPost::get()->filter("ParentID", $this->ID);
	}

}



/**
 * Blog Controller
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael String <micmania@hotmail.co.uk>
**/
class Blog_Controller extends Page_Controller {

	private static $allowed_actions = array(
		'archive',
		'tag',
		'category',
	);

	private static $url_handlers = array(
		'tag/$Tag!' => 'tag',
		'category/$Category!' => 'category',
		'archive/$Year!/$Month' => 'archive',
	);


	/** 
	 * The current Blog Post DataList query.
	 *
	 * @var DataList
	**/
	protected $blogPosts;



	public function index() {
		$this->blogPosts = $this->getBlogPosts();
		return $this->render();
	}



	/**
	 * Renders an archive for a specificed date. This can be by year or year/month
	 *
	 * @return SS_HTTPResponse
	**/
	public function archive() {
		$year = $this->getArchiveYear();
		$month = $this->getArchiveMonth();

		// If an invalid month has been passed, we can return a 404.
		if($this->request->param("Month") && !$month) {
			return $this->httpError(404, "Not Found");
		}

		if($year) {
			if($month) {
				$startDate = $year . '-' . $month . '-01 00:00:00';
				if($month == 12) {
					$endDate = ($year+1) . '-01-01 00:00:00';
				} else {
					$endDate = $year . '-' . ($month + 1) . '-' . '01 00:00:00';
				}
			} else {
				$startDate = $year . '-01-01 00:00:00';
				$endDate = ($year+1) . '12-31 23:59:59';
			}

			// Ensure that we never fetch back unpublished future posts.
			if(strtotime($endDate) > time()) {
				$endDate = date('Y-m-d H:i:s');
			}

			$query = $this->getBlogPosts()->dataQuery();

			$stage = $query->getQueryParam("Versioned.stage");
			if($stage) $stage = '_' . Convert::raw2sql($stage);

			$query->innerJoin("BlogPost", "`SiteTree" . $stage . "`.`ID` = `BlogPost" . $stage . "`.`ID`");
			$query->where("`PublishDate` >= '" . Convert::raw2sql($startDate) . "'
				AND `PublishDate` < '" . Convert::raw2sql($endDate) . "'");

			$this->blogPosts = $this->getBlogPosts()->setDataQuery($query);
			return $this->render();
		}
		return $this->httpError(404, "Not Found");
	}



	/**
	 * Renders the blog posts for a given tag.
	 *
	 * @return SS_HTTPResponse
	**/
	public function tag() {
		$tag = $this->getCurrentTag();
		if($tag) {
			$this->blogPosts = $tag->BlogPosts();
			return $this->render();
		}
		return $this->httpError(404, "Not Found");
	}



	/**
	 * Renders the blog posts for a given category
	 *
	 * @return SS_HTTPResponse
	**/
	public function category() {
		$category = $this->getCurrentCategory();
		if($category) {
			$this->blogPosts = $category->BlogPosts();
			return $this->render();
		}
		return $this->httpError(404, "Not Found");
	}



	/**
	 * Returns a list of paginated blog posts based on the blogPost dataList
	 *
	 * @return PaginatedList
	**/
	public function PaginatedList() {
		$posts = new PaginatedList($this->blogPosts);

		// If pagination is set to '0' then no pagination will be shown.
		if($this->PostsPerPage > 0) $posts->setPageLength($this->PostsPerPage);
		else $posts->setPageLength($this->getBlogPosts()->count());

		$start = $this->request->getVar($posts->getPaginationGetVar());
		$posts->setPageStart($start);

		return $posts;
	}



	/**
	 * Tag Getter for use in templates.
	 *
	 * @return BlogTag|null
	**/
	public function getCurrentTag() {
		$tag = $this->request->param("Tag");
		if($tag) {
			return $this->dataRecord->Tags()
				->filter("URLSegment", $tag)
				->first();
		}
		return null;
	}



	/**
	 * Category Getter for use in templates.
	 *
	 * @return BlogCategory|null
	**/
	public function getCurrentCategory() {
		$category = $this->request->param("Category");
		if($category) {
			return $this->dataRecord->Categories()
				->filter("URLSegment", $category)
				->first();
		}
		return null;
	}



	/**
	 * Fetches the archive year from the url
	 *
	 * @return int|null
	**/
	public function getArchiveYear() {
		$year = $this->request->param("Year");
		if(preg_match("/^[0-9]{4}$/", $year)) {
			return $year;
		}
		return null;
	}



	/**
	 * Fetches the archive money from the url.
	 *
	 * @return int|null
	**/
	public function getArchiveMonth() {
		$month = $this->request->param("Month");
		if(preg_match("/^[0-9]{1,2}$/", $month)) {
			if($month > 0 && $month < 13)
				return $month;
		}
		return null;
	}



	public function getArchiveDate() {
		$year = $this->getArchiveYear();
		$month = $this->getArchiveMonth();

		if($year) {
			if($month) {
				$startDate = $year . '-' . $month . '-01 00:00:00';
			} else {
				$startDate = $year . '-01-01 00:00:00';
			}
			$date = new Date("ArchiveDate");
			$date->setValue($startDate);
			return $date;
		}
	}

}