<?php

if (!class_exists('Widget')) {
    return;
}

/**
 * A list of tags associated with blog posts.
 *
 * @package blog
 */
class TagCloudWidget extends BlogTagsWidget implements MigratableObject
{
    /**
     * @var array
     */
    private static $db = array(
        'Title' => 'Varchar',
        'Limit' => 'Int',
        'Sortby' => 'Varchar',
    );

    /**
     * @var array
     */
    private static $only_available_in = array(
        'none',
    );

    /**
     * {@inheritdoc}
     */
    public function canCreate($member = null)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->ClassName = 'BlogTagsWidget';
        $this->write();
        return "Migrated " . $this->Title . " widget";
    }
}
