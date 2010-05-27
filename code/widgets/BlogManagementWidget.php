<?php
/**
 * Blog Management Widget
 * @package blog
 */
class BlogManagementWidget extends Widget implements PermissionProvider {
	static $db = array();

	static $has_one = array();

	static $has_many = array();

	static $many_many = array();

	static $belongs_many_many = array();

	static $defaults = array();

	static $title = "Blog Management";
	static $cmsTitle = "Blog Management";
	static $description = "Provide a number of links useful for administering a blog. Only shown if the user is an admin.";

	function CommentText() {
		$unmoderatedcount = DB::query("SELECT COUNT(*) FROM \"PageComment\" WHERE \"NeedsModeration\"=1")->value();
		if($unmoderatedcount == 1) {
			return _t("BlogManagementWidget.UNM1", "You have 1 unmoderated comment");
		} else if($unmoderatedcount > 1) {
			return sprintf(_t("BlogManagementWidget.UNMM", "You have %i unmoderated comments"), $unmoderatedcount);
		} else {
			return _t("BlogManagementWidget.COMADM", "Comment administration");
		}
	}

	function CommentLink() {
		if(!Permission::check('BLOGMANAGEMENT')) {
			return false;
		}
		$unmoderatedcount = DB::query("SELECT COUNT(*) FROM \"PageComment\" WHERE \"NeedsModeration\"=1")->value();

		if($unmoderatedcount > 0) {
			return "admin/comments/unmoderated";
		} else {
			return "admin/comments";
		}
	}

	function providePermissions() {
		return array("BLOGMANAGEMENT" => "Blog management");
	}

}

class BlogManagementWidget_Controller extends Widget_Controller { 
	
	function WidgetHolder() { 
		if(Permission::check("BLOGMANAGEMENT")) { 
			return $this->renderWith("WidgetHolder"); 
		} 
	}
	
	function PostLink() {
		$container = BlogTree::current();
		if ($container) return $container->Link('post');
	}
}
?>
