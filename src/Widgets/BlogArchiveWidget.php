<?php

namespace SilverStripe\Blog\Widgets;

if (!class_exists('\\SilverStripe\\Widgets\\Model\\Widget')) {
    return;
}

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBDate;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\Queries\SQLSelect;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\ArrayData;
use SilverStripe\Widgets\Model\Widget;

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
     * {@inheritdoc}
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            /**
             * @var Enum $archiveType
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
            'Total' => "Count('PublishDate')"
        ];

        $stage = Versioned::get_stage();
        $suffix = ($stage == 'Stage') ? '' : "_{$stage}";
        $query = SQLSelect::create($fields, "BlogPost{$suffix}")
            ->addGroupBy($publishDate)
            ->addOrderBy('PublishDate Desc')
            ->addWhere(['PublishDate < ?' => DBDatetime::now()->Format('Y-m-d')]);

        $posts = $query->execute();
        $result = ArrayList::create();
        while ($next = $posts->next()) {
            $date = DBDate::create();
            $date->setValue(strtotime($next['PublishDate']));
            $year = $date->Format('Y');

            if ($this->ArchiveType == 'Yearly') {
                $month = null;
                $title = $year;
            } else {
                $month = $date->Format('m');
                $title = $date->FormatI18N('%B %Y');
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
