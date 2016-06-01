<?php

/**
 * A blog tag for keyword descriptions of a blog post.
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @method Blog Blog()
 *
 * @property string $Title
 * @property string $URLSegment
 * @property int $BlogID
 */
class BlogTag extends DataObject implements CategorisationObject
{

    /**
     * Use an exception code so that attempted writes can continue on
     * duplicate errors.
     *
     * @const string
     * This must be a string because ValidationException has decided we can't use int
     */
    const DUPLICATE_EXCEPTION = "DUPLICATE";

    /**
     * @var array
     */
    private static $db = array(
        'Title' => 'Varchar(255)',
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'Blog' => 'Blog',
    );

    /**
     * @var array
     */
    private static $belongs_many_many = array(
        'BlogPosts' => 'BlogPost',
    );

    /**
     * @var array
     */
    private static $extensions = array(
        'URLSegmentExtension',
    );

    /**
     * @return DataList
     */
    public function BlogPosts()
    {
        $blogPosts = parent::BlogPosts();

        $this->extend("updateGetBlogPosts", $blogPosts);

        return $blogPosts;
    }

    /**
     * {@inheritdoc}
     */
    public function getCMSFields()
    {
        $fields = new FieldList(
            TextField::create('Title', _t('BlogTag.Title', 'Title'))
        );

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $validation = parent::validate();
        if($validation->valid()) {
            // Check for duplicate tags
            $blog = $this->Blog();
            if($blog && $blog->exists()) {
                $existing = $blog->Tags()->filter('Title', $this->Title);
                if($this->ID) {
                    $existing = $existing->exclude('ID', $this->ID);
                }
                if($existing->count() > 0) {
                    $validation->error(_t(
                        'BlogTag.Duplicate',
                        'A blog tags already exists with that name'
                    ), BlogTag::DUPLICATE_EXCEPTION);
                }
            }
        }
        return $validation;
    }

    /**
     * Returns a relative URL for the tag link.
     *
     * @return string
     */
    public function getLink()
    {
        return Controller::join_links($this->Blog()->Link(), 'tag', $this->URLSegment);
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
}
