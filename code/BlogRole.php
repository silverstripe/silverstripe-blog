<?php

class BlogRole extends DataObjectDecorator {

	/**
	 * Extend the member table with some extra fields.
	 */
	function extraDBFields() {
		return array(
			'db' => array(
				'Hash' => 'Varchar(32)',
				'GroupCode' => 'Varchar(255)'
			),
		);
	}
	
	/**
	 * These fields are used for the newsletter subscription form.
	 */
	function subscribeFields() {
		return new FieldSet(
			new TextField('FirstName', 'Your first name'),
			new TextField('Surname', 'Your surname'),
			new EmailField('Email', 'Your email')
		);
	}
	
	/**
	 * These are the required fields for the newsletter subscription form.
	 */
	function subscribeRequiredFields() {
		return new RequiredFields(array(
			'FirstName',
			'Email'
		));
	}

}

?>
