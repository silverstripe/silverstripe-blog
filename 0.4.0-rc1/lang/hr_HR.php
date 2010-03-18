<?php

/**
 * Croatian (Croatia) language pack
 * @package blog
 * @subpackage i18n
 */

i18n::include_locale_file('blog', 'en_US');

global $lang;

if(array_key_exists('hr_HR', $lang) && is_array($lang['hr_HR'])) {
	$lang['hr_HR'] = array_merge($lang['en_US'], $lang['hr_HR']);
} else {
	$lang['hr_HR'] = $lang['en_US'];
}

$lang['hr_HR']['ArchiveWidget']['MONTH'] = 'mjesec';
$lang['hr_HR']['ArchiveWidget']['YEAR'] = 'godina';
$lang['hr_HR']['BlogEntry']['AU'] = 'Autor';
$lang['hr_HR']['BlogEntry']['BBH'] = 'Pomoć za BBCode';
$lang['hr_HR']['BlogEntry']['CN'] = 'Sadržaj';
$lang['hr_HR']['BlogEntry']['DT'] = 'Datum';
$lang['hr_HR']['BlogEntry.ss']['COMMENTS'] = 'Komentari';
$lang['hr_HR']['BlogEntry.ss']['POSTEDBY'] = 'Objavio';
$lang['hr_HR']['BlogEntry.ss']['POSTEDON'] = 'Objavljeno';
$lang['hr_HR']['BlogEntry.ss']['TAGS'] = 'Tagovi:';
$lang['hr_HR']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'Pogledaj sve blog zapise tagirane sa';
$lang['hr_HR']['BlogEntry']['TS'] = 'Tagovi (odvojeni zarezom)';
$lang['hr_HR']['BlogHolder']['HAVENTPERM'] = 'Molimo prijavite se. Objava blog zapisa je administratorova zadaća.';
$lang['hr_HR']['BlogHolder']['POST'] = 'Objavi blog zapis';
$lang['hr_HR']['BlogHolder']['RSSFEED'] = 'RSS feed ovog bloga';
$lang['hr_HR']['BlogHolder']['SJ'] = 'Tema';
$lang['hr_HR']['BlogHolder']['SPUC'] = 'Molimo vas razdovojite tagove zarezima.';
$lang['hr_HR']['BlogHolder.ss']['NOENTRIES'] = 'Nema blog zapisa';
$lang['hr_HR']['BlogHolder.ss']['VIEWINGTAGGED'] = 'Pogledaj zapise tagirane sa';
$lang['hr_HR']['BlogHolder']['SUCCONTENT'] = 'Čestitamo, SilverStripe blog modul je uspješno instaliran. Ovaj blog zapis se slobodno može obrisati. Postavke bloga je moguće konfigurirati (kao što su widgeti prikazani sa strane) u [url=admin]CMSu[/url].';
$lang['hr_HR']['BlogHolder']['SUCTAGS'] = 'silverstripe, blog';
$lang['hr_HR']['BlogHolder']['SUCTITLE'] = 'SilverStripe blog modul uspješno je instaliran';
$lang['hr_HR']['BlogHolder']['TE'] = 'Na primjer: sport, osobno, znanstvena fantastika';
$lang['hr_HR']['BlogManagementWidget']['COMADM'] = 'Administriranje komentara';
$lang['hr_HR']['BlogManagementWidget.ss']['LOGOUT'] = 'Odlogiraj se';
$lang['hr_HR']['BlogManagementWidget.ss']['POSTNEW'] = 'Objavi novi blog zapis';
$lang['hr_HR']['BlogSummary.ss']['COMMENTS'] = 'Komentari';
$lang['hr_HR']['BlogSummary.ss']['POSTEDON'] = 'Objavljeno';
$lang['hr_HR']['BlogSummary.ss']['VIEWFULL'] = 'Pogledaj potpuni blog zapis pod nazivom - ';
$lang['hr_HR']['RSSWidget']['NTS'] = 'Broj orikazanih zapisa';
$lang['hr_HR']['RSSWidget']['URL'] = 'URL RSS feeda';
$lang['hr_HR']['TagCloudWidget']['LIMIT'] = 'Ograniči broj tagova';
$lang['hr_HR']['TagCloudWidget']['SBAL'] = 'abecedi';
$lang['hr_HR']['TagCloudWidget']['SBFREQ'] = 'učestalosti (frekvenciji)';
$lang['hr_HR']['TagCloudWidget']['SORTBY'] = 'Sortiraj prema';
$lang['hr_HR']['TagCloudWidget']['TILE'] = 'Naslov';

?>