<?php

class BlogRole extends DataObjectDecorator {

	function extraDBFields() {
		return array(
			'db' => array(
				'Hash' => 'Varchar(32)',
				'GroupCode' => 'Varchar(255)'
			),
		);
	}

}

?>
