<?php

class BlogAdminSidebar extends FieldGroup {

	public function isOpen() {
		$sidebar = Cookie::get('blog-admin-sidebar');
		if($sidebar == 1 || is_null($sidebar)) {
			return true;
		}
		return false;
	}

}