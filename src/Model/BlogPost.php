<?php

namespace SilverStripe\Blog\Model;

use Page;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\ListboxField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\SS_List;
use SilverStripe\ORM\UnsavedRelationList;
use SilverStripe\Security\Group;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use SilverStripe\TagField\TagField;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Parsers\ShortcodeParser;
use SilverStripe\View\Requirements;

/**
 * An individual blog post.
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
class BlogPost extends Page
{
    /**
     * Same as above, but for list of users that can be
     * given credit in the author field for blog posts
     * @var string|bool false or group code
     */
    private static $restrict_authors_to_group = false;

    /**
     * {@inheritDoc}
     * @var string
     */
    private static $table_name = 'BlogPost';

    /**
     * @var array
     */
    private static $db = [
        'PublishDate' => 'Datetime',
        'AuthorNames' => 'Varchar(1024)',
        'Summary'     => 'HTMLText'
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'FeaturedImage' => Image::class
    ];

    /**
     * @var array
     */
    private static $owns = [
        'FeaturedImage',
    ];

    /**
     * @var array
     */
    private static $many_many = [
        'Categories' => BlogCategory::class,
        'Tags'       => BlogTag::class,
        'Authors'    => Member::class
    ];

    /**
     * @var array
     */
    private static $defaults = [
        'ShowInMenus'     => false,
        'InheritSideBar'  => true,
        'ProvideComments' => true
    ];

    /**
     * @var array
     */
    private static $extensions = [
        BlogPostFilter::class
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'Title'
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Title'
    ];

    /**
     * @var array
     */
    private static $casting = [
        'Excerpt' => 'HTMLText',
        'Date' => 'DBDatetime'
    ];

    /**
     * @var array
     */
    private static $allowed_children = [];

    /**
     * The default sorting lists BlogPosts with an empty PublishDate at the top.
     *
     * @var string
     */
    private static $default_sort = '"PublishDate" IS NULL DESC, "PublishDate" DESC';

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
     * This helps estimate how long an article will take to read, if your target audience
     * is elderly then you should lower this value. See {@link getMinutesToRead()}
     *
     * @var int
     */
    private static $minutes_to_read_wpm = 200;

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
    public function RoleOf($member = null)
    {
        $member = $this->getMember($member);

        if (!$member) {
            return null;
        }

        if ($this->isAuthor($member)) {
            return _t(__CLASS__ . '.AUTHOR', 'Author');
        }

        $parent = $this->Parent();

        if ($parent instanceof Blog) {
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
    public function isAuthor($member = null)
    {
        if (!$member || !$member->exists()) {
            return false;
        }

        $list = $this->Authors();

        if ($list instanceof UnsavedRelationList) {
            return in_array($member->ID, $list->getIDList());
        }

        return $list->byID($member->ID) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCMSFields()
    {
        Requirements::css('silverstripe/blog:client/dist/styles/main.css');
        Requirements::javascript('silverstripe/blog:client/dist/js/main.bundle.js');

        $this->beforeUpdateCMSFields(function ($fields) {
            $uploadField = UploadField::create('FeaturedImage', _t(__CLASS__ . '.FeaturedImage', 'Featured Image'));
            $uploadField->getValidator()->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif']);

            /**
             * @var FieldList $fields
             */
            $fields->insertAfter('Content', $uploadField);

            $summary = HtmlEditorField::create('Summary', false);
            $summary->setRows(5);
            $summary->setDescription(_t(
                __CLASS__ . '.SUMMARY_DESCRIPTION',
                'If no summary is specified the first 30 words will be used.'
            ));

            $summaryHolder = ToggleCompositeField::create(
                'CustomSummary',
                _t(__CLASS__ . '.CUSTOMSUMMARY', 'Add A Custom Summary'),
                [
                    $summary,
                ]
            );
            $summaryHolder->setHeadingLevel(4);
            $summaryHolder->addExtraClass('custom-summary');

            $fields->insertAfter('FeaturedImage', $summaryHolder);

            $authorField = ListboxField::create(
                'Authors',
                _t(__CLASS__ . '.Authors', 'Authors'),
                $this->getCandidateAuthors()->map()->toArray()
            );

            $authorNames = TextField::create(
                'AuthorNames',
                _t(__CLASS__ . '.AdditionalCredits', 'Additional Credits'),
                null,
                1024
            )->setDescription(
                _t(
                    __CLASS__ . '.AdditionalCredits_Description',
                    'If some authors of this post don\'t have CMS access, enter their name(s) here. '.
                    'You can separate multiple names with a comma.'
                )
            );

            if (!$this->canEditAuthors()) {
                $authorField = $authorField->performDisabledTransformation();
                $authorNames = $authorNames->performDisabledTransformation();
            }

            $publishDate = DatetimeField::create('PublishDate', _t(__CLASS__ . '.PublishDate', 'Publish Date'));

            if (!$this->PublishDate) {
                $publishDate->setDescription(
                    _t(
                        __CLASS__ . '.PublishDate_Description',
                        'Will be set to "now" if published without a value.'
                    )
                );
            }

            // Get categories and tags
            $parent = $this->Parent();
            $categories = $parent instanceof Blog
                ? $parent->Categories()
                : BlogCategory::get();
            $tags = $parent instanceof Blog
                ? $parent->Tags()
                : BlogTag::get();

            // @todo: Reimplement the sidebar
            // $options = BlogAdminSidebar::create(
            $fields->addFieldsToTab(
                'Root.PostOptions',
                [
                    $publishDate,
                    TagField::create(
                        'Categories',
                        _t(__CLASS__ . '.Categories', 'Categories'),
                        $categories,
                        $this->Categories()
                    )
                        ->setCanCreate($this->canCreateCategories())
                        ->setShouldLazyLoad(true),
                    TagField::create(
                        'Tags',
                        _t(__CLASS__ . '.Tags', 'Tags'),
                        $tags,
                        $this->Tags()
                    )
                        ->setCanCreate($this->canCreateTags())
                        ->setShouldLazyLoad(true),
                    $authorField,
                    $authorNames
                ]
            );
            // )->setTitle('Post Options');
            // $options->setName('blog-admin-sidebar');
            // $fields->insertBefore($options, 'Root');

            $fields->fieldByName('Root.PostOptions')
                ->setTitle(_t(__CLASS__ . '.PostOptions', 'Post Options'));
        });

        $fields = parent::getCMSFields();

        $fields->fieldByName('Root')->setTemplate('TabSet_holder');

        return $fields;
    }

    /**
     * Gets the list of author candidates to be assigned as authors of this blog post.
     *
     * @return SS_List
     */
    public function getCandidateAuthors()
    {
        if ($this->config()->get('restrict_authors_to_group')) {
            return Group::get()->filter('Code', $this->config()->get('restrict_authors_to_group'))->first()->Members();
        }

        $list = Member::get();
        $this->extend('updateCandidateAuthors', $list);
        return $list;
    }

    /**
     * Determine if this user can edit the authors list.
     *
     * @param null|int|Member $member
     *
     * @return bool
     */
    public function canEditAuthors($member = null)
    {
        $member = $this->getMember($member);

        $extended = $this->extendedCan('canEditAuthors', $member);

        if ($extended !== null) {
            return $extended;
        }

        $parent = $this->Parent();

        if ($parent instanceof Blog && $parent->exists()) {
            if ($parent->isEditor($member)) {
                return true;
            }

            if ($parent->isWriter($member) && $this->isAuthor($member)) {
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
    protected function getMember($member = null)
    {
        if (!$member) {
            $member = Security::getCurrentUser();
        }

        if (is_numeric($member)) {
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
    public function canCreateCategories($member = null)
    {
        $member = $this->getMember($member);

        $parent = $this->Parent();

        if (!$parent || !$parent->exists() || !($parent instanceof Blog)) {
            return false;
        }

        if ($parent->isEditor($member)) {
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
    public function canCreateTags($member = null)
    {
        $member = $this->getMember($member);

        $parent = $this->Parent();

        if (!$parent || !$parent->exists() || !($parent instanceof Blog)) {
            return false;
        }

        if ($parent->isEditor($member)) {
            return true;
        }

        if ($parent->isWriter($member)) {
            return true;
        }

        return Permission::checkMember($member, 'ADMIN');
    }

    /**
     * {@inheritdoc}
     *
     * Update the PublishDate to now if the BlogPost would otherwise be published without a date.
     */
    public function onBeforePublish()
    {
        /**
         * @var DBDatetime $publishDate
         */
        $publishDate = $this->dbObject('PublishDate');

        if (!$publishDate->getValue()) {
            $this->PublishDate = DBDatetime::now()->getValue();
            $this->write();
        }
    }

    /**
     * {@inheritdoc}
     *
     * Sets blog relationship on all categories and tags assigned to this post.
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();

        foreach ($this->Categories() as $category) {
            /**
             * @var BlogCategory $category
             */
            $category->BlogID = $this->ParentID;
            $category->write();
        }

        foreach ($this->Tags() as $tag) {
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
    public function canView($member = null)
    {
        $member = $this->getMember($member);

        if (!parent::canView($member)) {
            return false;
        }

        if ($this->canEdit($member)) {
            return true;
        }

        // If on draft stage, user has permission to view draft, so show it
        if (Versioned::get_stage() === Versioned::DRAFT) {
            return true;
        }

        /**
         * @var DBDatetime $publishDate
         */
        $publishDate = $this->dbObject('PublishDate');
        if (!$publishDate->exists()) {
            return false;
        }

        return !$publishDate->InFuture();
    }

    /**
     * {@inheritdoc}
     */
    public function canPublish($member = null)
    {
        $member = $this->getMember($member);

        if (Permission::checkMember($member, 'ADMIN')) {
            return true;
        }

        $extended = $this->extendedCan('canPublish', $member);

        if ($extended !== null) {
            return $extended;
        }

        $parent = $this->Parent();

        if ($parent instanceof Blog && $parent->exists()) {
            if ($parent->isEditor($member)) {
                return true;
            }

            if ($parent->isWriter($member) && $this->isAuthor($member)) {
                return true;
            }

            if ($parent->isContributor($member)) {
                return parent::canEdit($member);
            }
        }

        return $this->canEdit($member);
    }

    /**
     * {@inheritdoc}
     */
    public function canEdit($member = null)
    {
        $member = $this->getMember($member);

        if (parent::canEdit($member)) {
            return true;
        }

        $parent = $this->Parent();

        if (!$parent || !$parent->exists() || !($parent instanceof Blog)) {
            return false;
        }

        if ($parent->isEditor($member)) {
            return true;
        }

        if (!$parent->isWriter($member) && !$parent->isContributor($member)) {
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
    public function Excerpt($wordsToDisplay = 30)
    {
        /** @var DBHTMLText $content */
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
    public function getMonthlyArchiveLink($type = 'day')
    {
        /**
         * @var DBDatetime $date
         */
        $date = $this->dbObject('PublishDate');

        if ($type != 'year') {
            if ($type == 'day') {
                return Controller::join_links(
                    $this->Parent()->Link('archive'),
                    $date->format('Y'),
                    $date->format('M'),
                    $date->format('d')
                );
            }

            return Controller::join_links($this->Parent()->Link('archive'), $date->format('Y'), $date->format('M'));
        }

        return Controller::join_links($this->Parent()->Link('archive'), $date->format('Y'));
    }

    /**
     * Returns a yearly archive link for the current blog post.
     *
     * @return string
     */
    public function getYearlyArchiveLink()
    {
        /**
         * @var DBDatetime $date
         */
        $date = $this->dbObject('PublishDate');

        return Controller::join_links($this->Parent()->Link('archive'), $date->format('Y'));
    }

    /**
     * Resolves static and dynamic authors linked to this post.
     *
     * @return ArrayList
     */
    public function getCredits()
    {
        $list = ArrayList::create();

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
        // Find best page to host user profiles
        $parent = $this->Parent();
        if (! ($parent instanceof Blog)) {
            $parent = Blog::get()->first();
        }

        // If there is no parent blog, return list undecorated
        if (!$parent) {
            $items = $this->Authors()->toArray();
            return ArrayList::create($items);
        }

        // Update all authors
        $items = ArrayList::create();
        foreach ($this->Authors() as $author) {
            // Add link for each author
            $author = $author->customise([
                'URL' => $parent->ProfileLink($author->URLSegment),
            ]);
            $items->push($author);
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
        $items = ArrayList::create();

        $authors = array_filter(preg_split('/\s*,\s*/', $this->AuthorNames));

        foreach ($authors as $author) {
            $item = ArrayData::create([
                'Name' => $author,
            ]);

            $items->push($item);
        }

        return $items;
    }

    /**
     * Checks to see if User Profiles has been disabled via config
     *
     * @return bool
     */
    public function getProfilesDisabled()
    {
        return Config::inst()->get(BlogController::class, 'disable_profiles');
    }

    /**
     * Sets the label for BlogPost.Title to 'Post Title' (Rather than 'Page name').
     *
     * @param bool $includeRelations
     *
     * @return array
     */
    public function fieldLabels($includeRelations = true)
    {
        $labels = parent::fieldLabels($includeRelations);

        $labels['Title'] = _t(__CLASS__ . '.PageTitleLabel', 'Post Title');

        return $labels;
    }

    /**
     * Proxy method for displaying the publish date in rss feeds.
     * @see https://github.com/silverstripe/silverstripe-blog/issues/394
     *
     * @return string|null
     */
    public function getDate()
    {
        if ($this->hasDatabaseField('Date')) {
            return $this->getField('Date');
        }
        return !empty($this->PublishDate) ? $this->PublishDate : null;
    }

    /**
     * Provides a rough estimate of how long this post will take to read based on wikipedias answer to "How fast can a
     * human read" of 200wpm. Source https://en.wikipedia.org/wiki/Speed_reading
     *
     * @param null|integer $wpm
     *
     * @return string
     */
    public function MinutesToRead($wpm = null)
    {
        $wpm = $wpm ?: $this->config()->get('minutes_to_read_wpm');

        if (!is_numeric($wpm)) {
            throw new \InvalidArgumentException(sprintf("Expecting integer but got %s instead", gettype($wpm)));
        }

        $wordCount = str_word_count(strip_tags($this->Content));

        if ($wordCount < $wpm) {
            return 0;
        }

        return round($wordCount / $wpm, 0);
    }

    /**
     * {@inheritdoc}
     */
    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (!$this->exists() && ($member = Security::getCurrentUser())) {
            $this->Authors()->add($member);
        }
    }
}
