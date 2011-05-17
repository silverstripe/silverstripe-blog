<?php
/**
 * Shows a widget with viewing blog entries
 * by months or years.
 * 
 * @package blog
 */
class ArchiveWidget extends Widget {
	static $db = array(
		'DisplayMode' => 'Varchar'
	);
	
	static $has_one = array();
	
	static $has_many = array();
	
	static $many_many = array();
	
	static $belongs_many_many = array();
	
	static $defaults = array(
		'DisplayMode' => 'month'
	);
	
	static $title;

	static $cmsTitle;
	
	static $description;
		
	function getCMSFields() {
		$fields = parent::getCMSFields(); 
		
		$fields->merge( 
			new FieldSet(
				new OptionsetField(
					'DisplayMode',
					_t('ArchiveWidget.DispBY', 'Display by'),
					array(
						'month' => _t('ArchiveWidget.MONTH', 'month'),
						'year' => _t('ArchiveWidget.YEAR', 'year')
					)
				)
			)	
		);
		
		$this->extend('updateCMSFields', $fields);
		
		return $fields;
	}
	
	function Dates() {
		Requirements::themedCSS('archivewidget');
		
		$results = new DataObjectSet();
		$container = BlogTree::current();
		$ids = $container->BlogHolderIDs();
		
		$stage = Versioned::current_stage();
		$suffix = (!$stage || $stage == 'Stage') ? "" : "_$stage";

		$monthclause = method_exists(DB::getConn(), 'formattedDatetimeClause') ? DB::getConn()->formattedDatetimeClause('"Date"', '%m') : 'MONTH("Date")';
		$yearclause  = method_exists(DB::getConn(), 'formattedDatetimeClause') ? DB::getConn()->formattedDatetimeClause('"Date"', '%Y') : 'YEAR("Date")';
		
		if($this->DisplayMode == 'month') {
			$sqlResults = DB::query("
				SELECT DISTINCT CAST($monthclause AS " . DB::getConn()->dbDataType('unsigned integer') . ") AS \"Month\", $yearclause AS \"Year\"
				FROM \"SiteTree$suffix\" INNER JOIN \"BlogEntry$suffix\" ON \"SiteTree$suffix\".\"ID\" = \"BlogEntry$suffix\".\"ID\"
				WHERE \"ParentID\" IN (" . implode(', ', $ids) . ")
				ORDER BY \"Year\" DESC, \"Month\" DESC;"
			);
		} else {
			$sqlResults = DB::query("
				SELECT DISTINCT $yearclause AS \"Year\" 
				FROM \"SiteTree$suffix\" INNER JOIN \"BlogEntry$suffix\" ON \"SiteTree$suffix\".\"ID\" = \"BlogEntry$suffix\".\"ID\"
				WHERE \"ParentID\" IN (" . implode(', ', $ids) . ")
				ORDER BY \"Year\" DESC"
			);
		}
		
		if($sqlResults) foreach($sqlResults as $sqlResult) {
			$isMonthDisplay = $this->DisplayMode == 'month';
			
			$monthVal = (isset($sqlResult['Month'])) ? (int) $sqlResult['Month'] : 1;
			$month = ($isMonthDisplay) ? $monthVal : 1;
			$year = ($sqlResult['Year']) ? (int) $sqlResult['Year'] : date('Y');
			
			$date = DBField::create('Date', array(
				'Day' => 1,
				'Month' => $month,
				'Year' => $year
			));
			
			if($isMonthDisplay) {
				$link = $container->Link('date') . '/' . $sqlResult['Year'] . '/' . sprintf("%'02d", $monthVal);
			} else {
				$link = $container->Link('date') . '/' . $sqlResult['Year'];
			}
			
			$results->push(new ArrayData(array(
				'Date' => $date,
				'Link' => $link
			)));
		}
		
		return $results;
	}	
}

ArchiveWidget::$title = i18n::_t('ArchiveWidget.BrowseByDate', 'Browse by Date');
ArchiveWidget::$cmsTitle = i18n::_t('ArchiveWidget.BlogArchive', 'Blog Archive');
ArchiveWidget::$description = i18n::_t('ArchiveWidget.Description', 'Show a list of months or years in which there are blog posts, and provide links to them.');
?>
