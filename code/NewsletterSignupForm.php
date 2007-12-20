<?php

class NewsletterSignupForm extends Form {	

	/**
	 * Gets a NewsletterType which is associated with the BlogHolder, the
	 * controller of this sign up form. It then uses the relationship getter
	 * to find the Group of a NewsletterType.
	 */
	function get_group_code() {
		if($controller = $this->controller) {
			if($controller instanceof BlogHolder) {
				if($controller->Newsletter()) {
					return $controller->Newsletter()->Group()->Code;
				}
			}
		}
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
		));
		
		$actions = new FieldSet(
			new FormAction('subscribe', 'Subscribe')
		);
		
		parent::__construct($controller, $name, $fields, $actions, $validator);		
	}
	
	function subscribe($data, $form) {
		$SQL_email = $data['Email'];

		// Check if there is a current member of given email in data. Check if in group.
		if($member = DataObject::get_one('Member', "`Member`.`Email` = '$SQL_email'")) {
			if($groupCode = $this->get_group_code()) {
				if($group = DataObject::get_one('Group', "Code = '$groupCode'")) {
					if($member->inGroup($group->ID)) {
						$form->sessionMessage('You are already subscribed.', 'warning');
						Director::redirectBack();
						return false;
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

		// If there is a group code found, add it to a field on the member for
		// later use (when a member confirms to be added).
		if($groupCode = $this->get_group_code()) {
			$member->GroupCode = $groupCode;
		}
		
		// Write the member to the database.
		$member->write();

		// Create an array with data to populate in the email.
		$populateArray = array();
		$populateArray['Member'] = $member;
		// If there is a group, populate a confirm link.
		if($this->get_group_code()) {
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