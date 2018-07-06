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
     * @return ArrayList
     */
    public function getArchive()
    {
        $format = ($this->ArchiveType == 'Yearly') ? '%Y' : '%Y-%m';
        $publishDate = DB::get_conn()->formattedDatetimeClause('"PublishDate"', $format);
        $fields = array(
            'PublishDate' => $publishDate,
            'Total' => "COUNT('\"PublishDate\"')"
        );

        $stage = Versioned::current_stage();
        $suffix = ($stage === 'Live') ? '_Live' : '';
        $query = SQLSelect::create($fields, '"BlogPost' . $suffix . '"')
            ->addGroupBy($publishDate)
            ->addOrderBy('"PublishDate" DESC')
            ->addLeftJoin('SiteTree' . $suffix, '"SiteTree' . $suffix . '"."ID" = "BlogPost' . $suffix . '"."ID"')
            ->addWhere(array('"PublishDate" <= ?' => SS_Datetime::now()->Format('Y-m-d H:i:s'), '"SiteTree' . $suffix . '"."ParentID"' => $this->BlogID));

        $posts = $query->execute();
        $result = new ArrayList();
        foreach ($posts as $post) {
            if ($this->ArchiveType == 'Yearly') {
                $year  = $post['PublishDate'];
                $month = null;
                $title = $year;
            } else {
                $date = Date::create();
                $date->setValue(strtotime($post['PublishDate']));

                $year  = $date->Format('Y');
                $month = $date->Format('m');
                $title = $date->FormatI18N('%B %Y');
            }

            $result->push(new ArrayData(array(
                'Title' => $title,
                'Link' => Controller::join_links($this->Blog()->Link('archive'), $year, $month)
            )));
        }

        $this->extend('updateGetArchive', $result);

        return $result;
    }
}

class BlogArchiveWidget_Controller extends Widget_Controller
{
}
