<?php

/**
 * Danish (Denmark) language pack
 * @package blog
 * @subpackage i18n
 */

i18n::include_locale_file('blog', 'en_US');

global $lang;

if(array_key_exists('da_DK', $lang) && is_array($lang['da_DK'])) {
	$lang['da_DK'] = array_merge($lang['en_US'], $lang['da_DK']);
} else {
	$lang['da_DK'] = $lang['en_US'];
}

$lang['da_DK']['ArchiveWidget']['DispBY'] = 'Vis efter';
$lang['da_DK']['ArchiveWidget']['MONTH'] = 'måned';
$lang['da_DK']['ArchiveWidget']['YEAR'] = 'år';
$lang['da_DK']['BlogEntry']['AU'] = 'Forfatter';
$lang['da_DK']['BlogEntry']['BBH'] = 'BBCode hjælp';
$lang['da_DK']['BlogEntry']['CN'] = 'Indhold';
$lang['da_DK']['BlogEntry']['DT'] = 'Dato';
$lang['da_DK']['BlogEntry.ss']['COMMENTS'] = 'Kommentarer';
$lang['da_DK']['BlogEntry.ss']['EDITTHIS'] = 'Rediger dette indlæg';
$lang['da_DK']['BlogEntry.ss']['POSTEDBY'] = 'Indsendt af';
$lang['da_DK']['BlogEntry.ss']['POSTEDON'] = 'd. ';
$lang['da_DK']['BlogEntry.ss']['TAGS'] = 'Tags:';
$lang['da_DK']['BlogEntry.ss']['UNPUBLISHTHIS'] = 'Upubliceret dette indlæg';
$lang['da_DK']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'Vis alle indlæg tagged ';
$lang['da_DK']['BlogEntry']['TS'] = 'Tags (kommasep.)';
$lang['da_DK']['BlogHolder']['HAVENTPERM'] = 'At sende indlæg kræver er en administrativ opgave. Log venligst ind.';
$lang['da_DK']['BlogHolder']['POST'] = 'Send blog indlæg';
$lang['da_DK']['BlogHolder']['RSSFEED'] = 'RSS feed af denne blog';
$lang['da_DK']['BlogHolder']['SJ'] = 'Emne';
$lang['da_DK']['BlogHolder']['SPUC'] = 'Husk at seperere tags med komma';
$lang['da_DK']['BlogHolder.ss']['NOENTRIES'] = 'Der er ingen blog indlæg';
$lang['da_DK']['BlogHolder.ss']['VIEWINGTAGGED'] = 'Se indlæg tagged med';
$lang['da_DK']['BlogHolder']['SUCCONTENT'] = 'Tillykke, SilverStripe blog modul er installeret succesfuldt. Dette blog indlæg kan du trygt slette. Du kan konfigurere bloggen som du har lyst (f.eks. widgets placeret i sidepanelet)';
$lang['da_DK']['BlogHolder']['SUCTAGS'] = 'Silverstripe, blog';
$lang['da_DK']['BlogHolder']['SUCTITLE'] = 'SilverStripe Blog modul installeret succesfuldt';
$lang['da_DK']['BlogHolder']['TE'] = 'F.eks. sport, personligt, science fiction';
$lang['da_DK']['BlogManagementWidget']['COMADM'] = 'Kommentaradministration';
$lang['da_DK']['BlogManagementWidget.ss']['LOGOUT'] = 'Log ud';
$lang['da_DK']['BlogManagementWidget.ss']['POSTNEW'] = 'Send et nyt blog indlæg';
$lang['da_DK']['BlogManagementWidget']['UNM1'] = 'Du har 1 uvurderet kommentar';
$lang['da_DK']['BlogManagementWidget']['UNMM'] = 'Du har %i uvurderet kommentarer';
$lang['da_DK']['BlogSummary.ss']['COMMENTS'] = 'Kommentarer';
$lang['da_DK']['BlogSummary.ss']['POSTEDON'] = 'd. ';
$lang['da_DK']['RSSWidget']['CT'] = 'Brugerdefineret title for dette feed';
$lang['da_DK']['RSSWidget']['NTS'] = 'Antal af viste indlæg ';
$lang['da_DK']['RSSWidget']['URL'] = 'URL eller RSS Feed';
$lang['da_DK']['TagCloudWidget']['LIMIT'] = 'Begrænsning af antalle af tags';
$lang['da_DK']['TagCloudWidget']['SBAL'] = 'alfabet';
$lang['da_DK']['TagCloudWidget']['SBFREQ'] = 'frekvens';
$lang['da_DK']['TagCloudWidget']['SORTBY'] = 'Sorter efter';
$lang['da_DK']['TagCloudWidget']['TILE'] = 'Titel';

?>