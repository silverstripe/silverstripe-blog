<?php

namespace SilverStripe\Blog\Model;

use SilverStripe\Assets\Image;
use SilverStripe\Blog\Forms\GridField\GridFieldConfigBlogPost;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\Tab;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Member;
use SilverStripe\View\Parsers\URLSegmentFilter;
use SilverStripe\View\Requirements;

/**
 * This class is responsible for add Blog specific behaviour to Members.
 *
 */
class BlogMemberExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $db = [
        'URLSegment'         => 'Varchar(255)',
        'BlogProfileSummary' => 'Text'
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'BlogProfileImage' => Image::class
    ];

    /**
     * @var array
     */
    private static $belongs_many_many = [
        'BlogPosts' => BlogPost::class
    ];

    /**
     * {@inheritdoc}
     */
    public function onBeforeWrite()
    {
        $count = 1;

        if ($this->owner->URLSegment && !$this->owner->isChanged('FirstName') && !$this->owner->isChanged('Surname')) {
            return;
        }

        $this->owner->URLSegment = $this->generateURLSegment();

        while (!$this->validURLSegment()) {
            $this->owner->URLSegment = preg_replace('/-[0-9]+$/', null, $this->owner->URLSegment) . '-' . $count;
            $count++;
        }

        // Auto publish profile images
        if ($this->owner->BlogProfileImage() && $this->owner->BlogProfileImage()->exists()) {
            $this->owner->BlogProfileImage()->publishSingle();
        }
    }

    /**
     * Generate a unique URL segment based on the Member's name.
     *
     * @return string
     */
    public function generateURLSegment()
    {
        $filter = URLSegmentFilter::create();
        $name = $this->owner->FirstName . ' ' . $this->owner->Surname;
        $urlSegment = $filter->filter($name);

        if (!$urlSegment || $urlSegment == '-' || $urlSegment == '-1') {
            $urlSegment = 'profile-' . $this->owner->ID;
        }

        return $urlSegment;
    }

    /**
     * Returns TRUE if this object has a URL segment value that does not conflict with any other
     * objects.
     *
     * @return bool
     */
    public function validURLSegment()
    {
        $conflict = Member::get()->filter('URLSegment', $this->owner->URLSegment);

        if ($this->owner->ID) {
            $conflict = $conflict->exclude('ID', $this->owner->ID);
        }

        return $conflict->count() == 0;
    }

    /**
     * {@inheritdoc}
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName('URLSegment');

        // Remove the automatically-generated posts tab.

        $fields->removeFieldFromTab('Root', 'BlogPosts');

        // Construct a better posts tab.
        Requirements::css('silverstripe/blog:client/dist/styles/main.css');
        Requirements::javascript('silverstripe/blog:client/dist/js/main.bundle.js');

        $tab = Tab::create('BlogPosts', _t(__CLASS__ . '.TABBLOGPOSTS', 'Blog Posts'));

        $gridField = GridField::create(
            'BlogPosts',
            _t(__CLASS__ . '.BLOGPOSTS', 'Blog Posts'),
            $this->owner->BlogPosts(),
            $gridFieldConfig = GridFieldConfigBlogPost::create()
        );

        // Remove the "add new blog post" action from a member's profile
        $gridFieldConfig->removeComponentsByType(GridFieldAddNewButton::class);

        $tab->Fields()->add($gridField);

        $fields->addFieldToTab('Root', $tab);

        return $fields;
    }
}
