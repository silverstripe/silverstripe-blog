<?php

/**
 * Russian (Russia) language pack
 * @package blog
 * @subpackage i18n
 */

i18n::include_locale_file('blog', 'en_US');

global $lang;

if(array_key_exists('ru_RU', $lang) && is_array($lang['ru_RU'])) {
	$lang['ru_RU'] = array_merge($lang['en_US'], $lang['ru_RU']);
} else {
	$lang['ru_RU'] = $lang['en_US'];
}

$lang['ru_RU']['ArchiveWidget']['DispBY'] = 'Группировать по';
$lang['ru_RU']['ArchiveWidget']['MONTH'] = 'месяцу';
$lang['ru_RU']['ArchiveWidget']['YEAR'] = 'году';
$lang['ru_RU']['BlogEntry']['AU'] = 'Автор';
$lang['ru_RU']['BlogEntry']['BBH'] = 'Подсказка по BBCode';
$lang['ru_RU']['BlogEntry']['CN'] = 'Содержимое';
$lang['ru_RU']['BlogEntry']['DT'] = 'Дата';
$lang['ru_RU']['BlogEntry.ss']['COMMENTS'] = 'Комментарии';
$lang['ru_RU']['BlogEntry.ss']['EDITTHIS'] = 'Редакт. эту запись';
$lang['ru_RU']['BlogEntry.ss']['POSTEDBY'] = 'Автор: ';
$lang['ru_RU']['BlogEntry.ss']['POSTEDON'] = ':';
$lang['ru_RU']['BlogEntry.ss']['TAGS'] = 'Метки:';
$lang['ru_RU']['BlogEntry.ss']['UNPUBLISHTHIS'] = 'Убрать запись с опубликов. сайта';
$lang['ru_RU']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'Смотреть все записи с метками';
$lang['ru_RU']['BlogEntry']['TS'] = 'Метки (раздел. запят.)';
$lang['ru_RU']['BlogHolder']['HAVENTPERM'] = 'Публикация записей в блоге доступна только администратору. Пожалуйста, войдите.';
$lang['ru_RU']['BlogHolder']['POST'] = 'Опубликовать запись в блоге';
$lang['ru_RU']['BlogHolder']['RSSFEED'] = 'RSS подписка на этот блог';
$lang['ru_RU']['BlogHolder']['SJ'] = 'Тема';
$lang['ru_RU']['BlogHolder']['SPUC'] = 'Разделяйте метки запятыми.';
$lang['ru_RU']['BlogHolder.ss']['NOENTRIES'] = 'В блоге нет записей';
$lang['ru_RU']['BlogHolder.ss']['VIEWINGTAGGED'] = 'Просмотр записей с метками ';
$lang['ru_RU']['BlogHolder']['SUCCONTENT'] = 'Поздравляем, модуль блога SilverStripe был успешно установлен. Эта запись в блоге может быть удалена. Вы можете настроить вид блога (например, отображение виджетов в боковой панели) в [url=admin]Системе Управления Содержимым[/url].';
$lang['ru_RU']['BlogHolder']['SUCTAGS'] = 'silverstripe, блог';
$lang['ru_RU']['BlogHolder']['SUCTITLE'] = 'Модуль блога SilverStripe успешно установлен';
$lang['ru_RU']['BlogHolder']['TE'] = 'Например - спорт, личное, фантастика';
$lang['ru_RU']['BlogManagementWidget']['COMADM'] = 'Управление комментариями';
$lang['ru_RU']['BlogManagementWidget.ss']['LOGOUT'] = 'Выход';
$lang['ru_RU']['BlogManagementWidget.ss']['POSTNEW'] = 'Опубликовать новую запись';
$lang['ru_RU']['BlogManagementWidget']['UNM1'] = 'У вас 1 непроверенный комментарий';
$lang['ru_RU']['BlogManagementWidget']['UNMM'] = 'У вас %i непроверенных комментариев';
$lang['ru_RU']['BlogSummary.ss']['COMMENTS'] = 'Комментарии';
$lang['ru_RU']['BlogSummary.ss']['POSTEDBY'] = 'Автор:';
$lang['ru_RU']['BlogSummary.ss']['POSTEDON'] = ':';
$lang['ru_RU']['BlogSummary.ss']['VIEWFULL'] = 'См. полностью запись под названием: ';
$lang['ru_RU']['RSSWidget']['CT'] = 'Собственное название ленты новостей';
$lang['ru_RU']['RSSWidget']['NTS'] = 'Показывать кол-во записей';
$lang['ru_RU']['RSSWidget']['URL'] = 'URL ленты RSS';
$lang['ru_RU']['TagCloudWidget']['LIMIT'] = 'Ограничить кол-во меток';
$lang['ru_RU']['TagCloudWidget']['SBAL'] = 'алфавиту';
$lang['ru_RU']['TagCloudWidget']['SBFREQ'] = 'частоте';
$lang['ru_RU']['TagCloudWidget']['SORTBY'] = 'Сортировать по';
$lang['ru_RU']['TagCloudWidget']['TILE'] = 'Название';

?>