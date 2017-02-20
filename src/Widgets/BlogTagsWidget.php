<?php

namespace SilverStripe\Blog\Widgets;

if (!class_exists('\\SilverStripe\\Widgets\\Model\\Widget')) {
    return;
}

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Widgets\Model\Widget;

/**
 * @method Blog Blog()
 */
class BlogTagsWidget extends Widget
{
    /**
     * @var string
     */
    private static $title = 'Tags';

    /**
     * @var string
     */
    private static $cmsTitle = 'Blog Tags';

    /**
     * @var string
     */
    private static $description = 'Displays a list of blog tags.';

    /**
     * @var array
     */
    private static $db = array(
        'Limit' => 'Int',
        'Order' => 'Varchar',
        'Direction' => 'Varchar',
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'Blog' => Blog::class
    );

    /**
     * {@inheritdoc}
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (Fieldlist $fields) {
            $fields[] = DropdownField::create(
                'BlogID',
                _t('BlogTagsWidget.Blog', 'Blog'),
                Blog::get()->map()
            );

            $fields[] = NumericField::create(
                'Limit',
                _t('BlogTagsWidget.Limit', 'Limit'),
                0
            )
                ->setDescription(
                    _t(
                        'BlogTagsWidget.Limit_Description',
                        'Limit the number of tags shown by this widget (set to 0 to show all tags).'
                    )
                )
                ->setMaxLength(3);

            $fields[] = DropdownField::create(
                'Order',
                _t('BlogTagsWidget.Sort', 'Sort'),
                array('Title' => 'Title', 'Created' => 'Created', 'LastEdited' => 'Updated')
            )
                ->setDescription(
                    _t('BlogTagsWidget.Sort_Description', 'Change the order of tags shown by this widget.')
                );

            $fields[] = DropdownField::create(
                'Direction',
                _t('BlogTagsWidget.Direction', 'Direction'),
                array('ASC' => 'Ascending', 'DESC' => 'Descending')
            )
                ->setDescription(
                    _t(
                        'BlogTagsWidget.Direction_Description',
                        'Change the direction of ordering of tags shown by this widget.'
                    )
                );
        });

        return parent::getCMSFields();
    }

    /**
     * @return DataList
     */
    public function getTags()
    {
        $blog = $this->Blog();

        if (!$blog) {
            return array();
        }

        $query = $blog->Tags();

        if ($this->Limit) {
            $query = $query->limit(Convert::raw2sql($this->Limit));
        }

        if ($this->Order && $this->Direction) {
            $query = $query->sort(Convert::raw2sql($this->Order), Convert::raw2sql($this->Direction));
        }

        return $query;
    }
}
