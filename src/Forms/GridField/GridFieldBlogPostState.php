<?php

namespace SilverStripe\Blog\Forms\GridField;

use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\Lumberjack\Forms\GridFieldSiteTreeState;
use SilverStripe\View\Requirements;

/**
 * Provides a component to the {@link GridField} which tells the user whether or not a blog post
 * has been published and when.
 *
 */
class GridFieldBlogPostState extends GridFieldSiteTreeState
{
    /**
     * {@inheritdoc}
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        if ($columnName == 'State') {
            Requirements::css(ModuleLoader::getModule('silverstripe/blog')->getRelativeResourcePath('css/cms.css'));
            if ($record instanceof BlogPost) {
                $modifiedLabel = '';

                if ($record->isModifiedOnStage) {
                    $modifiedLabel = '<span class="modified">' . _t(__CLASS__ . '.Modified') . '</span>';
                }

                if (!$record->isPublished()) {
                    /**
                     * @var SS_Datetime $lastEdited
                     */
                    $lastEdited = $record->dbObject('LastEdited');

                    return _t(
                        __CLASS__ . '.Draft',
                        '<i class="btn-icon gridfield-icon btn-icon-pencil"></i> Saved as Draft on {date}',
                        'State for when a post is saved.',
                        array(
                            'date' => $lastEdited->FormatFromSettings(),
                        )
                    );
                }

                /**
                 * @var SS_Datetime $publishDate
                 */
                $publishDate = $record->dbObject('PublishDate');

                if (strtotime($record->PublishDate) > time()) {
                    return _t(
                            __CLASS__ . '.Timer',
                        '<i class="gridfield-icon blog-icon-timer"></i> Publish at {date}',
                        'State for when a post is published.',
                        array(
                            'date' => $publishDate->FormatFromSettings(),
                        )
                    ) . $modifiedLabel;
                }

                return _t(
                        __CLASS__ . '.Published',
                    '<i class="btn-icon gridfield-icon btn-icon-accept"></i> Published on {date}',
                    'State for when a post is published.',
                    array(
                        'date' => $publishDate->FormatFromSettings(),
                    )
                ) . $modifiedLabel;
            }
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        if ($columnName == 'State') {
            if ($record instanceof BlogPost) {
                $published = $record->isPublished();

                if (!$published) {
                    $class = 'gridfield-icon draft';
                } elseif (strtotime($record->PublishDate) > time()) {
                    $class = 'gridfield-icon timer';
                } else {
                    $class = 'gridfield-icon published';
                }

                return array(
                    'class' => $class,
                );
            }
        }

        return array();
    }
}
