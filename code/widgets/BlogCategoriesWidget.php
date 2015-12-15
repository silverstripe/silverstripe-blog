<?php

if (!class_exists("Widget")) {
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
    private static $db = array(
        'Limit' => 'Int',
        'Order' => 'Varchar',
        'Direction' => 'Varchar',
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'Blog' => 'Blog',
    );

    /**
     * {@inheritdoc}
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields[] = DropdownField::create(
                'BlogID', _t('BlogCategoriesWidget.Blog', 'Blog'), Blog::get()->map()
            );

            $fields[] = NumericField::create(
                'Limit', _t('BlogCategoriesWidget.Limit.Label', 'Limit'), 0
            )
                ->setDescription(_t('BlogCategoriesWidget.Limit.Description', 'Limit the number of categories shown by this widget (set to 0 to show all categories).'))
                ->setMaxLength(3);

            $fields[] = DropdownField::create(
                'Order', _t('BlogCategoriesWidget.Sort.Label', 'Sort'), array('Title' => 'Title', 'Created' => 'Created', 'LastEdited' => 'Updated')
            )
                ->setDescription(_t('BlogCategoriesWidget.Sort.Description', 'Change the order of categories shown by this widget.'));

            $fields[] = DropdownField::create(
                'Direction', _t('BlogCategoriesWidget.Direction.Label', 'Direction'), array('ASC' => 'Ascending', 'DESC' => 'Descending')
            )
                ->setDescription(_t('BlogCategoriesWidget.Direction.Description', 'Change the direction of ordering of categories shown by this widget.'));
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
            return array();
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

class BlogCategoriesWidget_Controller extends Widget_Controller
{
}
