<?php

/**
 * An indivisual blog post.
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @method ManyManyList Categories()
 * @method ManyManyList Tags()
 * @method ManyManyList Authors()
 *
 * @author Michael Strong <github@michaelstrong.co.uk>
**/
class BlogPost extends Page {

	private static $db = array(
		"PublishDate" => "SS_Datetime",
		"AuthorNames" => "Varchar(1024)",
		"Summary" => "HTMLText",
	);

	private static $has_one = array(
		"FeaturedImage" => "Image",
	);

	private static $many_many = array(
		"Categories" => "BlogCategory",
		"Tags" => "BlogTag",
		"Authors" => "Member",
	);

	private static $defaults = array(
		"ShowInMenus" => false,
		"InheritSideBar" => true, // Support for widgets
		"ProvideComments" => true, // Support for comments
	);

	private static $extensions = array(
		"BlogPostFilter",
	);

	private static $searchable_fields = array(
		"Title",
	);

	private static $summary_fields = array(
		"Title",
	);

	private static $casting = array(
		'Excerpt' => 'Text'
	);

	private static $allowed_children = array();

	private static $default_sort = "PublishDate DESC";

	private static $can_be_root = false;

	/**
	 * This will display or hide the current class from the SiteTree. This
	 * variable can be configured using YAML.
	 *
	 * @var boolean
	**/
	private static $show_in_sitetree = false;


	/**
	 * Determine if the given member is an author of this post
	 *
	 * @param Member $member
	 * @return boolean
	 */
	public function isAuthor($member) {
		if(!$member || !$member->exists()) return false;

		$list = $this->Authors();
		if($list instanceof UnsavedRelationList) {
			return in_array($member->ID, $list->getIDList());
		}

		return $list->byID($member->ID) !== null;
	}

	/**
	 * Determine the role of the given member
	 * Call be called via template to determine the current user
	 *
	 * E.g. `Hello $RoleOf($CurrentMember.ID)`
	 *
	 * @param Member|integer $member
	 * @return string|null Author, Editor, Writer, Contributor, or null if no role
	 */
	public function RoleOf($member) {
		if(is_numeric($member)) $member = DataObject::get_by_id('Member', $member);
		if(!$member) return null;

		// Check if this member is an author
		if($this->isAuthor($member)) return _t("BlogPost.AUTHOR", "Author");

		// Check parent role
		$parent = $this->Parent();
		if($parent instanceof Blog) return $parent->RoleOf($member);
	}


	public function getCMSFields() {
		Requirements::css(BLOGGER_DIR . '/css/cms.css');
		Requirements::javascript(BLOGGER_DIR . '/js/cms.js');

		$self =& $this;
		$this->beforeUpdateCMSFields(function($fields) use ($self) {

			// Add blog summary
			$summaryHolder = ToggleCompositeField::create(
				'CustomSummary',
				_t('BlogPost.CUSTOMSUMMARY', 'Add A Custom Summary'),
				array(
					$summary = HtmlEditorField::create("Summary", false)
				)
			)
				->setHeadingLevel(4)
				->addExtraClass('custom-summary');
			$summary->setRows(5);
			$summary->setDescription(_t(
				'BlogPost.SUMMARY_DESCRIPTION',
				"If no summary is specified the first 30 words will be used."
			));
			$fields->insertBefore($summaryHolder, 'Content');

			// Add featured image
			$fields->insertAfter(
				$uploadField = UploadField::create("FeaturedImage", _t("BlogPost.FeaturedImage", "Featured Image")),
				"Content"
			);
			$uploadField->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'));

			// We're going to hide MenuTitle - Its not needed in blog posts.
			$fields->push(HiddenField::create('MenuTitle'));

			// We're going to add the url segment to sidebar so we're making it a little lighter
			$urlSegment = $fields->dataFieldByName('URLSegment');
			$urlSegment->setURLPrefix($self->Parent()->RelativeLink());

			// Remove the MenuTitle and URLSegment from the main tab
			$fields->removeFieldsFromTab('Root.Main', array(
				'MenuTitle',
				'URLSegment',
			));

			// Author field
			$authorField = ListboxField::create(
				"Authors",
				_t("BlogPost.Authors", "Authors"),
				Member::get()->map()->toArray()
			)->setMultiple(true);

			$authorNames = TextField::create(
				"AuthorNames",
				_t("BlogPost.AdditionalCredits", "Additional Credits"),
				null,
				1024
			)->setDescription('If some authors of this post don\'t have CMS access, enter their name(s) here. You can separate multiple names with a comma.');
			if(!$self->canEditAuthors()) {
				$authorField = $authorField->performDisabledTransformation();
				$authorNames = $authorNames->performDisabledTransformation();
			}

			// Build up our sidebar
			$options = BlogAdminSidebar::create(
				$publishDate = DatetimeField::create("PublishDate", _t("BlogPost.PublishDate", "Publish Date")),
				$urlSegment,
				TagField::create(
					'Categories',
					_t('BlogPost.Categories', 'Categories'),
					$self->Parent()->Categories()->map(),
					$self->Categories()->map(),
					!$self->canCreateCategories()
				),
				TagField::create(
					'Tags',
					_t('BlogPost.Tags', 'Tags'),
					$self->Parent()->Tags()->map(),
					$self->Tags()->map(),
					!$self->canCreateTags()
				),
				$authorField,
				$authorNames
			)->setTitle('Post Options');
			$publishDate->getDateField()->setConfig("showcalendar", true);

			// Insert it before the TabSet
			$fields->insertBefore($options, 'Root');
		});

		$fields = parent::getCMSFields();

		// We need to render an outer template to deal with our custom layout
		$fields->fieldByName('Root')->setTemplate('TabSet_holder');

		return $fields;
	}

	/**
	 * Determine whether user can create new categories.
	 *
	 * @param int|Member|null $member
	 *
	 * @return bool
	 */
	public function canCreateCategories($member = null) {
		$member = $member ?: Member::currentUser();
		if(is_numeric($member)) $member = Member::get()->byID($member);

		$parent = $this->Parent();

		if(!$parent || !$parent->exists() || !($parent instanceof Blog)) return false;

		if($parent->isEditor($member)) return true;

		return Permission::checkMember($member, 'ADMIN');
	}

	/**
	 * Determine whether user can create new tags.
	 *
	 * @param int|Member|null $member
	 *
	 * @return bool
	 */
	public function canCreateTags($member = null) {
		$member = $member ?: Member::currentUser();
		if(is_numeric($member)) $member = Member::get()->byID($member);

		$parent = $this->Parent();

		if(!$parent || !$parent->exists() || !($parent instanceof Blog)) return false;

		if($parent->isEditor($member)) return true;

		if($parent->isWriter($member)) return true;

		return Permission::checkMember($member, 'ADMIN');
	}

	protected function onBeforeWrite() {
		parent::onBeforeWrite();

		// If no publish date is set, set the date to now.
		if(!$this->PublishDate) $this->PublishDate = SS_Datetime::now()->getValue();

		// If creating a new entry, assign the current member as an author
		// This allows writers and contributors to then edit their new post
		if(!$this->exists() && ($member = Member::currentUser())) {
			$this->Authors()->add($member);
		}
	}



	/**
	 * Update the PublishDate to now, if being published for the first time, and the date hasn't been set to the future.
	**/
	public function onBeforePublish() {
		if ($this->dbObject('PublishDate')->InPast() && !$this->isPublished()) {
			$this->PublishDate = SS_Datetime::now()->getValue();
			$this->write();
		}
	}

	/**
	 * Sets blog relationship on all categories and tags assigned to this post.
	 *
	 * @throws ValidationException
	 */
	public function onAfterWrite()
	{
		parent::onAfterWrite();

		foreach ($this->Categories() as $category) {
			$category->BlogID = $this->ParentID;
			$category->write();
		}

		foreach ($this->Tags() as $tag) {
			$tag->BlogID = $this->ParentID;
			$tag->write();
		}
	}

	/**
	 * Checks the publish date to see if the blog post has actually been published.
	 *
	 * @param $member Member|null
	 *
	 * @return boolean
	**/
	public function canView($member = null) {
		if(!parent::canView($member)) return false;

		if($this->PublishDate) {
			$publishDate = $this->dbObject("PublishDate");
			if($publishDate->InFuture() && !Permission::checkMember($member, "VIEW_DRAFT_CONTENT")) {
				return false;
			}
		}
		return true;
	}

	public function canEdit($member = null) {
		$member = $member ?: Member::currentUser();
		if(is_numeric($member)) $member = Member::get()->byID($member);

		// Inherit permission
		if(parent::canEdit($member)) return true;

		// Check if assigned to a blog
		$parent = $this->Parent();
		if(!$parent || !$parent->exists() || !($parent instanceof Blog)) return false;

		// Editors have full control
		if($parent->isEditor($member)) return true;

		// Only writers or contributors can edit
		if(!$parent->isWriter($member) && !$parent->isContributor($member)) return false;

		// And only if they are also authors
		return $this->isAuthor($member);
	}

	/**
	 * Determine if this user can edit the authors list
	 *
	 * @param Member $member
	 * @return boolean
	 */
	public function canEditAuthors($member = null) {
		$member = $member ?: Member::currentUser();
		if(is_numeric($member)) $member = Member::get()->byID($member);

		$extended = $this->extendedCan('canEditAuthors', $member);
		if($extended !== null) return $extended;

		// Check blog roles
		$parent = $this->Parent();
		if($parent instanceof Blog && $parent->exists()) {
			// Editors can do anything
			if($parent->isEditor($member)) return true;

			// Writers who are also authors can edit authors
			if($parent->isWriter($member) && $this->isAuthor($member)) return true;
		}

		// Check permission
		return Permission::checkMember($member, Blog::MANAGE_USERS);
	}

	public function canPublish($member = null) {
		$member = $member ?: Member::currentUser();
		if(is_numeric($member)) $member = Member::get()->byID($member);

		if(Permission::checkMember($member, "ADMIN")) return true;

		// Standard mechanism for accepting permission changes from extensions
		$extended = $this->extendedCan('canPublish', $member);
		if($extended !== null) return $extended;

		// Check blog roles
		$parent = $this->Parent();
		if($parent instanceof Blog && $parent->exists()) {
			// Editors can do anything
			if($parent->isEditor($member)) return true;

			// Writers who are also authors can edit authors
			if($parent->isWriter($member) && $this->isAuthor($member)) return true;

			// Contributors can ONLY publish this page if they somehow have global publish permissions
			// In this case defer to old canEdit implementation
			if($parent->isContributor($member)) return parent::canEdit($member);
		}

		// Normal case - fail over to canEdit()
		return $this->canEdit($member);
	}


	/**
	 * Returns the post excerpt.
	 *
	 * @param $wordCount int - number of words to display
	 *
	 * @return string
	**/
	public function Excerpt($wordCount = 30) {
		return $this->dbObject("Content")->LimitWordCount($wordCount);
	}



	/**
	 * Returns a monthly archive link for the current blog post.
	 *
	 * @param $type string day|month|year
	 *
	 * @return string URL
	**/
	public function getMonthlyArchiveLink($type = "day") {
		$date = $this->dbObject("PublishDate");
		if($type != "year") {
			if($type == "day") {
				return Controller::join_links(
					$this->Parent()->Link("archive"), 
					$date->format("Y"), 
					$date->format("m"), 
					$date->format("d")
				);
			}
			return Controller::join_links($this->Parent()->Link("archive"), $date->format("Y"), $date->format("m"));
		}
		return Controller::join_links($this->Parent()->Link("archive"), $date->format("Y"));
	}



	/**
	 * Returns a yearly archive link for the current blog post.
	 *
	 * @return string URL
	**/
	public function getYearlyArchiveLink() {
		$date = $this->dbObject("PublishDate");
		return Controller::join_links($this->Parent()->Link("archive"), $date->format("Y"));
	}

	/**
	 * Resolves static and dynamic authors linked to this post.
	 *
	 * @return ArrayList
	 */
	public function getCredits()
	{
		$list = new ArrayList();

		$list->merge($this->getDynamicCredits());
		$list->merge($this->getStaticCredits());

		return $list->sort('Name');
	}

	/**
	 * Resolves dynamic authors linked to this post.
	 *
	 * @return ArrayList
	 */
	protected function getDynamicCredits()
	{
		$items = new ArrayList();

		foreach($this->Authors() as $author) {
			$items->push(
				$author->customise(array(
					'URL' => $this->Parent->ProfileLink($author->URLSegment),
				))
			);
		}

		return $items;
	}

	/**
	 * Resolves static authors linked to this post.
	 *
	 * @return ArrayList
	 */
	protected function getStaticCredits()
	{
		$items = new ArrayList();

		$authors = array_filter(preg_split('/\s*,\s*/', $this->AuthorNames));

		foreach ($authors as $author) {
			$item = new ArrayData(array(
				'Name' => $author,
			));

			$items->push($item);
		}

		return $items;
	}

	/**
	 * Sets the label for BlogPost.Title to 'Post Title' (Rather than 'Page name')
	 *
	 * @return array
	**/
	public function fieldLabels($includerelations = true) {   
		$labels = parent::fieldLabels($includerelations);
		$labels['Title'] = _t('BlogPost.PageTitleLabel', "Post Title");      
		return $labels;
	}

}


/**
 * Blog Post controller
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <github@michaelstrong.co.uk>
**/
class BlogPost_Controller extends Page_Controller {
	
}
