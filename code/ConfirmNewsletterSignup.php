<?php

class ConfirmNewsletterSignup extends Controller {

	/**
	 * Add theme CSS requirements to make this look controller look a bit prettier.
	 * If these aren't here, the page looks like a mess to users.
	 */
	function init() {
		parent::init();
		Requirements::themedCSS('layout');
		Requirements::themedCSS('typography');
		Requirements::themedCSS('form');
	}
	
	/**
	 * Action for signing up a member to a given group in NewsletterSignupForm.
	 * Used as mysite.com/confirm-subscription/member/123 (where 123 is a md5 hash to find the member)
	 */	
	function member() {
		// Create an empty string for messages to be passed back to the template.
		$content = '';

		// Check if the ID params exist, and ensure safe for SQL.
		if(!$hash = Convert::raw2sql(Director::urlParam('ID'))) {
			$content = "<p><strong>Error:</strong> No member identification was given.</p>";
		} else {
			// Check if a member exists with the hash given from ID param.
			if(!$member = DataObject::get_one('Member', "Hash = '$hash'")) {
				$content = "<p><strong>Error:</strong> Member does not exist by given parameters.</p>";
			} else {
				// Check if a group was passed in and exists.
				if($groupCode = $member->GroupCode) {
					// Check if the member is in this group.
					if($group = DataObject::get_one('Group', "Code = '$groupCode'")) {
						if($member->inGroup($group->ID)) {
							$content = "<p><strong>$member->Email</strong> is already signed up.</p>";
						} else {
							// Member is not in the group, so add the member to the group.
							$member->Groups()->add($group);
							
							// Send an email welcoming the member.
							$email = new ConfirmNewsletterSignup_Email();
							$email->to = $member->Email;
							$email->from = Email::getAdminEmail();
							$email->subject = 'Welcome to the mailing list';
							$email->populateTemplate(array(
								'Member' => $member
							));
							$email->send();
							
							$content = "<p><strong>$member->Email</strong> has been signed up successfully. A welcome email has been sent.</p>";
						}
					}
				}
			}
		}

		// Render these variables into the template. Pass in the message from previous logic.		
		$this->customise(array(
			'Content' => $content
		));

		// Render with a chosen template.
		return $this->renderWith(array(
			'ConfirmNewsletterSignup_member',
			'Page'
		));
	}

}

class ConfirmNewsletterSignup_Email extends Email_Template {
	
	protected $ss_template = 'ConfirmNewsletterSignup_Email';
	
}

?>