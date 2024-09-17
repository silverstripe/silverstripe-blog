<?php

namespace SilverStripe\Blog\Widgets;

use SilverStripe\Widgets\Model\WidgetController;
use SilverStripe\Dev\Deprecation;

if (!class_exists(WidgetController::class)) {
    return;
}

/**
 * @deprecated 4.3.0 Will be removed without equivalent functionality to replace it
 */
class BlogRecentPostsWidgetController extends WidgetController
{
    public function __construct($widget = null)
    {
        Deprecation::withSuppressedNotice(function () {
            Deprecation::notice(
                '4.3.0',
                'Will be removed without equivalent functionality to replace it',
                Deprecation::SCOPE_CLASS
            );
        });
        parent::__construct($widget);
    }
}
