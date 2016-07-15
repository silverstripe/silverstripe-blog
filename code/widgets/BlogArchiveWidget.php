<?php

if (!class_exists('Widget')) {
    return;
}

/**
 * @method Blog Blog()
 *
 * @property string $ArchiveType
 * @property int $NumberToDisplay
 */
class BlogArchiveWidget extends Widget
{
    /**
     * @var string
     */
    private static $title = 'Archive';

    /**
     * @var string
     */
    private static $cmsTitle = 'Archive';

    /**
     * @var string
     */
    private static $description = 'Displays an archive list of posts.';

    /**
     * @var array
     */
    private static $db = array(
        'NumberToDisplay' => 'Int',
        'ArchiveType' => 'Enum(\'Monthly,Yearly\', \'Monthly\')',
    );

    /**
     * @var array
     */
    private static $defaults = array(
        'NumberOfMonths' => 12,
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
        $self =& $this;

        $this->beforeUpdateCMSFields(function ($fields) use ($self) {
            /**
             * @var Enum $archiveType
             */
            $archiveType = $self->dbObject('ArchiveType');

            $type = $archiveType->enumValues();

            foreach ($type as $k => $v) {
                $type[$k] = _t('BlogArchiveWidget.' . ucfirst(strtolower($v)), $v);
            }

            /**
             * @var FieldList $fields
             */
            $fields->merge(array(
                DropdownField::create('BlogID', _t('BlogArchiveWidget.Blog', 'Blog'), Blog::get()->map()),
                DropdownField::create('ArchiveType', _t('BlogArchiveWidget.ArchiveType', 'ArchiveType'), $type),
                NumericField::create('NumberToDisplay', _t('BlogArchiveWidget.NumberToDisplay', 'No. to Display'))
            ));
        });

        return parent::getCMSFields();
    }

    /**
     * Returns a list of months where blog posts are present.
     *
     * @return DataList
     */
    public function getArchive()
    {
        $query = $this->Blog()->getBlogPosts()->dataQuery();

        if ($this->ArchiveType == 'Yearly') {
            $query->groupBy('DATE_FORMAT("PublishDate", \'%Y\')');
        } else {
            $query->groupBy('DATE_FORMAT("PublishDate", \'%Y-%M\')');
        }

        $posts = $this->Blog()->getBlogPosts()->setDataQuery($query);

        if ($this->NumberToDisplay > 0) {
            $posts = $posts->limit($this->NumberToDisplay);
        }

        $archive = new ArrayList();

        if ($posts->count() > 0) {
            foreach ($posts as $post) {
                /**
                 * @var BlogPost $post
                 */
                $date = Date::create();
                $date->setValue($post->PublishDate);

                if ($this->ArchiveType == 'Yearly') {
                    $year = $date->Format("Y");
                    $month = null;
                    $title = $year;
                } else {
                    $year = $date->Format("Y");
                    $month = $date->Format("m");
                    $title = $date->FormatI18N("%B %Y");
                }

                $archive->push(new ArrayData(array(
                    'Title' => $title,
                    'Link' => Controller::join_links($this->Blog()->Link('archive'), $year, $month)
                )));
            }
        }

        return $archive;
    }
}

class BlogArchiveWidget_Controller extends Widget_Controller
{
}
