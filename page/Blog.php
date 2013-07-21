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

	private static $has_many = array(
		"Tags" => "BlogTag",
		"Categories" => "BlogCategory"
	);
	
	private static $allowed_children = array(
		"BlogPost"
	);


	/**
	 * Enable archive.
	 *
	 * @var boolean
	**/
	private static $archive_enabled = true;


	/**
	 * Enable tags
	 *
	 * @var boolean
	**/
	private static $tags_enabled = true;


	/**
	 * Enable categories
	 *
	 * @var boolean
	**/
	private static $categories_enabled = true;



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

		if($this->config()->get("categories_enabled")) {
			$fields->addFieldToTab("Root." . _t("Blog.CATEGORIES", "Categories"),
				GridField::create(
					"Categories",
					_t("Blog.FieldLabels.Categories", "Categories"),
					$this->Categories(),
					GridFieldConfig_AddByDBField::create()
				)
			);
		}

		if($this->config()->get("tags_enabled")) {
			$fields->addFieldToTab("Root." . _t("Blog.TAGS", "Tags"),
				GridField::create(
					"Tags",
					_t("Blog.FieldLabels.Tags", "Tags"),
					$this->Tags(),
					GridFieldConfig_AddByDBField::create()
				)
			);
		}
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
		return $this->AllChildren()->filter("ClassName", ClassInfo::subClassesFor("BlogPost"));
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
		'tag/$Tag' => "tag",
		'category/$Category' => "category",
	);

	protected $blogPosts;

	public function index() {
		$this->blogPosts = $this->AllChildren();
		return $this->render();
	}


	public function archive() {
		return $this->render();
	}


	public function tag() {
		$tag = $this->request->param("Tag");
		if($tag) {
			$tag = $this->Tags()->filter("Title", $tag)->first();
			if($tag) {
				$this->blogPosts = $this->AllChildren()->filter("ID", $tag->BlogPosts()->getIDList());
				return $this->render();
			}
		}
		return $this->httpError(404, "Not Found");
	}

	public function category() {
		$category = $this->request->param("Category");
		if($category) {
			$category = $this->Categories()->filter("Title", $category)->first();
			if($category) {
				$this->blogPosts = $this->AllChildren()->filter("ID", $category->BlogPosts()->getIDList());
				return $this->render();
			}
		}
		return $this->httpError(404, "Not Found");
	}

	public function PaginatedPosts() {
		return new PaginatedList($this->blogPosts);
	}
}