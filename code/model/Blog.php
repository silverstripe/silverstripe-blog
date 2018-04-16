<?php

/**
 * Blog Holder
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @method HasManyList Tags() List of tags in this blog
 * @method HasManyList Categories() List of categories in this blog
 * @method ManyManyList Editors() List of editors
 * @method ManyManyList Writers() List of writers
 * @method ManyManyList Contributors() List of contributors
 */
class Blog extends Page implements PermissionProvider
{
    /**
     * Permission for user management.
     *
     * @var string
     */
    const MANAGE_USERS = 'BLOG_MANAGE_USERS';

    /**
     * If true, users assigned as editor, writer, or contributor will be automatically granted
     * CMS_ACCESS_CMSMain permission. If false, only users with this permission already may be
     * assigned.
     *
     * @config
     *
     * @var boolean
     */
    private static $grant_user_access = true;

    /**
     * Permission to either require, or grant to users assigned to work on this blog.
     *
     * @config
     *
     * @var string
     */
    private static $grant_user_permission = 'CMS_ACCESS_CMSMain';

    /**
     * Group code to assign newly granted users to.
     *
     * @config
     *
     * @var string
     */
    private static $grant_user_group = 'blog-users';

    /**
     * @var array
     */
    private static $db = array(
        'PostsPerPage' => 'Int',
    );

    /**
     * @var array
     */
    private static $has_many = array(
        'Tags' => 'BlogTag',
        'Categories' => 'BlogCategory',
    );

    /**
     * @var array
     */
    private static $many_many = array(
        'Editors' => 'Member',
        'Writers' => 'Member',
        'Contributors' => 'Member',
    );

    /**
     * @var array
     */
    private static $allowed_children = array(
        'BlogPost',
    );

    /**
     * @var array
     */
    private static $extensions = array(
        'BlogFilter',
    );

    /**
     * @var array
     */
    private static $defaults = array(
        'ProvideComments' => false,
        'PostsPerPage' => 10,
    );

    /**
     * @var string
     */
    private static $description = 'Adds a blog to your website.';

    private static $icon = 'blog/images/site-tree-icon.png';

    /**
     * {@inheritdoc}
     */
    public function getCMSFields()
    {
        Requirements::css(BLOGGER_DIR . '/css/cms.css');
        Requirements::javascript(BLOGGER_DIR . '/js/cms.js');

        $self =& $this;

        $this->beforeUpdateCMSFields(function ($fields) use ($self) {
            if (!$self->canEdit()) {
                return;
            }

            $categories = GridField::create(
                'Categories',
                _t('Blog.Categories', 'Categories'),
                $self->Categories(),
                GridFieldCategorisationConfig::create(15, $self->Categories()->sort('Title'), 'BlogCategory', 'Categories', 'BlogPosts')
            );

            $tags = GridField::create(
                'Tags',
                _t('Blog.Tags', 'Tags'),
                $self->Tags(),
                GridFieldCategorisationConfig::create(15, $self->Tags()->sort('Title'), 'BlogTag', 'Tags', 'BlogPosts')
            );

            /**
             * @var FieldList $fields
             */
            $fields->addFieldsToTab('Root.Categorisation', array(
                $categories,
                $tags
            ));

            $fields->findOrMakeTab('Root.Categorisation')->addExtraClass('blog-cms-categorisation');
        });

        return parent::getCMSFields();
    }

    /**
     * {@inheritdoc}
     */
    public function canEdit($member = null)
    {
        $member = $this->getMember($member);

        if ($this->isEditor($member)) {
            return true;
        }

        return parent::canEdit($member);
    }

    /**
     * @param null|int|Member $member
     *
     * @return null|Member
     */
    protected function getMember($member = null)
    {
        if (!$member) {
            $member = Member::currentUser();
        }

        if (is_numeric($member)) {
            $member = Member::get()->byID($member);
        }

        return $member;
    }

    /**
     * Check if this member is an editor of the blog.
     *
     * @param Member $member
     *
     * @return bool
     */
    public function isEditor($member)
    {
        $isEditor = $this->isMemberOf($member, $this->Editors());
        $this->extend('updateIsEditor', $isEditor, $member);

        return $isEditor;
    }

    /**
     * Determine if the given member belongs to the given relation.
     *
     * @param Member $member
     * @param DataList $relation
     *
     * @return bool
     */
    protected function isMemberOf($member, $relation)
    {
        if (!$member || !$member->exists()) {
            return false;
        }

        if ($relation instanceof UnsavedRelationList) {
            return in_array($member->ID, $relation->getIDList());
        }

        return $relation->byID($member->ID) !== null;
    }

    /**
     * Determine the role of the given member.
     *
     * Call be called via template to determine the current user.
     *
     * @example "Hello $RoleOf($CurrentMember.ID)"
     *
     * @param int|Member $member
     *
     * @return null|string
     */
    public function RoleOf($member)
    {
        if (is_numeric($member)) {
            $member = DataObject::get_by_id('Member', $member);
        }

        if (!$member) {
            return null;
        }

        if ($this->isEditor($member)) {
            return _t('Blog.EDITOR', 'Editor');
        }

        if ($this->isWriter($member)) {
            return _t('Blog.WRITER', 'Writer');
        }

        if ($this->isContributor($member)) {
            return _t('Blog.CONTRIBUTOR', 'Contributor');
        }

        return null;
    }

    /**
     * Check if this member is a writer of the blog.
     *
     * @param Member $member
     *
     * @return bool
     */
    public function isWriter($member)
    {
        $isWriter = $this->isMemberOf($member, $this->Writers());
        $this->extend('updateIsWriter', $isWriter, $member);

        return $isWriter;
    }

    /**
     * Check if this member is a contributor of the blog.
     *
     * @param Member $member
     *
     * @return bool
     */
    public function isContributor($member)
    {
        $isContributor = $this->isMemberOf($member, $this->Contributors());
        $this->extend('updateIsContributor', $isContributor, $member);

        return $isContributor;
    }

    /**
     * {@inheritdoc}
     */
    public function canAddChildren($member = null)
    {
        $member = $this->getMember($member);

        if ($this->isEditor($member) || $this->isWriter($member) || $this->isContributor($member)) {
            return true;
        }

        return parent::canAddChildren($member);
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsFields()
    {
        $fields = parent::getSettingsFields();

        $fields->addFieldToTab('Root.Settings',
            NumericField::create('PostsPerPage', _t('Blog.PostsPerPage', 'Posts Per Page'))
        );

        $members = $this->getCandidateUsers()->map()->toArray();

        $editorField = ListboxField::create('Editors', 'Editors', $members)
            ->setMultiple(true)
            ->setRightTitle('<a class="toggle-description">help</a>')
            ->setDescription('
				An editor has control over specific Blogs, and all posts included within it. Short of being able to assign other editors to a blog, they are able to handle most changes to their assigned blog.<br />
				<br />
				Editors have these permissions:<br />
				<br />
				Update or publish any BlogPost in their Blog<br />
				Update or publish their Blog<br />
				Assign/unassign writers to their Blog<br />
				Assign/unassign contributors to their Blog<br />
				Assign/unassign any member as an author of a particular BlogPost
			');

        if (!$this->canEditEditors()) {
            $editorField = $editorField->performDisabledTransformation();
        }

        $writerField = ListboxField::create('Writers', 'Writers', $members)
            ->setMultiple(true)
            ->setRightTitle('<a class="toggle-description">help</a>')
            ->setDescription('
				A writer has full control over creating, editing and publishing BlogPosts they have authored or have been assigned to. Writers are unable to edit BlogPosts to which they are not assigned.<br />
				<br />
				Writers have these permissions:<br />
				<br />
				Update or publish any BlogPost they have authored or have been assigned to<br />
				Assign/unassign any member as an author of a particular BlogPost they have authored or have been assigned to
			');

        if (!$this->canEditWriters()) {
            $writerField = $writerField->performDisabledTransformation();
        }

        $contributorField = ListboxField::create('Contributors', 'Contributors', $members)
            ->setMultiple(true)
            ->setRightTitle('<a class="toggle-description">help</a>')
            ->setDescription('
				Contributors have the ability to create or edit BlogPosts, but are unable to publish without authorisation of an editor. They are also unable to assign other contributing authors to any of their BlogPosts.<br />
				<br />
				Contributors have these permissions:<br />
				<br />
				Update any BlogPost they have authored or have been assigned to
			');

        if (!$this->canEditContributors()) {
            $contributorField = $contributorField->performDisabledTransformation();
        }

        $fields->addFieldsToTab('Root.Users', array(
            $editorField,
            $writerField,
            $contributorField
        ));

        return $fields;
    }

    /**
     * Gets the list of user candidates to be assigned to assist with this blog.
     *
     * @return SS_List
     */
    protected function getCandidateUsers()
    {
        if ($this->config()->grant_user_access) {
            $list = Member::get();
            $this->extend('updateCandidateUsers', $list);
            return $list;
        } else {
            return Permission::get_members_by_permission(
                $this->config()->grant_user_permission
            );
        }
    }

    /**
     * Determine if this user can edit the editors list.
     *
     * @param int|Member $member
     *
     * @return bool
     */
    public function canEditEditors($member = null)
    {
        $member = $this->getMember($member);

        $extended = $this->extendedCan('canEditEditors', $member);

        if ($extended !== null) {
            return $extended;
        }

        return Permission::checkMember($member, self::MANAGE_USERS);
    }

    /**
     * Determine if this user can edit writers list.
     *
     * @param int|Member $member
     *
     * @return boolean
     */
    public function canEditWriters($member = null)
    {
        $member = $this->getMember($member);

        $extended = $this->extendedCan('canEditWriters', $member);

        if ($extended !== null) {
            return $extended;
        }

        if ($this->isEditor($member)) {
            return true;
        }

        return Permission::checkMember($member, self::MANAGE_USERS);
    }

    /**
     * Determines if this user can edit the contributors list.
     *
     * @param int|Member $member
     *
     * @return boolean
     */
    public function canEditContributors($member = null)
    {
        $member = $this->getMember($member);

        $extended = $this->extendedCan('canEditContributors', $member);

        if ($extended !== null) {
            return $extended;
        }

        if ($this->isEditor($member)) {
            return true;
        }

        return Permission::checkMember($member, self::MANAGE_USERS);
    }

    /**
     * Returns BlogPosts for a given date period.
     *
     * @param int $year
     * @param null|int $month
     * @param null|int $day
     *
     * @return DataList
     */
    public function getArchivedBlogPosts($year, $month = null, $day = null)
    {
        $query = $this->getBlogPosts()->dataQuery();

        $stage = $query->getQueryParam('Versioned.stage');

        if ($stage) {
            $stage = '_' . $stage;
        }

        $query->innerJoin('BlogPost', sprintf('"SiteTree%s"."ID" = "BlogPost%s"."ID"', $stage, $stage));

        $conn = DB::getConn();

        // Filter by year
        $yearCond = $conn->formattedDatetimeClause('"BlogPost"."PublishDate"', '%Y');
        $query->where(sprintf('%s = \'%04d\'', $yearCond, Convert::raw2sql($year)));

        // Filter by month (if given)
        if ($month) {
            $monthCond = $conn->formattedDatetimeClause('"BlogPost"."PublishDate"', '%m');
            $query->where(sprintf('%s = \'%02d\'', $monthCond, Convert::raw2sql($month)));

            if ($day) {
                $dayCond = $conn->formattedDatetimeClause('"BlogPost"."PublishDate"', '%d');
                $query->where(sprintf('%s = \'%02d\'', $dayCond, Convert::raw2sql($day)));
            }
        }


        return $this->getBlogPosts()->setDataQuery($query);
    }

    /**
     * Return blog posts.
     *
     * @return DataList of BlogPost objects
     */
    public function getBlogPosts()
    {
        $blogPosts = BlogPost::get()->filter('ParentID', $this->ID);

        $this->extend('updateGetBlogPosts', $blogPosts);

        return $blogPosts;
    }

    /**
     * Get a link to a Member profile.
     *
     * @param string $urlSegment
     *
     * @return string
     */
    public function ProfileLink($urlSegment)
    {
        return Controller::join_links($this->Link(), 'profile', $urlSegment);
    }

    /**
     * This sets the title for our gridfield.
     *
     * @return string
     */
    public function getLumberjackTitle()
    {
        return _t('Blog.LumberjackTitle', 'Blog Posts');
    }

    /**
     * This overwrites lumberjacks default gridfield config.
     *
     * @return GridFieldConfig
     */
    public function getLumberjackGridFieldConfig()
    {
        return GridFieldConfig_BlogPost::create();
    }

    /**
     * {@inheritdoc}
     */
    public function providePermissions()
    {
        return array(
            Blog::MANAGE_USERS => array(
                'name' => _t(
                    'Blog.PERMISSION_MANAGE_USERS_DESCRIPTION',
                    'Manage users for individual blogs'
                ),
                'help' => _t(
                    'Blog.PERMISSION_MANAGE_USERS_HELP',
                    'Allow assignment of Editors, Writers, or Contributors to blogs'
                ),
                'category' => _t('Blog.PERMISSIONS_CATEGORY', 'Blog permissions'),
                'sort' => 100
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->assignGroup();
    }

    /**
     * Assign users as necessary to the blog group.
     */
    protected function assignGroup()
    {
        if (!$this->config()->grant_user_access) {
            return;
        }

        $group = $this->getUserGroup();

        // Must check if the method exists or else an error occurs when changing page type
        if ($this->hasMethod('Editors')) {
            foreach (array($this->Editors(), $this->Writers(), $this->Contributors()) as $levels) {
                foreach ($levels as $user) {
                    if (!$user->inGroup($group)) {
                        $user->Groups()->add($group);
                    }
                }
            }
        }
    }

    /**
     * Gets or creates the group used to assign CMS access.
     *
     * @return Group
     */
    protected function getUserGroup()
    {
        $code = $this->config()->grant_user_group;

        $group = Group::get()->filter('Code', $code)->first();

        if ($group) {
            return $group;
        }

        $group = new Group();
        $group->Title = 'Blog users';
        $group->Code = $code;

        $group->write();

        $permission = new Permission();
        $permission->Code = $this->config()->grant_user_permission;

        $group->Permissions()->add($permission);

        return $group;
    }
}

/**
 * @package silverstripe
 * @subpackage blog
 */
class Blog_Controller extends Page_Controller
{
    /**
     * @var array
     */
    private static $allowed_actions = array(
        'archive',
        'tag',
        'category',
        'rss',
        'profile',
    );

    /**
     * @var array
     */
    private static $url_handlers = array(
        'tag/$Tag!/$Rss' => 'tag',
        'category/$Category!/$Rss' => 'category',
        'archive/$Year!/$Month/$Day' => 'archive',
        'profile/$URLSegment!' => 'profile',
    );

    /**
     * @var array
     */
    private static $casting = array(
        'MetaTitle' => 'Text',
        'FilterDescription' => 'Text',
    );

    /**
     * The current Blog Post DataList query.
     *
     * @var DataList
     */
    protected $blogPosts;

    /**
     * @return string
     */
    public function index()
    {
        /**
         * @var Blog $dataRecord
         */
        $dataRecord = $this->dataRecord;

        $this->blogPosts = $dataRecord->getBlogPosts();

        return $this->render();
    }

    /**
     * Renders a Blog Member's profile.
     *
     * @return SS_HTTPResponse
     */
    public function profile()
    {
        $profile = $this->getCurrentProfile();

        if (!$profile) {
            return $this->httpError(404, 'Not Found');
        }

        $this->blogPosts = $this->getCurrentProfilePosts();

        return $this->render();
    }

    /**
     * Get the Member associated with the current URL segment.
     *
     * @return null|Member
     */
    public function getCurrentProfile()
    {
        $urlSegment = $this->request->param('URLSegment');

        if ($urlSegment) {
            $filter = URLSegmentFilter::create();
            $filter->setAllowMultibyte(true);

            return Member::get()
                ->filter('URLSegment', $filter->filter($urlSegment))
                ->first();
        }

        return null;
    }

    /**
     * Get posts related to the current Member profile.
     *
     * @return null|DataList
     */
    public function getCurrentProfilePosts()
    {
        $profile = $this->getCurrentProfile();

        if ($profile) {
            return $profile->BlogPosts()->filter('ParentID', $this->ID);
        }

        return null;
    }

    /**
     * Renders an archive for a specified date. This can be by year or year/month.
     *
     * @return null|SS_HTTPResponse
     */
    public function archive()
    {
        /**
         * @var Blog $dataRecord
         */
        $dataRecord = $this->dataRecord;

        $year = $this->getArchiveYear();
        $month = $this->getArchiveMonth();
        $day = $this->getArchiveDay();

        if ($this->request->param('Month') && !$month) {
            $this->httpError(404, 'Not Found');
        }

        if ($month && $this->request->param('Day') && !$day) {
            $this->httpError(404, 'Not Found');
        }

        if ($year) {
            $this->blogPosts = $dataRecord->getArchivedBlogPosts($year, $month, $day);
            return $this->render();
        }

        $this->httpError(404, 'Not Found');

        return null;
    }

    /**
     * Fetches the archive year from the url.
     *
     * @return int
     */
    public function getArchiveYear()
    {
        if ($this->request->param('Year')) {
            if (preg_match('/^[0-9]{4}$/', $year = $this->request->param('Year'))) {
                return (int) $year;
            }
        } elseif ($this->request->param('Action') == 'archive') {
            return SS_Datetime::now()->Year();
        }

        return null;
    }

    /**
     * Fetches the archive money from the url.
     *
     * @return null|int
     */
    public function getArchiveMonth()
    {
        $month = $this->request->param('Month');

        if (preg_match('/^[0-9]{1,2}$/', $month)) {
            if ($month > 0 && $month < 13) {
                if (checkdate($month, 01, $this->getArchiveYear())) {
                    return (int) $month;
                }
            }
        }

        return null;
    }

    /**
     * Fetches the archive day from the url.
     *
     * @return null|int
     */
    public function getArchiveDay()
    {
        $day = $this->request->param('Day');

        if (preg_match('/^[0-9]{1,2}$/', $day)) {
            if (checkdate($this->getArchiveMonth(), $day, $this->getArchiveYear())) {
                return (int) $day;
            }
        }

        return null;
    }

    /**
     * Renders the blog posts for a given tag.
     *
     * @return null|SS_HTTPResponse
     */
    public function tag()
    {
        $tag = $this->getCurrentTag();

        if ($tag) {
            $this->blogPosts = $tag->BlogPosts();

            if($this->isRSS()) {
            	return $this->rssFeed($this->blogPosts, $tag->getLink());
            } else {
                return $this->render();
            }
        }

        $this->httpError(404, 'Not Found');

        return null;
    }

    /**
     * Tag Getter for use in templates.
     *
     * @return null|BlogTag
     */
    public function getCurrentTag()
    {
        /**
         * @var Blog $dataRecord
         */
        $dataRecord = $this->dataRecord;
        $tag = $this->request->param('Tag');
        if ($tag) {
            $filter = URLSegmentFilter::create();

            return $dataRecord->Tags()
                ->filter('URLSegment', array($tag, $filter->filter($tag)))
                ->first();
        }
        return null;
    }

    /**
     * Renders the blog posts for a given category.
     *
     * @return null|SS_HTTPResponse
     */
    public function category()
    {
        $category = $this->getCurrentCategory();

        if ($category) {
            $this->blogPosts = $category->BlogPosts();

            if($this->isRSS()) {
            	return $this->rssFeed($this->blogPosts, $category->getLink());
            } else {
                return $this->render();
            }
        }

        $this->httpError(404, 'Not Found');

        return null;
    }

    /**
     * Category Getter for use in templates.
     *
     * @return null|BlogCategory
     */
    public function getCurrentCategory()
    {
        /**
         * @var Blog $dataRecord
         */
        $dataRecord = $this->dataRecord;
        $category = $this->request->param('Category');
        if ($category) {
            $filter = URLSegmentFilter::create();

            return $dataRecord->Categories()
                ->filter('URLSegment', array($category, $filter->filter($category)))
                ->first();
        }
        return null;
    }

    /**
     * Get the meta title for the current action.
     *
     * @return string
     */
    public function getMetaTitle()
    {
        $title = $this->data()->getTitle();
        $filter = $this->getFilterDescription();

        if ($filter) {
            $title = sprintf('%s - %s', $title, $filter);
        }

        $this->extend('updateMetaTitle', $title);

        return $title;
    }

    /**
     * Returns a description of the current filter.
     *
     * @return string
     */
    public function getFilterDescription()
    {
        $items = array();

        $list = $this->PaginatedList();
        $currentPage = $list->CurrentPage();

        if ($currentPage > 1) {
            $items[] = _t(
                'Blog.FILTERDESCRIPTION_PAGE',
                'Page {page}',
                null,
                array(
                    'page' => $currentPage,
                )
            );
        }

        if ($author = $this->getCurrentProfile()) {
            $items[] = _t(
                'Blog.FILTERDESCRIPTION_AUTHOR',
                'By {author}',
                null,
                array(
                    'author' => $author->Title,
                )
            );
        }

        if ($tag = $this->getCurrentTag()) {
            $items[] = _t(
                'Blog.FILTERDESCRIPTION_TAG',
                'Tagged with {tag}',
                null,
                array(
                    'tag' => $tag->Title,
                )
            );
        }

        if ($category = $this->getCurrentCategory()) {
            $items[] = _t(
                'Blog.FILTERDESCRIPTION_CATEGORY',
                'In category {category}',
                null,
                array(
                    'category' => $category->Title,
                )
            );
        }

        if ($this->owner->getArchiveYear()) {
            if ($this->owner->getArchiveDay()) {
                $date = $this->owner->getArchiveDate()->Nice();
            } elseif ($this->owner->getArchiveMonth()) {
                $date = $this->owner->getArchiveDate()->format('F, Y');
            } else {
                $date = $this->owner->getArchiveDate()->format('Y');
            }

            $items[] = _t(
                'Blog.FILTERDESCRIPTION_DATE',
                'In {date}',
                null,
                array(
                    'date' => $date,
                )
            );
        }

        $result = '';

        if ($items) {
            $result = implode(', ', $items);
        }

        $this->extend('updateFilterDescription', $result);

        return $result;
    }

    /**
     * Returns a list of paginated blog posts based on the BlogPost dataList.
     *
     * @return PaginatedList
     */
    public function PaginatedList()
    {
        $allPosts = $this->blogPosts ?: new ArrayList();

        $posts = PaginatedList::create($allPosts);

        // Set appropriate page size
        if ($this->PostsPerPage > 0) {
            $pageSize = $this->PostsPerPage;
        } elseif ($count = $allPosts->count()) {
            $pageSize = $count;
        } else {
            $pageSize = 99999;
        }
        $posts->setPageLength($pageSize);

        // Set current page
        $start = (int)$this->request->getVar($posts->getPaginationGetVar());
        $posts->setPageStart($start);

        return $posts;
    }

    /**
     * Returns the absolute link to the next page for use in the page meta tags. This helps search engines
     * find the pagination and index all pages properly.
     *
     * @example "<% if $PaginationAbsoluteNextLink %><link rel="next" href="$PaginationAbsoluteNextLink"><% end_if %>"
     *
     * @return string
     */
    public function PaginationAbsoluteNextLink() {
        $posts = $this->PaginatedList();
        if ($posts->NotLastPage()) {
            return Director::absoluteURL($posts->NextLink());
        }
    }

    /**
     * Returns the absolute link to the previous page for use in the page meta tags. This helps search engines
     * find the pagination and index all pages properly.
     *
     * @example "<% if $PaginationAbsolutePrevLink %><link rel="prev" href="$PaginationAbsolutePrevLink"><% end_if %>"
     *
     * @return string
     */
    public function PaginationAbsolutePrevLink() {
        $posts = $this->PaginatedList();
        if ($posts->NotFirstPage()) {
            return Director::absoluteURL($posts->PrevLink());
        }
    }

    /**
     * Displays an RSS feed of blog posts.
     *
     * @return string
     */
    public function rss()
    {
        /**
         * @var Blog $dataRecord
         */
        $dataRecord = $this->dataRecord;

        $this->blogPosts = $dataRecord->getBlogPosts();

        return $this->rssFeed($this->blogPosts, $this->Link());
    }

    /**
     * Returns the current archive date.
     *
     * @return null|Date
     */
    public function getArchiveDate()
    {
        $year = $this->getArchiveYear();
        $month = $this->getArchiveMonth();
        $day = $this->getArchiveDay();

        if ($year) {
            if ($month) {
                $date = sprintf('%s-%s-01', $year, $month);

                if ($day) {
                    $date = sprintf('%s-%s-%s', $year, $month, $day);
                }
            } else {
                $date = sprintf('%s-01-01', $year);
            }

            return DBField::create_field('Date', $date);
        }

        return null;
    }

    /**
     * Returns a link to the RSS feed.
     *
     * @return string
     */
    public function getRSSLink()
    {
        return $this->Link('rss');
    }

    /**
     * Displays an RSS feed of the given blog posts.
     *
     * @param DataList $blogPosts
     * @param string $link
     *
     * @return string
     */
    protected function rssFeed($blogPosts, $link)
    {
        $rss = new RSSFeed($blogPosts, $link, $this->MetaTitle, $this->MetaDescription);

        $this->extend('updateRss', $rss);

        return $rss->outputToBrowser();
    }

    /**
     * Returns true if the $Rss sub-action for categories/tags has been set to "rss"
     */
    private function isRSS()
    {
        $rss = $this->request->param('Rss');
        if(is_string($rss) && strcasecmp($rss, "rss") == 0) {
            return true;
        } else {
            return false;
        }
    }

}
