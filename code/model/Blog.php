<?php

/**
 * Blog Holder
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <github@michaelstrong.co.uk>
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


	private static $defaults = array(
		"ProvideComments" => false,
	);

	private static $description = 'Adds a blog to your website.';


	public function getCMSFields() {
		$self =& $this;
		$this->beforeUpdateCMSFields(function($fields) use ($self) {

			// Create categories and tag config
			$config = GridFieldConfig_RecordEditor::create();
			$config->removeComponentsByType("GridFieldAddNewButton");
			$config->addComponent(new GridFieldAddByDBField("buttons-before-left"));

			$categories = GridField::create(
				"Categories",
				_t("Blog.Categories", "Categories"),
				$self->Categories(),
				$config
			);

			$tags = GridField::create(
				"Tags",
				_t("Blog.Tags", "Tags"),
				$self->Tags(),
				$config
			);

			$fields->addFieldsToTab("Root.BlogOptions", array(
				$categories,
				$tags
			));

		});
		
		$fields = parent::getCMSFields();
		return $fields;
	}



	public function getSettingsFields() {
		$fields = parent::getSettingsFields();
		$fields->addFieldToTab("Root.Settings", 
			NumericField::create("PostsPerPage", _t("Blog.PostsPerPage", "Posts Per Page"))
		);
		return $fields;
	}



	/**
	 * Return blog posts
	 *
	 * @return DataList of BlogPost objects
	**/
	public function getBlogPosts() {
		$blogPosts = BlogPost::get()->filter("ParentID", $this->ID);
		//Allow decorators to manipulate list
		$this->extend('updateGetBlogPosts', $blogPosts);
		return $blogPosts;
	}



	/**
	 * Returns blogs posts for a given date period.
	 *
	 * @param $year int
	 * @param $month int
	 * @param $dat int
	 *
	 * @return DataList
	**/
	public function getArchivedBlogPosts($year, $month = null, $day = null) {
		$query = $this->getBlogPosts()->dataQuery();

		$stage = $query->getQueryParam("Versioned.stage");
		if($stage) $stage = '_' . Convert::raw2sql($stage);

		$query->innerJoin("BlogPost", "`SiteTree" . $stage . "`.`ID` = `BlogPost" . $stage . "`.`ID`");
		$query->where("YEAR(PublishDate) = '" . Convert::raw2sql($year) . "'");
		if($month) {
			$query->where("MONTH(PublishDate) = '" . Convert::raw2sql($month) . "'");
			if($day) {
				$query->where("DAY(PublishDate) = '" . Convert::raw2sql($day) . "'");
			}
		}

		return $this->getBlogPosts()->setDataQuery($query);
	}



	/**
	 * This sets the title for our gridfield
	 *
	 * @return string
	 */
	public function getLumberjackTitle() {
		return _t('Blog.LumberjackTitle', 'Blog Posts');
	}



	/**
	 * This overwrites lumberjacks default gridfield config.
	 *
	 * @return GridFieldConfig
	 */
	public function getLumberjackGridFieldConfig() {
		return GridFieldConfig_BlogPost::create();
	}

}



/**
 * Blog Controller
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <github@michaelstrong.co.uk>
**/
class Blog_Controller extends Page_Controller {

	private static $allowed_actions = array(
		'archive',
		'tag',
		'category',
		'rss',
	);

	private static $url_handlers = array(
		'tag/$Tag!' => 'tag',
		'category/$Category!' => 'category',
		'archive/$Year!/$Month/$Day' => 'archive',
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
		$day = $this->getArchiveDay();

		// If an invalid month has been passed, we can return a 404.
		if($this->request->param("Month") && !$month) {
			return $this->httpError(404, "Not Found");
		}

		// Check for valid day
		if($month && $this->request->param("Day") && !$day) {
			return $this->httpError(404, "Not Found");
		}

		if($year) {
			$this->blogPosts = $this->getArchivedBlogPosts($year, $month, $day);
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
	 * Displays an RSS feed of blog posts
	 *
	 * @return string HTML
	**/
	public function rss() {
		$rss = new RSSFeed($this->getBlogPosts(), $this->Link(), $this->MetaTitle, $this->MetaDescription);
		$this->extend('updateRss', $rss);
		return $rss->outputToBrowser();
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
			return (int) $year;
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
			if($month > 0 && $month < 13) {
				// Check that we have a valid date.
				if(checkdate($month, 01, $this->getArchiveYear())) {
					return (int) $month;
				}
			}
		}
		return null;
	}



	/**
	 * Fetches the archive day from the url
	 *
	 * @return int|null
	**/
	public function getArchiveDay() {
		$day = $this->request->param("Day");
		if(preg_match("/^[0-9]{1,2}$/", $day)) {

			// Check that we have a valid date
			if(checkdate($this->getArchiveMonth(), $day, $this->getArchiveYear())) {
				return (int) $day;
			}
		}
		return null;
	}



	/**
	 * Returns the current archive date.
	 *
	 * @return Date
	**/
	public function getArchiveDate() {
		$year = $this->getArchiveYear();
		$month = $this->getArchiveMonth();
		$day = $this->getArchiveDay();

		if($year) {
			if($month) {
				$date = $year . '-' . $month . '-01';
				if($day) {
					$date = $year . '-' . $month . '-' . $day;
				}
			} else {
				$date = $year . '-01-01';
			}
			return DBField::create_field("Date", $date);
		}
	}



	/**
	 * Returns a link to the RSS feed.
	 *
	 * @return string URL
	**/
	public function getRSSLink() {
		return $this->Link("rss");
	}

}
