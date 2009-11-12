<?php

/**
 * Estonian (Estonia) language pack
 * @package blog
 * @subpackage i18n
 */

i18n::include_locale_file('blog', 'en_US');

global $lang;

if(array_key_exists('et_EE', $lang) && is_array($lang['et_EE'])) {
	$lang['et_EE'] = array_merge($lang['en_US'], $lang['et_EE']);
} else {
	$lang['et_EE'] = $lang['en_US'];
}

$lang['et_EE']['ArchiveWidget']['DispBY'] = 'Kuva';
$lang['et_EE']['ArchiveWidget']['MONTH'] = 'kuu';
$lang['et_EE']['ArchiveWidget']['PLURALNAME'] = 'Arhiveeri vidinad';
$lang['et_EE']['ArchiveWidget']['SINGULARNAME'] = 'Arhiveeri vidin';
$lang['et_EE']['ArchiveWidget']['YEAR'] = 'aasta';
$lang['et_EE']['BlogEntry']['AU'] = 'Autor';
$lang['et_EE']['BlogEntry']['BBH'] = 'BBCode spikker';
$lang['et_EE']['BlogEntry']['CN'] = 'Sisu';
$lang['et_EE']['BlogEntry']['DT'] = 'Kuupäev';
$lang['et_EE']['BlogEntry']['PLURALNAME'] = 'Blogi sisu';
$lang['et_EE']['BlogEntry']['SINGULARNAME'] = 'Blogi sisu';
$lang['et_EE']['BlogEntry.ss']['COMMENTS'] = 'Kommentaarid';
$lang['et_EE']['BlogEntry.ss']['EDITTHIS'] = 'Muuda seda postitust';
$lang['et_EE']['BlogEntry.ss']['POSTEDBY'] = 'Autori';
$lang['et_EE']['BlogEntry.ss']['POSTEDON'] = 'poolt';
$lang['et_EE']['BlogEntry.ss']['TAGS'] = 'Sildid:';
$lang['et_EE']['BlogEntry.ss']['UNPUBLISHTHIS'] = 'Muuda see postitus avaldamatuks';
$lang['et_EE']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'Vaata kõiki postitusi siltidega';
$lang['et_EE']['BlogEntry']['TS'] = 'Sildid (komaga eraldatud)';
$lang['et_EE']['BlogHolder']['HAVENTPERM'] = 'Blogi postitamine on administraatori ülesanne. Palun logi sisse.';
$lang['et_EE']['BlogHolder']['PLURALNAME'] = 'Blogi omanikud';
$lang['et_EE']['BlogHolder']['POST'] = 'Postita blogi sissekanne';
$lang['et_EE']['BlogHolder']['RSSFEED'] = 'Selle blogi RSS voog';
$lang['et_EE']['BlogHolder']['SINGULARNAME'] = 'Blogi omanik';
$lang['et_EE']['BlogHolder']['SJ'] = 'Teema';
$lang['et_EE']['BlogHolder']['SPUC'] = 'Palun eralda sildid komadega.';
$lang['et_EE']['BlogHolder.ss']['NOENTRIES'] = 'Blogi sissekanded puuduvad';
$lang['et_EE']['BlogHolder.ss']['VIEWINGTAGGED'] = 'Kuvatakse sissekandeid siltidega';
$lang['et_EE']['BlogHolder']['SUCCONTENT'] = 'Õnnitleme, SilverStripe blogimoodul on edukalt installeeritud. Selle blogi sissekande võib ohutult ära kustutada. Oma blogi ilmet (nagu küljeribal kuvatavaid vidinaid) saad seadistada [url=admin]sisuhaldussüsteemi kaudu[/url].';
$lang['et_EE']['BlogHolder']['SUCTAGS'] = 'silverstripe, blog';
$lang['et_EE']['BlogHolder']['SUCTITLE'] = 'SilverStripe blogimoodul edukalt installeeritud';
$lang['et_EE']['BlogHolder']['TE'] = 'Näiteks: sport, isiklik, teaduslik fantastika';
$lang['et_EE']['BlogManagementWidget']['COMADM'] = 'Kommentaaride haldamine';
$lang['et_EE']['BlogManagementWidget']['PLURALNAME'] = 'Blogi Muudatuse vidinad';
$lang['et_EE']['BlogManagementWidget']['SINGULARNAME'] = 'Blogi muudatuste vidin';
$lang['et_EE']['BlogManagementWidget.ss']['LOGOUT'] = 'Logi välja';
$lang['et_EE']['BlogManagementWidget.ss']['POSTNEW'] = 'Postita uus blogi sissekanne';
$lang['et_EE']['BlogManagementWidget']['UNM1'] = 'Sul on 1 üle vaatamata kommentaar';
$lang['et_EE']['BlogManagementWidget']['UNMM'] = 'Sul on %i üle vaatamata kommentaari';
$lang['et_EE']['BlogSummary.ss']['COMMENTS'] = 'Kommentaarid';
$lang['et_EE']['BlogSummary.ss']['POSTEDBY'] = 'Postitas';
$lang['et_EE']['BlogSummary.ss']['POSTEDON'] = '-';
$lang['et_EE']['BlogSummary.ss']['VIEWFULL'] = 'Vaata tervet postitust pealkirjaga - ';
$lang['et_EE']['RSSWidget']['CT'] = 'Kohandatud pealkiri voole';
$lang['et_EE']['RSSWidget']['NTS'] = 'Kuvatavate sissekannete arv';
$lang['et_EE']['RSSWidget']['PLURALNAME'] = 'RSS vidinad';
$lang['et_EE']['RSSWidget']['SINGULARNAME'] = 'RSS vidin';
$lang['et_EE']['RSSWidget']['URL'] = 'URL või RSS voog';
$lang['et_EE']['SubscribeRSSWidget']['PLURALNAME'] = 'Telli RSS Vidinad';
$lang['et_EE']['SubscribeRSSWidget']['SINGULARNAME'] = 'Telli RSS Vidin';
$lang['et_EE']['SubscribeRSSWidget.ss']['SUBSCRIBETEXT'] = 'Telli';
$lang['et_EE']['SubscribeRSSWidget.ss']['SUBSCRIBETITLE'] = 'Telli blogi RSS kaudu';
$lang['et_EE']['TagCloudWidget']['LIMIT'] = 'Piira siltide arvu';
$lang['et_EE']['TagCloudWidget']['PLURALNAME'] = 'Lipikute pilve vidinad';
$lang['et_EE']['TagCloudWidget']['SBAL'] = 'tähestikuliselt';
$lang['et_EE']['TagCloudWidget']['SBFREQ'] = 'sageduse järgi';
$lang['et_EE']['TagCloudWidget']['SINGULARNAME'] = 'Lipikute pilve vidin';
$lang['et_EE']['TagCloudWidget']['SORTBY'] = 'Sorteeri';
$lang['et_EE']['TagCloudWidget']['TILE'] = 'Pealkiri';
$lang['et_EE']['TrackBackPing']['PLURALNAME'] = 'Pingid';
$lang['et_EE']['TrackBackPing']['SINGULARNAME'] = 'Ping';

?>