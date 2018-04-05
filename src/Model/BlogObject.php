<?php

namespace SilverStripe\Blog\Model;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\View\Parsers\URLSegmentFilter;

/**
 * An object shared by BlogTag and BlogCategory.
 *
 */
trait BlogObject
{
    /**
     * @return DataList
     */
    public function BlogPosts()
    {
        $blogPosts = parent::BlogPosts();

        $this->extend('updateGetBlogPosts', $blogPosts);

        return $blogPosts;
    }

    /**
     * {@inheritdoc}
     */
    public function getCMSFields()
    {
        $fields = TabSet::create(
            'Root',
            Tab::create(
                'Main',
                TextField::create('Title', _t(__CLASS__ . '.Title', 'Title'))
            )
        );

        $fields = FieldList::create($fields);
        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    /**
     * {@inheritdoc}
     * @return ValidationResult
     */
    public function validate()
    {
        /** @var ValidationResult $validation */
        $validation = parent::validate();
        if (!$validation->isValid()) {
            return $validation;
        }

        $blog = $this->Blog();
        if (!$blog || !$blog->exists()) {
            return $validation;
        }

        if ($this->getDuplicatesByField('Title')->count() > 0) {
            $validation->addError($this->getDuplicateError(), self::DUPLICATE_EXCEPTION);
        }

        return $validation;
    }

    /**
     * Returns a relative link to this category.
     *
     * @return string
     */
    public function getLink()
    {
        return Controller::join_links(
            $this->Blog()->Link(),
            $this->getListUrlSegment(),
            $this->URLSegment
        );
    }

    /**
     * Inherits from the parent blog or can be overwritten using a DataExtension.
     *
     * @param null|Member $member
     *
     * @return bool
     */
    public function canView($member = null)
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);

        if ($extended !== null) {
            return $extended;
        }

        return $this->Blog()->canView($member);
    }

    /**
     * {@inheritdoc}
     */
    public function canCreate($member = null, $context = [])
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);

        if ($extended !== null) {
            return $extended;
        }

        $permission = Blog::config()->grant_user_permission;

        return Permission::checkMember($member, $permission);
    }

    /**
     * Inherits from the parent blog or can be overwritten using a DataExtension.
     *
     * @param null|Member $member
     *
     * @return bool
     */
    public function canDelete($member = null)
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);

        if ($extended !== null) {
            return $extended;
        }

        return $this->Blog()->canDelete($member);
    }

    /**
     * Inherits from the parent blog or can be overwritten using a DataExtension.
     *
     * @param null|Member $member
     *
     * @return bool
     */
    public function canEdit($member = null)
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);

        if ($extended !== null) {
            return $extended;
        }

        return $this->Blog()->canEdit($member);
    }

    /**
     * {@inheritdoc}
     */
    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if ($this->exists() || empty($this->URLSegment)) {
            return $this->generateURLSegment();
        }
    }

    /**
     * Generates a unique URLSegment from the title.
     *
     * @param int $increment
     *
     * @return string
     */
    public function generateURLSegment($increment = 0)
    {
        $increment = (int) $increment;
        $filter = URLSegmentFilter::create();

        // Setting this to on. Because of the UI flow, it would be quite a lot of work
        // to support turning this off. (ie. the add by title flow would not work).
        // If this becomes a problem we can approach it then.
        // @see https://github.com/silverstripe/silverstripe-blog/issues/376
        $filter->setAllowMultibyte(true);

        $this->URLSegment = $filter->filter($this->Title);

        if ($increment > 0) {
            $this->URLSegment .= '-' . $increment;
        }

        if ($this->getDuplicatesByField('URLSegment')->count() > 0) {
            $this->generateURLSegment($increment + 1);
        }

        return $this->URLSegment;
    }

    /**
     * Looks for objects o the same type and the same value by the given Field
     *
     * @param  string $field E.g. URLSegment or Title
     * @return DataList
     */
    protected function getDuplicatesByField($field)
    {
        $duplicates = DataList::create(self::class)
            ->filter(
                [
                    $field   => $this->$field,
                    'BlogID' => (int) $this->BlogID
                ]
            );

        if ($this->ID) {
            $duplicates = $duplicates->exclude('ID', $this->ID);
        }

        return $duplicates;
    }

    /**
     * This returns the url segment for the listing page.
     * eg. 'categories' in /my-blog/categories/category-url
     *
     * This is not editable at the moment, but a method is being used incase we want
     * to make it editable in the future. We can use this method to provide logic
     * without replacing multiple areas of the code base. We're also not being opinionated
     * about how the segment should be obtained at the moment and allowing for the
     * implementation to decide.
     *
     * @return string
     */
    abstract protected function getListUrlSegment();

    /**
     * Returns an error message for this object when it tries to write a duplicate.
     *
     * @return string
     */
    abstract protected function getDuplicateError();
}
