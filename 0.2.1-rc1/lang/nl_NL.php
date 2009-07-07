<?php

/**
 * Dutch (Netherlands) language pack
 * @package modules: blog
 * @subpackage i18n
 */

i18n::include_locale_file('modules: blog', 'en_US');

global $lang;

if(array_key_exists('nl_NL', $lang) && is_array($lang['nl_NL'])) {
	$lang['nl_NL'] = array_merge($lang['en_US'], $lang['nl_NL']);
} else {
	$lang['nl_NL'] = $lang['en_US'];
}

$lang['nl_NL']['BlogEntry.ss']['COMMENTS'] = 'Reacties';
$lang['nl_NL']['BlogEntry.ss']['EDITTHIS'] = 'Bewerk dit post';
$lang['nl_NL']['BlogEntry.ss']['POSTEDBY'] = 'Auteur';
$lang['nl_NL']['BlogEntry.ss']['POSTEDON'] = 'Aan';
$lang['nl_NL']['BlogEntry.ss']['TAGS'] = 'Tags:';
$lang['nl_NL']['BlogEntry.ss']['UNPUBLISHTHIS'] = 'onpubliceer dit post';
$lang['nl_NL']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'Bekijk alle posten getiteld';
$lang['nl_NL']['BlogHolder.ss']['NOENTRIES'] = 'Er zijn geen blog entrees ';
$lang['nl_NL']['BlogHolder.ss']['VIEWINGTAGGED'] = 'U bekijkt entrees tagged met';
$lang['nl_NL']['BlogManagementWidget.ss']['LOGOUT'] = 'Afmelden';
$lang['nl_NL']['BlogManagementWidget.ss']['POSTNEW'] = 'Publiceer een nieuw blog entree';
$lang['nl_NL']['BlogSummary.ss']['COMMENTS'] = 'Reacties';
$lang['nl_NL']['BlogSummary.ss']['POSTEDON'] = 'Aan';
$lang['nl_NL']['BlogSummary.ss']['VIEWFULL'] = 'Bekijk het gehele post getitled';
$lang['nl_NL']['TagCloudWidget']['SBAL'] = 'alfabet';
$lang['nl_NL']['TagCloudWidget']['SBFREQ'] = 'frequentie';
$lang['nl_NL']['TagCloudWidget']['SORTBY'] = 'Sorteer bij';

?>