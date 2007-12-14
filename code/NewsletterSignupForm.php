<?php

class NewsletterSignupForm extends Form {	

	/**
	 * Code of a group.
	 */
	protected static $groupCode;
	
	/**
	 * Set a group for sign up. Must be a code value of an instance of Group.
	 * Members who sign up will be added to this group.
	 */
	public static function set_group_code($code) {
		self::$groupCode = $code;
	}
	
	/**
	 * Get the group used for sign ups.
	 */
	public static function get_group_code() {
		return self::$groupCode;
	}

	function __construct($controller, $name) {

		$fields = new FieldSet(
			new TextField('FirstName', 'Your first name'),
			new TextField('Surname', 'Your surname'),
			new EmailField('Email', 'Your email')
		);
		
		$validator = new RequiredFields(array(
				'FirstName',
				'Email'
			)
		);
		
		$actions = new FieldSet(
			new FormAction('subscribe', 'Subscribe')
		);
		
		parent::__construct($controller, $name, $fields, $actions, $validator);		
	}
	
	function subscribe($data, $form) {
		$SQL_email = $data['Email'];

		// Check if there is a current member of given email in data. Check if in group.
		if($member = DataObject::get_one('Member', "`Member`.`Email` = '$SQL_email'")) {
			if($groupCode = self::get_group_code()) {
				if($group = DataObject::get_one('Group', "Code = '$groupCode'")) {
					if($member->inGroup($group->ID)) {
						$form->sessionMessage('You are already subscribed.', 'warning');
						Director::redirectBack();
					}
				}
			}
		} else {
			// Create a new member, as this is a new subscriber.
			$member = new Member();
		}
		
		// Save the data into the member.
		$form->saveInto($member);
			
		// Hash the email of the subscriber and microtime, write the member.
		$member->Hash = md5(microtime() . $member->Email);
		$member->write();

		// Create an array with data to populate in the email.
		$populateArray = array();
		$populateArray['Member'] = $member;
		// If there is a group, populate a confirm link.
		if(self::get_group_code()) {
			$populateArray['ConfirmLink'] = Director::absoluteBaseURL() . 'confirm-subscription/member/' . $member->Hash;
		}

		// Send off a confirmation email to the subscriber.
		$email = new NewsletterSignupForm_Email();
		$email->to = $member->Email;
		$email->from = Email::getAdminEmail();
		$email->subject = 'Thank you for subscribing';
		$email->populateTemplate($populateArray);
		$email->send();

		// Display message, and redirect back.
		$form->sessionMessage('You have been sent an email to confirm your subscription.', 'good');
		Director::redirectBack();		
	}
}

class NewsletterSignupForm_Email extends Email_Template {

	protected $ss_template = 'NewsletterSignupForm_Email';

}

?>