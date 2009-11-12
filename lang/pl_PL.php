<?php

/**
 * Polish (Poland) language pack
 * @package blog
 * @subpackage i18n
 */

i18n::include_locale_file('blog', 'en_US');

global $lang;

if(array_key_exists('pl_PL', $lang) && is_array($lang['pl_PL'])) {
	$lang['pl_PL'] = array_merge($lang['en_US'], $lang['pl_PL']);
} else {
	$lang['pl_PL'] = $lang['en_US'];
}

$lang['pl_PL']['ArchiveWidget']['DispBY'] = 'Wyświetlaj według';
$lang['pl_PL']['ArchiveWidget']['MONTH'] = 'miesiąc';
$lang['pl_PL']['ArchiveWidget']['YEAR'] = 'rok';
$lang['pl_PL']['BlogEntry']['AU'] = 'Autor';
$lang['pl_PL']['BlogEntry']['BBH'] = 'Pomoc BBCode';
$lang['pl_PL']['BlogEntry']['CN'] = 'Zawartość';
$lang['pl_PL']['BlogEntry']['DT'] = 'Data';
$lang['pl_PL']['BlogEntry']['PLURALNAME'] = 'Wpisy bloga';
$lang['pl_PL']['BlogEntry']['SINGULARNAME'] = 'Wpis bloga';
$lang['pl_PL']['BlogEntry.ss']['COMMENTS'] = 'Komentarze';
$lang['pl_PL']['BlogEntry.ss']['EDITTHIS'] = 'Edytuj ten post';
$lang['pl_PL']['BlogEntry.ss']['POSTEDBY'] = 'Dodane przez';
$lang['pl_PL']['BlogEntry.ss']['POSTEDON'] = 'Opublikowano';
$lang['pl_PL']['BlogEntry.ss']['TAGS'] = 'Tagi:';
$lang['pl_PL']['BlogEntry.ss']['UNPUBLISHTHIS'] = 'Cofnij publikację tego postu';
$lang['pl_PL']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'Zobacz wszystkie posty otagowane jako';
$lang['pl_PL']['BlogEntry']['TS'] = 'Tagi (oddziel przecinkami)';
$lang['pl_PL']['BlogHolder']['HAVENTPERM'] = 'Tylko administrator może publikować wpisy na blogu. Zaloguj się.';
$lang['pl_PL']['BlogHolder']['PLURALNAME'] = 'Blog Listy';
$lang['pl_PL']['BlogHolder']['POST'] = 'Publikuj wpis';
$lang['pl_PL']['BlogHolder']['RSSFEED'] = 'Subskrybuj wpisy na tym blogu przez RSS';
$lang['pl_PL']['BlogHolder']['SINGULARNAME'] = 'Blog Lista';
$lang['pl_PL']['BlogHolder']['SJ'] = 'Temat';
$lang['pl_PL']['BlogHolder']['SPUC'] = 'Oddziel tagi używając przecinków.';
$lang['pl_PL']['BlogHolder.ss']['NOENTRIES'] = 'Nie ma żadnych wpisów na blogu';
$lang['pl_PL']['BlogHolder.ss']['VIEWINGTAGGED'] = 'Zobacz wpisy otagowane jako';
$lang['pl_PL']['BlogHolder']['SUCCONTENT'] = 'Gratulacje, moduł bloga SilverStripe został poprawnie zainstalowany. Możesz spokojnie usunąć ten wpis. Możesz skonfigurować różne części swojego bloga (takie jak widgety, wyświetlane z boku) w [url=admin]CMSie[/url].';
$lang['pl_PL']['BlogHolder']['SUCTAGS'] = 'silverstripe, blog';
$lang['pl_PL']['BlogHolder']['SUCTITLE'] = 'Blog SilverStripe został poprawnie zainstalowany.';
$lang['pl_PL']['BlogHolder']['TE'] = 'Na przykład: sport, osobiste, science fiction';
$lang['pl_PL']['BlogManagementWidget']['COMADM'] = 'Administracja komentarzami';
$lang['pl_PL']['BlogManagementWidget.ss']['LOGOUT'] = 'Wyloguj';
$lang['pl_PL']['BlogManagementWidget.ss']['POSTNEW'] = 'Dodaj nowy wpis';
$lang['pl_PL']['BlogManagementWidget']['UNM1'] = 'Masz 1 niesprawdzony komentarz';
$lang['pl_PL']['BlogManagementWidget']['UNMM'] = 'Masz %i niesprawdzonych komentarzy';
$lang['pl_PL']['BlogSummary.ss']['COMMENTS'] = 'Komentarze';
$lang['pl_PL']['BlogSummary.ss']['POSTEDBY'] = 'Napisane przez';
$lang['pl_PL']['BlogSummary.ss']['POSTEDON'] = 'Opublikowano';
$lang['pl_PL']['BlogSummary.ss']['VIEWFULL'] = 'Zobacz pełny post zatytułowany - ';
$lang['pl_PL']['RSSWidget']['CT'] = 'Tytuł dla kanału';
$lang['pl_PL']['RSSWidget']['NTS'] = 'Ilość pokazywanych wpisów';
$lang['pl_PL']['RSSWidget']['PLURALNAME'] = 'Widżety RSS';
$lang['pl_PL']['RSSWidget']['SINGULARNAME'] = 'Widżet RSS';
$lang['pl_PL']['RSSWidget']['URL'] = 'URL RSS';
$lang['pl_PL']['SubscribeRSSWidget']['PLURALNAME'] = 'Subksrybuj widżety RSS';
$lang['pl_PL']['SubscribeRSSWidget']['SINGULARNAME'] = 'Subksrybuj widżet RSS';
$lang['pl_PL']['TagCloudWidget']['LIMIT'] = 'Limit tagów';
$lang['pl_PL']['TagCloudWidget']['SBAL'] = 'alfabetu';
$lang['pl_PL']['TagCloudWidget']['SBFREQ'] = 'częstości występowania';
$lang['pl_PL']['TagCloudWidget']['SORTBY'] = 'Sortuj według';
$lang['pl_PL']['TagCloudWidget']['TILE'] = 'Tytuł';

?>