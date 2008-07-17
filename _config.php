<?php

// Add the extension to the Member class.
Object::add_extension('Member', 'BlogRole');

// Director rule for ability to visit mysite.com/confirm/signup/123
Director::addRules(50, array(
	'confirm-subscription/$Action/$ID' => 'ConfirmNewsletterSignup'
));

LeftAndMain::require_javascript('blog/javascript/bbcodehelp.js');
?>
