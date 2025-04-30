<?php

namespace SilverStripe\Blog\Widgets;

use SilverStripe\Widgets\Model\WidgetController;
use SilverStripe\Dev\Deprecation;

if (!class_exists(WidgetController::class)) {
    return;
}

/**
 * @deprecated 4.3.0 Will be removed without equivalent functionality to replace it in a future major release
 */
class BlogTagsWidgetController extends WidgetController
{
    public function __construct($widget = null)
    {
        Deprecation::withSuppressedNotice(function () {
            Deprecation::notice(
                '4.3.0',
                'Will be removed without equivalent functionality to replace it in a future major release',
                Deprecation::SCOPE_CLASS
            );
        });
        parent::__construct($widget);
    }
}
