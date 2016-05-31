<?php

/**
 * Adds URLSegment functionality to Tags & Categories.
 *
 * @package silverstripe
 * @subpackage blog
 */
class URLSegmentExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $db = array(
        'URLSegment' => 'Varchar(255)',
    );

    /**
     * {@inheritdoc}
     */
    public function onBeforeWrite()
    {
        if ($this->owner->BlogID) {
            $this->owner->generateURLSegment();
        }
    }

    /**
     * Generates a unique URLSegment from the title.
     *
     * @param int $increment
     *
     * @return string
     */
    public function generateURLSegment($increment = null)
    {
        $filter = new URLSegmentFilter();

        // Setting this to on. Because of the UI flow, it would be quite a lot of work
        // to support turning this off. (ie. the add by title flow would not work).
        // If this becomes a problem we can approach it then.
        // @see https://github.com/silverstripe/silverstripe-blog/issues/376
        $filter->setAllowMultibyte(true);

        $this->owner->URLSegment = $filter->filter($this->owner->Title);

        if (is_int($increment)) {
            $this->owner->URLSegment .= '-' . $increment;
        }

        // Postgres use '' instead of 0 as an emtpy blog ID
        // Without this all the tests fail
        if (!$this->owner->BlogID) {
            $this->owner->BlogID = 0;
        }

        $duplicate = DataList::create($this->owner->ClassName)->filter(array(
            'URLSegment' => $this->owner->URLSegment,
            'BlogID' => $this->owner->BlogID,
        ));

        if ($this->owner->ID) {
            $duplicate = $duplicate->exclude('ID', $this->owner->ID);
        }

        if ($duplicate->count() > 0) {
            if (is_int($increment)) {
                $increment += 1;
            } else {
                $increment = 0;
            }

            $this->owner->generateURLSegment((int) $increment);
        }

        return $this->owner->URLSegment;
    }
}
