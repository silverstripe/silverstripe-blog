<?php

class NewsletterSignupForm extends Form {	

	/**
	 * Get the group code of the newsletter associated with the
	 * BlogHolder instance that this form was created from.
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
	
	/**
	 * Create the NewsletterSignupForm.
	 * Take the fields and required fields from the extension role.
	 */
	function __construct($controller, $name) {
		$member = singleton('Member');

		$fields = $member->subscribeFields();

		$validator = $member->subscribeRequiredFields();

		$actions = new FieldSet(
			new FormAction('subscribe', 'Subscribe')
		);

		parent::__construct($controller, $name, $fields, $actions, $validator);		
	}

	/**
	 * NewsletterSignupForm action.
	 * Requires that the email address be submitted in the form.
	 * 
	 * Checks if there is a member in the system by submitted email, and checks if
	 * that member has already signed up. If member has, then form gives message and
	 * redirects back. If not, then it carries on with the process.
	 */	
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