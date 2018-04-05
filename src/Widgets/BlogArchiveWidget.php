<?php

namespace SilverStripe\Blog\Widgets;

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBDate;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\FieldType\DBEnum;
use SilverStripe\ORM\Queries\SQLSelect;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\ArrayData;
use SilverStripe\Widgets\Model\Widget;

if (!class_exists(Widget::class)) {
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
    private static $db = [
        'NumberToDisplay' => 'Int',
        'ArchiveType' => 'Enum(\'Monthly,Yearly\', \'Monthly\')',
    ];

    /**
     * @var array
     */
    private static $defaults = [
        'NumberOfMonths' => 12,
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
    private static $table_name = 'BlogArchiveWidget';

    /**
     * {@inheritdoc}
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            /**
             * @var DBEnum $archiveType
             */
            $archiveType = $this->dbObject('ArchiveType');

            $type = $archiveType->enumValues();

            foreach ($type as $k => $v) {
                $type[$k] = _t(__CLASS__ .'.' . ucfirst(strtolower($v)), $v);
            }

            /**
             * @var FieldList $fields
             */
            $fields->merge([
                DropdownField::create(
                    'BlogID',
                    _t(__CLASS__ . '.Blog', 'Blog'),
                    Blog::get()->map()
                ),
                DropdownField::create('ArchiveType', _t(__CLASS__ . '.ArchiveType', 'ArchiveType'), $type),
                NumericField::create('NumberToDisplay', _t(__CLASS__ . '.NumberToDisplay', 'No. to Display'))
            ]);
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
        $fields = [
            'PublishDate' => $publishDate,
            'Total' => "COUNT('\"PublishDate\"')"
        ];

        $stage = Versioned::get_stage();
        $suffix = ($stage === Versioned::LIVE) ? '_' . Versioned::LIVE : '';
        $query = SQLSelect::create($fields, '"BlogPost' . $suffix . '"')
            ->addGroupBy($publishDate)
            ->addOrderBy('"PublishDate" DESC')
            ->addWhere(['"PublishDate" <= ?' => DBDatetime::now()->Format(DBDatetime::ISO_DATETIME)]);

        $posts = $query->execute();
        $result = ArrayList::create();
        foreach ($posts as $post) {
            if ($this->ArchiveType == 'Yearly') {
                $year  = $post['PublishDate'];
                $month = null;
                $title = $year;
            } else {
                $date = DBDate::create();
                $date->setValue(strtotime($post['PublishDate']));

                $year  = $date->Format('y');
                $month = $date->Format('MM');
                $title = $date->Format('MMMM y');
            }

            $result->push(ArrayData::create([
                'Title' => $title,
                'Link' => Controller::join_links($this->Blog()->Link('archive'), $year, $month)
            ]));
        }

        $this->extend('updateGetArchive', $result);

        return $result;
    }
}
