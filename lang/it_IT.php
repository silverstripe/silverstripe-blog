<?php

/**
 * Italian (Italy) language pack
 * @package modules: blog
 * @subpackage i18n
 */

i18n::include_locale_file('modules: blog', 'en_US');

global $lang;

if(array_key_exists('it_IT', $lang) && is_array($lang['it_IT'])) {
	$lang['it_IT'] = array_merge($lang['en_US'], $lang['it_IT']);
} else {
	$lang['it_IT'] = $lang['en_US'];
}

$lang['it_IT']['ArchiveWidget']['MONTH'] = 'mese';
$lang['it_IT']['ArchiveWidget']['YEAR'] = 'anno';
$lang['it_IT']['BlogEntry']['AU'] = 'Autore';
$lang['it_IT']['BlogEntry']['DT'] = 'Data';
$lang['it_IT']['BlogEntry.ss']['COMMENTS'] = 'Commenti';
$lang['it_IT']['BlogHolder']['SJ'] = 'Soggetto';
$lang['it_IT']['BlogHolder']['SUCTAGS'] = 'silverstripe, blog';
$lang['it_IT']['BlogSummary.ss']['COMMENTS'] = 'Commenti';
$lang['it_IT']['TagCloudWidget']['SBAL'] = 'alfabeto';
$lang['it_IT']['TagCloudWidget']['SBFREQ'] = 'frequenza';
$lang['it_IT']['TagCloudWidget']['SORTBY'] = 'Ordina per';
$lang['it_IT']['TagCloudWidget']['TILE'] = 'Titolo';

?>