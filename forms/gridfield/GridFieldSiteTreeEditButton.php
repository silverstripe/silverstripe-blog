<?php

/**
 * Swaps the GridField Link out for the SiteTree edit link using {@link SiteTree::CMSEditLink()}
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @author Michael Strong <github@michaelstrong.co.uk>
**/
class GridFieldSiteTreeEditButton extends GridFieldEditButton {
	
	/**
	 * @param GridField $gridField
	 * @param DataObject $record
	 * @param string $columnName
	 *
	 * @return string - the HTML for the column 
	 */
	public function getColumnContent($gridField, $record, $columnName) {
		// No permission checks - handled through GridFieldDetailForm
		// which can make the form readonly if no edit permissions are available.

		$data = new ArrayData(array(
			'Link' => $record->CMSEditLink()
		));

		return $data->renderWith('GridFieldEditButton');
	}

}