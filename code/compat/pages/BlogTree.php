<?php

/**
 * @deprecated since version 2.0
 */
class BlogTree extends Page implements MigratableObject
{
    /**
     * @var string
     */
    private static $hide_ancestor = 'BlogTree';

    /**
     * @var array
     */
    private static $db = array(
        'Name' => 'Varchar(255)',
        'LandingPageFreshness' => 'Varchar',
    );

    /**
     * {@inheritdoc}
     */
    public function canCreate($member = null, $context = array())
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $published = $this->IsPublished();
        if ($this->ClassName === 'BlogTree') {
            $this->ClassName = 'Page';
            $this->RecordClassName = 'Page';
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
class BlogTree_Controller extends Page_Controller
{
}
