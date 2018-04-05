<?php

namespace SilverStripe\Blog\Widgets;

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\DataList;
use SilverStripe\Widgets\Model\Widget;

if (!class_exists(Widget::class)) {
    return;
}

/**
 * @method Blog Blog()
 */
class BlogCategoriesWidget extends Widget
{
    /**
     * @var string
     */
    private static $title = 'Categories';

    /**
     * @var string
     */
    private static $cmsTitle = 'Blog Categories';

    /**
     * @var string
     */
    private static $description = 'Displays a list of blog categories.';

    /**
     * @var array
     */
    private static $db = [
        'Limit' => 'Int',
        'Order' => 'Varchar',
        'Direction' => 'Varchar',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'Blog' => Blog::class,
    ];

    /**
     * @var string
     */
    private static $table_name = 'BlogCategoriesWidget';

    /**
     * {@inheritdoc}
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields[] = DropdownField::create(
                'BlogID',
                _t(__CLASS__ . '.Blog', 'Blog'),
                Blog::get()->map()
            );

            $fields[] = NumericField::create(
                'Limit',
                _t(__CLASS__ . '.Limit', 'Limit'),
                0
            )
                ->setDescription(
                    _t(
                        __CLASS__ . '.Limit_Description',
                        'Limit the number of categories shown by this widget (set to 0 to show all categories).'
                    )
                )
                ->setMaxLength(3);

            $fields[] = DropdownField::create(
                'Order',
                _t(__CLASS__ . '.Sort', 'Sort'),
                ['Title' => 'Title', 'Created' => 'Created', 'LastEdited' => 'Updated']
            )
                ->setDescription(
                    _t(__CLASS__ . '.Sort_Description', 'Change the order of categories shown by this widget.')
                );

            $fields[] = DropdownField::create(
                'Direction',
                _t(__CLASS__ . '.Direction', 'Direction'),
                ['ASC' => 'Ascending', 'DESC' => 'Descending']
            )
                ->setDescription(
                    _t(
                        __CLASS__ . '.Direction_Description',
                        'Change the direction of ordering of categories shown by this widget.'
                    )
                );
        });

        return parent::getCMSFields();
    }

    /**
     * @return DataList
     */
    public function getCategories()
    {
        $blog = $this->Blog();

        if (!$blog) {
            return [];
        }

        $query = $blog->Categories();

        if ($this->Limit) {
            $query = $query->limit(Convert::raw2sql($this->Limit));
        }

        if ($this->Order && $this->Direction) {
            $query = $query->sort(Convert::raw2sql($this->Order), Convert::raw2sql($this->Direction));
        }

        return $query;
    }
}
