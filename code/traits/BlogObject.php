<?php

/**
 * An object shared by BlogTag and BlogCategory.
 *
 * @package silverstripe
 * @subpackage blog
 */
trait BlogObject {

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
        $fields = new TabSet('Root',
            new Tab('Main',
                TextField::create('Title', _t(self::class . '.Title', 'Title'))
            )
        );

        $fields = FieldList::create($fields);
        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $validation = parent::validate();
        if(!$validation->valid()) {
            return $validation;
        }

        $blog = $this->Blog();
        if(!$blog || !$blog->exists()) {
            return $validation;
        }

        if($this->getDuplicatesByUrlSegment()->count() > 0) {
            $validation->error($this->getDuplicateError(), self::DUPLICATE_EXCEPTION);
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
    public function canCreate($member = null, $context = array())
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

        return $this->Blog()->canEdit($member);
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
        if(empty($this->URLSegment)) {
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
        $filter = new URLSegmentFilter();

        $this->URLSegment = $filter->filter($this->owner->Title);

        if ($increment > 0) {
            $this->URLSegment .= '-' . $increment;
        }

        if ($this->getDuplicatesByUrlSegment()->count() > 0) {
            $this->owner->generateURLSegment($increment+1);
        }

        return $this->owner->URLSegment;
    }

    /**
     * Looks for objects of the same type by url segment.
     *
     * @return DataList
     */
    protected function getDuplicatesByUrlSegment()
    {
        $duplicates = DataList::create(self::class)->filter(array(
            'URLSegment' => $this->URLSegment,
            'BlogID' => (int) $this->BlogID,
        ));

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
