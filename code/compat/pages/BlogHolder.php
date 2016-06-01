<?php

/**
 * @deprecated since version 2.0
 */
class BlogHolder extends BlogTree implements MigratableObject
{
    /**
     * @var string
     */
    private static $hide_ancestor = 'BlogHolder';

    /**
     * @var array
     */
    private static $db = array(
        'AllowCustomAuthors' => 'Boolean',
        'ShowFullEntry' => 'Boolean',
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'Owner' => 'Member',
    );

    /**
     * {@inheritdoc}
     */
    public function canCreate($member = null, $context = array())
    {
        return false;
    }


    //Overload these to stop the Uncaught Exception: Object->__call(): the method 'parent' does not exist on 'BlogHolder' error.
    public function validURLSegment()
    {
        return true;
    }
    public function syncLinkTracking()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $published = $this->IsPublished();

        if ($this->ClassName === 'BlogHolder') {
            $this->ClassName = 'Blog';
            $this->RecordClassName = 'Blog';
            $this->PostsPerPage = 10;
            $this->write();
        }

        if ($published) {
            $this->publish('Stage', 'Live');
            $message = "PUBLISHED: ";
        } else {
            $message = "DRAFT: ";
        }

        return $message . $this->Title;
    }
}

/**
 * @deprecated since version 2.0
 */
class BlogHolder_Controller extends BlogTree_Controller
{
}
