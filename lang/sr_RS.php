<?php

/**
 * Serbian (Serbia) language pack
 * @package blog
 * @subpackage i18n
 */

i18n::include_locale_file('blog', 'en_US');

global $lang;

if(array_key_exists('sr_RS', $lang) && is_array($lang['sr_RS'])) {
	$lang['sr_RS'] = array_merge($lang['en_US'], $lang['sr_RS']);
} else {
	$lang['sr_RS'] = $lang['en_US'];
}

$lang['sr_RS']['ArchiveWidget']['DispBY'] = 'Прикажи по';
$lang['sr_RS']['ArchiveWidget']['MONTH'] = 'месецу';
$lang['sr_RS']['ArchiveWidget']['YEAR'] = 'години';
$lang['sr_RS']['BlogEntry']['AU'] = 'Аутор';
$lang['sr_RS']['BlogEntry']['BBH'] = 'Помоћ око ББкода';
$lang['sr_RS']['BlogEntry']['CN'] = 'Садржај';
$lang['sr_RS']['BlogEntry']['DT'] = 'Датум';
$lang['sr_RS']['BlogEntry.ss']['COMMENTS'] = 'Коментари';
$lang['sr_RS']['BlogEntry.ss']['EDITTHIS'] = 'Измени овај унос';
$lang['sr_RS']['BlogEntry.ss']['POSTEDBY'] = 'Послао';
$lang['sr_RS']['BlogEntry.ss']['POSTEDON'] = ' ';
$lang['sr_RS']['BlogEntry.ss']['TAGS'] = 'Тагови:';
$lang['sr_RS']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'Погледајте све уносе означене са';
$lang['sr_RS']['BlogEntry']['TS'] = 'Ознаке (одвојене зарезом)';
$lang['sr_RS']['BlogHolder']['HAVENTPERM'] = 'Слање нових уноса у блог је администраторски задатак. Пријавите се.';
$lang['sr_RS']['BlogHolder']['POST'] = 'Пошаљи унос у блог';
$lang['sr_RS']['BlogHolder']['RSSFEED'] = 'RSS довод овог блога';
$lang['sr_RS']['BlogHolder']['SJ'] = 'Наслов';
$lang['sr_RS']['BlogHolder']['SPUC'] = 'Одвојите ознаке зарезима.';
$lang['sr_RS']['BlogHolder.ss']['NOENTRIES'] = 'Нема уноса у блог';
$lang['sr_RS']['BlogHolder.ss']['VIEWINGTAGGED'] = 'Приказујем уносе означене са';
$lang['sr_RS']['BlogHolder']['SUCTAGS'] = 'silverstripe, блог';
$lang['sr_RS']['BlogHolder']['SUCTITLE'] = 'SilverStripe модул за блог је успешно инсталиран';
$lang['sr_RS']['BlogHolder']['TE'] = 'На пример: спорт, лично, научна фантастика';
$lang['sr_RS']['BlogManagementWidget']['COMADM'] = 'Администрација коментара';
$lang['sr_RS']['BlogManagementWidget.ss']['LOGOUT'] = 'Одјави се';
$lang['sr_RS']['BlogManagementWidget.ss']['POSTNEW'] = 'Пошаљи нов унос у блог';
$lang['sr_RS']['BlogSummary.ss']['COMMENTS'] = 'Коментари';
$lang['sr_RS']['BlogSummary.ss']['POSTEDON'] = 'у';
$lang['sr_RS']['BlogSummary.ss']['VIEWFULL'] = 'Погледајте цео унос насловљен - ';
$lang['sr_RS']['RSSWidget']['CT'] = 'Прилагођени наслов за овај довод';
$lang['sr_RS']['RSSWidget']['NTS'] = 'Број ставки за приказивање';
$lang['sr_RS']['RSSWidget']['URL'] = 'URL RSS довода';
$lang['sr_RS']['TagCloudWidget']['LIMIT'] = 'Ограничи број тагова';
$lang['sr_RS']['TagCloudWidget']['SBAL'] = 'азбучном реду';
$lang['sr_RS']['TagCloudWidget']['SBFREQ'] = 'фреквенцији';
$lang['sr_RS']['TagCloudWidget']['SORTBY'] = 'Сортирај по';
$lang['sr_RS']['TagCloudWidget']['TILE'] = 'Наслов';

?>