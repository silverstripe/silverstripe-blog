<?php

/**
 * German (Germany) language pack
 * @package blog
 * @subpackage i18n
 */

i18n::include_locale_file('blog', 'en_US');

global $lang;

if(array_key_exists('de_DE', $lang) && is_array($lang['de_DE'])) {
	$lang['de_DE'] = array_merge($lang['en_US'], $lang['de_DE']);
} else {
	$lang['de_DE'] = $lang['en_US'];
}

$lang['de_DE']['ArchiveWidget']['DispBY'] = 'Anzeige nach';
$lang['de_DE']['ArchiveWidget']['MONTH'] = 'Monat';
$lang['de_DE']['ArchiveWidget']['PLURALNAME'] = 'Archiv-Widgets';
$lang['de_DE']['ArchiveWidget']['SINGULARNAME'] = 'Archiv-Widget';
$lang['de_DE']['ArchiveWidget']['YEAR'] = 'Jahr';
$lang['de_DE']['BlogEntry']['AU'] = 'Autor';
$lang['de_DE']['BlogEntry']['BBH'] = 'BBCode Hilfe';
$lang['de_DE']['BlogEntry']['CN'] = 'Inhalt';
$lang['de_DE']['BlogEntry']['DT'] = 'Datum';
$lang['de_DE']['BlogEntry']['PLURALNAME'] = 'Blog-Einträge';
$lang['de_DE']['BlogEntry']['SINGULARNAME'] = 'Blog-Eintrag';
$lang['de_DE']['BlogEntry.ss']['COMMENTS'] = 'Kommentare';
$lang['de_DE']['BlogEntry.ss']['EDITTHIS'] = 'Eintrag bearbeiten';
$lang['de_DE']['BlogEntry.ss']['POSTEDBY'] = 'Eintrag von';
$lang['de_DE']['BlogEntry.ss']['POSTEDON'] = 'am';
$lang['de_DE']['BlogEntry.ss']['TAGS'] = 'Tags:';
$lang['de_DE']['BlogEntry.ss']['UNPUBLISHTHIS'] = 'Eintrag zurückziehen';
$lang['de_DE']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'Alle Beiträge mit Tag';
$lang['de_DE']['BlogEntry']['TS'] = 'Tags (Komma getrennt)';
$lang['de_DE']['BlogHolder']['HAVENTPERM'] = 'Beiträge können nur von Administratoren eingestellt werden. Bitte einloggen.';
$lang['de_DE']['BlogHolder']['PLURALNAME'] = 'Blog-Besitzer';
$lang['de_DE']['BlogHolder']['POST'] = 'Eintrag senden';
$lang['de_DE']['BlogHolder']['RSSFEED'] = 'RSS Feed dieser Blogs';
$lang['de_DE']['BlogHolder']['SINGULARNAME'] = 'Blog-Besitzer';
$lang['de_DE']['BlogHolder']['SJ'] = 'Betreff';
$lang['de_DE']['BlogHolder']['SPUC'] = 'Bitte Tags mit Kommata trennen.';
$lang['de_DE']['BlogHolder.ss']['NOENTRIES'] = 'Es gibt keine Blog Einträge';
$lang['de_DE']['BlogHolder.ss']['VIEWINGTAGGED'] = 'Anzeige der Einträge mit Tag';
$lang['de_DE']['BlogHolder']['SUCCONTENT'] = 'Herzlichen Glückwunsch, das SilverStripe Blog Modul wurde erfolgreich installiert. Dieser Blog-Eintrag kann sicher gelöscht werden. Sie können die Blog-Einstellungen (z.B. die angezeigten Widgets in der Sidebar) im admin-Bereich verändern.';
$lang['de_DE']['BlogHolder']['SUCTAGS'] = 'silverstripe, blog';
$lang['de_DE']['BlogHolder']['SUCTITLE'] = 'SilverStripe Blog Module erfolgreich installiert';
$lang['de_DE']['BlogHolder']['TE'] = 'Zum Beispiel: sport, musik, video';
$lang['de_DE']['BlogManagementWidget']['COMADM'] = 'Kommentare bearbeiten';
$lang['de_DE']['BlogManagementWidget']['PLURALNAME'] = 'Blog-Verwaltungs-Widgets';
$lang['de_DE']['BlogManagementWidget']['SINGULARNAME'] = 'Blog-Verwaltungs-Widget';
$lang['de_DE']['BlogManagementWidget.ss']['LOGOUT'] = 'Logout';
$lang['de_DE']['BlogManagementWidget.ss']['POSTNEW'] = 'Neuen Eintrag schreiben';
$lang['de_DE']['BlogManagementWidget']['UNM1'] = 'Sie haben 1 noch nicht moderierten Kommentar';
$lang['de_DE']['BlogManagementWidget']['UNMM'] = 'Sie haben %i noch nicht moderierte Kommentare';
$lang['de_DE']['BlogSummary.ss']['COMMENTS'] = 'Kommentare';
$lang['de_DE']['BlogSummary.ss']['POSTEDBY'] = 'Verfasst von';
$lang['de_DE']['BlogSummary.ss']['POSTEDON'] = 'am';
$lang['de_DE']['BlogSummary.ss']['VIEWFULL'] = 'Detaillierte Ansicht von dem Titel --';
$lang['de_DE']['RSSWidget']['CT'] = 'Eigener Titel für den feed';
$lang['de_DE']['RSSWidget']['NTS'] = 'Anzahl der angezeigten Items';
$lang['de_DE']['RSSWidget']['PLURALNAME'] = 'RSS-Widgets';
$lang['de_DE']['RSSWidget']['SINGULARNAME'] = 'RSS-Widget';
$lang['de_DE']['RSSWidget']['URL'] = 'URL des RSS Feed der anderen Seite. Bitte vergewissern Sie sich, dass diese URL auf einen RSS Feed verweist.';
$lang['de_DE']['SubscribeRSSWidget']['PLURALNAME'] = 'RSS-Abonnier-Widgets';
$lang['de_DE']['SubscribeRSSWidget']['SINGULARNAME'] = 'RSS-Abonnier-Widget';
$lang['de_DE']['SubscribeRSSWidget.ss']['SUBSCRIBETEXT'] = 'Abonnieren';
$lang['de_DE']['SubscribeRSSWidget.ss']['SUBSCRIBETITLE'] = 'Abonniere diesen Blog per RSS';
$lang['de_DE']['TagCloudWidget']['LIMIT'] = 'Anzahl der erlaubten Tags';
$lang['de_DE']['TagCloudWidget']['PLURALNAME'] = 'Tag-Cloud-Widgets';
$lang['de_DE']['TagCloudWidget']['SBAL'] = 'Alphabet';
$lang['de_DE']['TagCloudWidget']['SBFREQ'] = 'Häufigkeit';
$lang['de_DE']['TagCloudWidget']['SINGULARNAME'] = 'Tag-Cloud-Widget';
$lang['de_DE']['TagCloudWidget']['SORTBY'] = 'Sortiert nach';
$lang['de_DE']['TagCloudWidget']['TILE'] = 'Titel';
$lang['de_DE']['TrackBackPing']['PLURALNAME'] = 'Ping-Verfolgung';
$lang['de_DE']['TrackBackPing']['SINGULARNAME'] = 'Ping-Verfolgung';

?>