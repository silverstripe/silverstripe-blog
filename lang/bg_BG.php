<?php

/**
 * Bulgarian (Bulgaria) language pack
 * @package blog
 * @subpackage i18n
 */

i18n::include_locale_file('blog', 'en_US');

global $lang;

if(array_key_exists('bg_BG', $lang) && is_array($lang['bg_BG'])) {
	$lang['bg_BG'] = array_merge($lang['en_US'], $lang['bg_BG']);
} else {
	$lang['bg_BG'] = $lang['en_US'];
}

$lang['bg_BG']['ArchiveWidget']['DispBY'] = 'Покажи по';
$lang['bg_BG']['ArchiveWidget']['MONTH'] = 'месец';
$lang['bg_BG']['ArchiveWidget']['YEAR'] = 'година';
$lang['bg_BG']['BlogEntry']['AU'] = 'Автор';
$lang['bg_BG']['BlogEntry']['BBH'] = 'BBCode помощ';
$lang['bg_BG']['BlogEntry']['CN'] = 'Съдържание';
$lang['bg_BG']['BlogEntry']['DT'] = 'Дата';
$lang['bg_BG']['BlogEntry.ss']['COMMENTS'] = 'Коментари';
$lang['bg_BG']['BlogEntry.ss']['EDITTHIS'] = 'Промени тази статия';
$lang['bg_BG']['BlogEntry.ss']['POSTEDBY'] = 'Публикувано от';
$lang['bg_BG']['BlogEntry.ss']['POSTEDON'] = 'на';
$lang['bg_BG']['BlogEntry.ss']['TAGS'] = 'Марки:';
$lang['bg_BG']['BlogEntry.ss']['UNPUBLISHTHIS'] = 'Премахни от публикация тази статия';
$lang['bg_BG']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'Прегледай всички маркирани статий';
$lang['bg_BG']['BlogEntry']['TS'] = 'Марки (разделени със запетайка)';
$lang['bg_BG']['BlogHolder']['HAVENTPERM'] = 'Публикуване на блогове е администраторска задача. Моля влезте в системата.';
$lang['bg_BG']['BlogHolder']['POST'] = 'Публикувай блог статия';
$lang['bg_BG']['BlogHolder']['RSSFEED'] = 'RSS емисия за този блог';
$lang['bg_BG']['BlogHolder']['SJ'] = 'Предмет';
$lang['bg_BG']['BlogHolder']['SPUC'] = 'Моля разделете марките използвайки запетайки.';
$lang['bg_BG']['BlogHolder.ss']['NOENTRIES'] = 'Няма никакви блог статий';
$lang['bg_BG']['BlogHolder.ss']['VIEWINGTAGGED'] = 'Разглеждане на статий маркирани с';
$lang['bg_BG']['BlogHolder']['SUCCONTENT'] = 'Поздравления, SilverStripe blog модула беше инсталиран успешно. Тази блог статия може да бъде изтрита. Сега можете да конфигурирате аспектите на вашият блог (например кои widgets ще се показват) в [url=admin]CMS системата[/url].';
$lang['bg_BG']['BlogHolder']['SUCTAGS'] = 'silverstripe, блог';
$lang['bg_BG']['BlogHolder']['SUCTITLE'] = 'SilverStripe блог модул успешно инсталиран';
$lang['bg_BG']['BlogHolder']['TE'] = 'Например: спорт, наука, здраве';
$lang['bg_BG']['BlogManagementWidget']['COMADM'] = 'Администрация за коментари';
$lang['bg_BG']['BlogManagementWidget.ss']['LOGOUT'] = 'Излез';
$lang['bg_BG']['BlogManagementWidget.ss']['POSTNEW'] = 'Публикувайте нова блог статия';
$lang['bg_BG']['BlogManagementWidget']['UNM1'] = 'Вие имате 1 непрегледан коментар';
$lang['bg_BG']['BlogManagementWidget']['UNMM'] = 'Вие имате %i непрегледани коментара';
$lang['bg_BG']['BlogSummary.ss']['COMMENTS'] = 'Коментари';
$lang['bg_BG']['BlogSummary.ss']['POSTEDBY'] = 'Публикувано от';
$lang['bg_BG']['BlogSummary.ss']['POSTEDON'] = 'на';
$lang['bg_BG']['BlogSummary.ss']['VIEWFULL'] = 'Разгледай цялата статия';
$lang['bg_BG']['RSSWidget']['CT'] = 'Собствено заглавие за емисията';
$lang['bg_BG']['RSSWidget']['NTS'] = 'Брой на предмети за показване';
$lang['bg_BG']['RSSWidget']['URL'] = 'Адрес на RSS емисия';
$lang['bg_BG']['TagCloudWidget']['LIMIT'] = 'Ограничете броя на тагове';
$lang['bg_BG']['TagCloudWidget']['SBAL'] = 'азбука';
$lang['bg_BG']['TagCloudWidget']['SBFREQ'] = 'честота';
$lang['bg_BG']['TagCloudWidget']['SORTBY'] = 'Сортирай по';
$lang['bg_BG']['TagCloudWidget']['TILE'] = 'Заглавие';

?>