<?php

namespace SilverStripe\Blog\Forms;

use SilverStripe\Forms\FieldGroup;
use SilverStripe\Control\Cookie;

class BlogAdminSidebar extends FieldGroup
{
    /**
     * @return bool
     */
    public function isOpen()
    {
        $sidebar = Cookie::get('blog-admin-sidebar');

        if ($sidebar == 1 || is_null($sidebar)) {
            return true;
        }

        return false;
    }
}
