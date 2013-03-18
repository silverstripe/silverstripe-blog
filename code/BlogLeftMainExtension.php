<?php
/**
 * Influences the page list behaviour of blog entries in the CMS.
 * Adds author and "post date" fields.
 */
class BlogLeftMainExtension extends Extension {
	function updateListView($listView) {
		$parentId = $listView->getController()->getRequest()->requestVar('ParentID');
		$parent = ($parentId) ? Page::get()->byId($parentId) : new Page();

		// Only apply logic for this page type
		if($parent && $parent instanceof BlogHolder) {
			$gridField = $listView->Fields()->dataFieldByName('Page');
			if($gridField) {
				// Sort by post date
				$list = $gridField->getList();
				$list = $list->leftJoin('BlogEntry', '"BlogEntry"."ID" = "SiteTree"."ID"');
				$gridField->setList($list->sort('Date', 'DESC'));

				// Change columns
				$cols = $gridField->getConfig()->getComponentByType('GridFieldDataColumns');
				if($cols) {
					$fields = $cols->getDisplayFields($gridField);
					$castings = $cols->getFieldCasting($gridField);
					
					// Add author to columns
					$fields['Author'] = _t("BlogEntry.AU", "Author");
					// Add post date and remove duplicate "created" date
					$fields['Date'] = _t("BlogEntry.DT", "Date");
					$castings['Date'] = 'SS_Datetime->Ago';
					if(isset($fields['Created'])) unset($fields['Created']);
					
					$cols->setDisplayFields($fields);
					$cols->setFieldCasting($castings);
				}
			}
		}
	}
}