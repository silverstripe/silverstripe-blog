<?php

if (!class_exists("Widget")) {
    return;
}

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
        'Blog' => 'Blog',
    );

    /**
     * {@inheritdoc}
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (Fieldlist $fields) {
            $fields[] = DropdownField::create(
                'BlogID', _t('BlogTagsWidget.Blog', 'Blog'), Blog::get()->map()
            );

            $fields[] = NumericField::create(
                'Limit', _t('BlogTagsWidget.Limit.Label', 'Limit'), 0
            )
                ->setDescription(_t('BlogTagsWidget.Limit.Description', 'Limit the number of tags shown by this widget (set to 0 to show all tags).'))
                ->setMaxLength(3);

            $fields[] = DropdownField::create(
                'Order', _t('BlogTagsWidget.Sort.Label', 'Sort'), array('Title' => 'Title', 'Created' => 'Created', 'LastEdited' => 'Updated')
            )
                ->setDescription(_t('BlogTagsWidget.Sort.Description', 'Change the order of tags shown by this widget.'));

            $fields[] = DropdownField::create(
                'Direction', _t('BlogTagsWidget.Direction.Label', 'Direction'), array('ASC' => 'Ascending', 'DESC' => 'Descending')
            )
                ->setDescription(_t('BlogTagsWidget.Direction.Description', 'Change the direction of ordering of tags shown by this widget.'));
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

class BlogTagsWidget_Controller extends Widget_Controller
{
}
