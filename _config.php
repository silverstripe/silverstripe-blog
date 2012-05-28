<?php

Director::addRules(10, array(
	'metaweblog' => 'MetaWeblogController'
));

if(class_exists('WidgetArea')) DataExtension::add_to_class('BlogTree','BlogTreeExtension');

?>
