<?php

/**
 * An indivisual blog post.
 *
 * @package    silverstripe
 * @subpackage blog
 *
 * @method ManyManyList Categories()
 * @method ManyManyList Tags()
 * @method ManyManyList Authors()
 *
 * @author     Michael Strong <github@michaelstrong.co.uk>
 **/
class BlogPost extends Page {

	private static $db = array(
		"PublishDate" => "SS_Datetime",
		"AuthorNames" => "Varchar(1024)"
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
		"Title"
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
	 *
	 * @return boolean
	 */
	protected function isAuthor($member) {
		if(!$member || !$member->exists()) return false;

		$list = $this->Authors();
		if($list instanceof UnsavedRelationList) {
			return in_array($member->ID, $list->getIDList());
		}

		return $list->byID($member->ID) !== null;
	}


	public function getCMSFields() {
		Requirements::css(BLOGGER_DIR . '/css/cms.css');

		$self =& $this;
		$this->beforeUpdateCMSFields(function ($fields) use ($self) {

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
			)->setDescription('Comma separated list of names');
			if(!$self->canEditAuthors()) {
				$authorField = $authorField->performDisabledTransformation();
				$authorNames = $authorNames->performDisabledTransformation();
			}

			// Build up our sidebar
			$options = BlogAdminSidebar::create(
				$publishDate = DatetimeField::create("PublishDate", _t("BlogPost.PublishDate", "Publish Date")),
				$urlSegment,
				ListboxField::create(
					"Categories",
					_t("BlogPost.Categories", "Categories"),
					$self->Parent()->Categories()->map()->toArray()
				)->setMultiple(true),
				ListboxField::create(
					"Tags",
					_t("BlogPost.Tags", "Tags"),
					$self->Parent()->Tags()->map()->toArray()
				)->setMultiple(true),
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
	 * Update the PublishDate to now, if being published for the first time, and the date hasn't
	 * been set to the future.
	 **/
	public function onBeforePublish() {
		if($this->dbObject('PublishDate')->InPast() && !$this->isPublished()) {
			$this->PublishDate = SS_Datetime::now()->getValue();
			$this->write();
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
	 *
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
	 * Sets the label for BlogPost.Title to 'Post Title' (Rather than 'Page name')
	 *
	 * @return array
	 **/
	public function fieldLabels($includerelations = true) {
		$labels = parent::fieldLabels($includerelations);
		$labels['Title'] = _t('BlogPost.PageTitleLabel', "Post Title");
		return $labels;
	}

	/**
	 * Resolves dynamic and static authors with appropriate join data.
	 *
	 * @return ArrayList
	 */
	public function getCredits() {
		$dynamic = $this->getDynamicAuthors();

		$static = $this->getStaticAuthors();

		return new ArrayList(
			$this->itemsWithJoins(
				array_merge($dynamic, $static)
			)
		);
	}

	/**
	 * Resolves all dynamic authors linked to this post.
	 *
	 * @return array
	 */
	protected function getDynamicAuthors() {
		$items = [];

		$authors = $this->Authors()->toArray();

		foreach($authors as $author) {
			$item = new ArrayData(array(
				'Name' => $author->Name,
				'Join' => '',
				'URL' => '',
			));

			if($author->URLSegment) {
				$item->URL = $this->Parent->ProfileLink($author->URLSegment);
			}

			$items[] = $item;
		}

		return $items;
	}

	/**
	 * Resolves all static authors linked to this post.
	 *
	 * @return array
	 */
	protected function getStaticAuthors() {
		$items = [];

		$authors = array_filter(explode(',', $this->AuthorNames));

		foreach($authors as $author) {
			$item = new ArrayData(array(
				'Name' => $author,
				'Join' => '',
				'URL' => ''
			));

			$items[] = $item;
		}

		return $items;
	}

	/**
	 * Returns a new array with the appropriate join data.
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	protected function itemsWithJoins(array $items) {
		$count = count($items);

		for($i = 0; $i < $count; $i++) {
			if($count === 2 && $i > 0) {
				$items[$i]->Join = ' and ';
			}

			if($count > 2) {
				if($i > 0) {
					$items[$i]->Join = ', ';
				}

				if($i + 1 === $count) {
					$items[$i]->Join = ' and ';
				}
			}
		}

		return $items;
	}
}


/**
 * Blog Post controller
 *
 * @package    silverstripe
 * @subpackage blog
 *
 * @author     Michael Strong <github@michaelstrong.co.uk>
 **/
class BlogPost_Controller extends Page_Controller {

}
