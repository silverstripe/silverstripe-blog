<?php

/**
 * An individual blog post.
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @method ManyManyList Categories()
 * @method ManyManyList Tags()
 * @method ManyManyList Authors()
 * @method Blog Parent()
 *
 * @property string $PublishDate
 * @property string $AuthorNames
 * @property int $ParentID
 */
class BlogPost extends Page {
	/**
	 * @var array
	 */
	private static $db = array(
		'PublishDate' => 'SS_Datetime',
		'AuthorNames' => 'Varchar(1024)',
		'Summary' => 'HTMLText',
	);

	/**
	 * @var array
	 */
	private static $has_one = array(
		'FeaturedImage' => 'Image',
	);

	/**
	 * @var array
	 */
	private static $many_many = array(
		'Categories' => 'BlogCategory',
		'Tags' => 'BlogTag',
		'Authors' => 'Member',
	);

	/**
	 * @var array
	 */
	private static $defaults = array(
		'ShowInMenus' => false,
		'InheritSideBar' => true,
		'ProvideComments' => true,
	);

	/**
	 * @var array
	 */
	private static $extensions = array(
		'BlogPostFilter',
	);

	/**
	 * @var array
	 */
	private static $searchable_fields = array(
		'Title',
	);

	/**
	 * @var array
	 */
	private static $summary_fields = array(
		'Title',
	);

	/**
	 * @var array
	 */
	private static $casting = array(
		'Excerpt' => 'Text',
	);

	/**
	 * @var array
	 */
	private static $allowed_children = array();

	/**
	 * @var string
	 */
	private static $default_sort = 'PublishDate DESC';

	/**
	 * @var bool
	 */
	private static $can_be_root = false;

	/**
	 * This will display or hide the current class from the SiteTree. This variable can be
	 * configured using YAML.
	 *
	 * @var bool
	 */
	private static $show_in_sitetree = false;

	/**
	 * Determine the role of the given member.
	 *
	 * Call be called via template to determine the current user.
	 *
	 * @example "Hello $RoleOf($CurrentMember.ID)"
	 *
	 * @param null|int|Member $member
	 *
	 * @return null|string
	 */
	public function RoleOf($member = null) {
		$member = $this->getMember($member);

		if(!$member) {
			return null;
		}

		if($this->isAuthor($member)) {
			return _t('BlogPost.AUTHOR', 'Author');
		}

		$parent = $this->Parent();

		if($parent instanceof Blog) {
			return $parent->RoleOf($member);
		}

		return null;
	}

	/**
	 * Determine if the given member is an author of this post.
	 *
	 * @param null|Member $member
	 *
	 * @return bool
	 */
	public function isAuthor($member = null) {
		if(!$member || !$member->exists()) {
			return false;
		}

		$list = $this->Authors();

		if($list instanceof UnsavedRelationList) {
			return in_array($member->ID, $list->getIDList());
		}

		return $list->byID($member->ID) !== null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCMSFields() {
		Requirements::css(BLOGGER_DIR . '/css/cms.css');
		Requirements::javascript(BLOGGER_DIR . '/js/cms.js');

		$self =& $this;

		$this->beforeUpdateCMSFields(function ($fields) use ($self) {
			$uploadField = UploadField::create('FeaturedImage', _t('BlogPost.FeaturedImage', 'Featured Image'));
			$uploadField->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'));

			/**
			 * @var FieldList $fields
			 */
			$fields->insertAfter($uploadField, 'Content');

			$summary = HtmlEditorField::create('Summary', false);
			$summary->setRows(5);
			$summary->setDescription(_t(
				'BlogPost.SUMMARY_DESCRIPTION',
				'If no summary is specified the first 30 words will be used.'
			));

			$summaryHolder = ToggleCompositeField::create(
				'CustomSummary',
				_t('BlogPost.CUSTOMSUMMARY', 'Add A Custom Summary'),
				array(
					$summary,
				)
			);
			$summaryHolder->setHeadingLevel(4);
			$summaryHolder->addExtraClass('custom-summary');

			$fields->insertAfter($summaryHolder, 'FeaturedImage');

			$fields->push(HiddenField::create('MenuTitle'));

			$urlSegment = $fields->dataFieldByName('URLSegment');
			$urlSegment->setURLPrefix($self->Parent()->RelativeLink());

			$fields->removeFieldsFromTab('Root.Main', array(
				'MenuTitle',
				'URLSegment',
			));

			$authorField = ListboxField::create(
				'Authors',
				_t('BlogPost.Authors', 'Authors'),
				Member::get()->map()->toArray()
			)->setMultiple(true);

			$authorNames = TextField::create(
				'AuthorNames',
				_t('BlogPost.AdditionalCredits', 'Additional Credits'),
				null,
				1024
			);
			$authorNames->setDescription('If some authors of this post don\'t have CMS access, enter their name(s) here. You can separate multiple names with a comma.');

			if(!$self->canEditAuthors()) {
				$authorField = $authorField->performDisabledTransformation();
				$authorNames = $authorNames->performDisabledTransformation();
			}

			$publishDate = DatetimeField::create('PublishDate', _t('BlogPost.PublishDate', 'Publish Date'));
			$publishDate->getDateField()->setConfig('showcalendar', true);

			$options = BlogAdminSidebar::create(
				$publishDate,
				$urlSegment,
				TagField::create(
					'Categories',
					_t('BlogPost.Categories', 'Categories'),
					$self->Parent()->Categories(),
					$self->Categories()
				)
					->setCanCreate($self->canCreateCategories())
					->setShouldLazyLoad(true),
				TagField::create(
					'Tags',
					_t('BlogPost.Tags', 'Tags'),
					$self->Parent()->Tags(),
					$self->Tags()
				)
					->setCanCreate($self->canCreateTags())
					->setShouldLazyLoad(true),
				$authorField,
				$authorNames
			)->setTitle('Post Options');

			$fields->insertBefore($options, 'Root');
		});

		$fields = parent::getCMSFields();

		$fields->fieldByName('Root')->setTemplate('TabSet_holder');

		return $fields;
	}

	/**
	 * Determine if this user can edit the authors list.
	 *
	 * @param null|int|Member $member
	 *
	 * @return bool
	 */
	public function canEditAuthors($member = null) {
		$member = $this->getMember($member);

		$extended = $this->extendedCan('canEditAuthors', $member);

		if($extended !== null) {
			return $extended;
		}

		$parent = $this->Parent();

		if($parent instanceof Blog && $parent->exists()) {
			if($parent->isEditor($member)) {
				return true;
			}

			if($parent->isWriter($member) && $this->isAuthor($member)) {
				return true;
			}
		}

		return Permission::checkMember($member, Blog::MANAGE_USERS);
	}

	/**
	 * @param null|int|Member $member
	 *
	 * @return null|Member
	 */
	protected function getMember($member = null) {
		if(!$member) {
			$member = Member::currentUser();
		}

		if(is_numeric($member)) {
			$member = Member::get()->byID($member);
		}

		return $member;
	}

	/**
	 * Determine whether user can create new categories.
	 *
	 * @param null|int|Member $member
	 *
	 * @return bool
	 */
	public function canCreateCategories($member = null) {
		$member = $this->getMember($member);

		$parent = $this->Parent();

		if(!$parent || !$parent->exists() || !($parent instanceof Blog)) {
			return false;
		}

		if($parent->isEditor($member)) {
			return true;
		}

		return Permission::checkMember($member, 'ADMIN');
	}

	/**
	 * Determine whether user can create new tags.
	 *
	 * @param null|int|Member $member
	 *
	 * @return bool
	 */
	public function canCreateTags($member = null) {
		$member = $this->getMember($member);

		$parent = $this->Parent();

		if(!$parent || !$parent->exists() || !($parent instanceof Blog)) {
			return false;
		}

		if($parent->isEditor($member)) {
			return true;
		}

		if($parent->isWriter($member)) {
			return true;
		}

		return Permission::checkMember($member, 'ADMIN');
	}

	/**
	 * {@inheritdoc}
	 *
	 * Update the PublishDate to now, if being published for the first time, and the date hasn't
	 * been set to the future.
	 */
	public function onBeforePublish() {
		/**
		 * @var SS_Datetime $publishDate
		 */
		$publishDate = $this->dbObject('PublishDate');

		if($publishDate->InPast() && !$this->isPublished()) {
			$this->PublishDate = SS_Datetime::now()->getValue();
			$this->write();
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * Sets blog relationship on all categories and tags assigned to this post.
	 */
	public function onAfterWrite() {
		parent::onAfterWrite();

		foreach($this->Categories() as $category) {
			/**
			 * @var BlogCategory $category
			 */
			$category->BlogID = $this->ParentID;
			$category->write();
		}

		foreach($this->Tags() as $tag) {
			/**
			 * @var BlogTag $tag
			 */
			$tag->BlogID = $this->ParentID;
			$tag->write();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function canView($member = null) {
		if(!parent::canView($member)) {
			return false;
		}

		if($this->PublishDate) {
			/**
			 * @var SS_Datetime $publishDate
			 */
			$publishDate = $this->dbObject('PublishDate');

			if($publishDate->InFuture() && !Permission::checkMember($member, 'VIEW_DRAFT_CONTENT')) {
				return false;
			}
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function canPublish($member = null) {
		$member = $this->getMember($member);

		if(Permission::checkMember($member, 'ADMIN')) {
			return true;
		}

		$extended = $this->extendedCan('canPublish', $member);

		if($extended !== null) {
			return $extended;
		}

		$parent = $this->Parent();

		if($parent instanceof Blog && $parent->exists()) {
			if($parent->isEditor($member)) {
				return true;
			}

			if($parent->isWriter($member) && $this->isAuthor($member)) {
				return true;
			}

			if($parent->isContributor($member)) {
				return parent::canEdit($member);
			}
		}

		return $this->canEdit($member);
	}

	/**
	 * {@inheritdoc}
	 */
	public function canEdit($member = null) {
		$member = $this->getMember($member);

		if(parent::canEdit($member)) {
			return true;
		}

		$parent = $this->Parent();

		if(!$parent || !$parent->exists() || !($parent instanceof Blog)) {
			return false;
		}

		if($parent->isEditor($member)) {
			return true;
		}

		if(!$parent->isWriter($member) && !$parent->isContributor($member)) {
			return false;
		}

		return $this->isAuthor($member);
	}

	/**
	 * Returns the post excerpt.
	 *
	 * @param int $wordsToDisplay
	 *
	 * @return string
	 */
	public function Excerpt($wordsToDisplay = 30) {
		/**
		 * @var Text $content
		 */
		$content = $this->dbObject('Content');

		return $content->Summary($wordsToDisplay);
	}

	/**
	 * Returns a monthly archive link for the current blog post.
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	public function getMonthlyArchiveLink($type = 'day') {
		/**
		 * @var SS_Datetime $date
		 */
		$date = $this->dbObject('PublishDate');

		if($type != 'year') {
			if($type == 'day') {
				return Controller::join_links(
					$this->Parent()->Link('archive'),
					$date->format('Y'),
					$date->format('m'),
					$date->format('d')
				);
			}

			return Controller::join_links($this->Parent()->Link('archive'), $date->format('Y'), $date->format('m'));
		}

		return Controller::join_links($this->Parent()->Link('archive'), $date->format('Y'));
	}

	/**
	 * Returns a yearly archive link for the current blog post.
	 *
	 * @return string
	 */
	public function getYearlyArchiveLink() {
		/**
		 * @var SS_Datetime $date
		 */
		$date = $this->dbObject('PublishDate');

		return Controller::join_links($this->Parent()->Link('archive'), $date->format('Y'));
	}

	/**
	 * Resolves static and dynamic authors linked to this post.
	 *
	 * @return ArrayList
	 */
	public function getCredits() {
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
	protected function getDynamicCredits() {
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
	protected function getStaticCredits() {
		$items = new ArrayList();

		$authors = array_filter(preg_split('/\s*,\s*/', $this->AuthorNames));

		foreach($authors as $author) {
			$item = new ArrayData(array(
				'Name' => $author,
			));

			$items->push($item);
		}

		return $items;
	}

	/**
	 * Sets the label for BlogPost.Title to 'Post Title' (Rather than 'Page name').
	 *
	 * @param bool $includeRelations
	 *
	 * @return array
	 */
	public function fieldLabels($includeRelations = true) {
		$labels = parent::fieldLabels($includeRelations);

		$labels['Title'] = _t('BlogPost.PageTitleLabel', "Post Title");

		return $labels;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function onBeforeWrite() {
		parent::onBeforeWrite();

		if(!$this->PublishDate) {
			$this->PublishDate = SS_Datetime::now()->getValue();
		}

		if(!$this->exists() && ($member = Member::currentUser())) {
			$this->Authors()->add($member);
		}
	}
}

/**
 * @package silverstripe
 * @subpackage blog
 */
class BlogPost_Controller extends Page_Controller {

}
