<?php

/**
 * Malay (Malaysia) language pack
 * @package blog
 * @subpackage i18n
 */

i18n::include_locale_file('blog', 'en_US');

global $lang;

if(array_key_exists('ms_MY', $lang) && is_array($lang['ms_MY'])) {
	$lang['ms_MY'] = array_merge($lang['en_US'], $lang['ms_MY']);
} else {
	$lang['ms_MY'] = $lang['en_US'];
}

$lang['ms_MY']['ArchiveWidget']['PLURALNAME'] = 'Arkiv Widgets';
$lang['ms_MY']['ArchiveWidget']['SINGULARNAME'] = 'Arkiv Widget';
$lang['ms_MY']['BlogEntry']['PLURALNAME'] = 'Blog Poster';
$lang['ms_MY']['BlogEntry']['SINGULARNAME'] = 'Blog Post';
$lang['ms_MY']['BlogHolder']['PLURALNAME'] = 'Blog holdere';
$lang['ms_MY']['BlogHolder']['SINGULARNAME'] = 'Blog holder';
$lang['ms_MY']['BlogManagementWidget']['PLURALNAME'] = 'Blog Admin Widgets';
$lang['ms_MY']['BlogManagementWidget']['SINGULARNAME'] = 'Blog Admin Widgets';
$lang['ms_MY']['RSSWidget']['PLURALNAME'] = 'R S S Widgets';
$lang['ms_MY']['RSSWidget']['SINGULARNAME'] = 'R S S Widget';
$lang['ms_MY']['SubscribeRSSWidget']['PLURALNAME'] = 'Abonner på R S S Widgets';
$lang['ms_MY']['SubscribeRSSWidget']['SINGULARNAME'] = 'Abonner på R S S Widgets';
$lang['ms_MY']['TagCloudWidget']['PLURALNAME'] = 'Tag Cloud Widgets';
$lang['ms_MY']['TagCloudWidget']['SINGULARNAME'] = 'Tag Cloud Widget';

?>