<?php

/**
 * Portuguese (Portugal) language pack
 * @package blog
 * @subpackage i18n
 */

i18n::include_locale_file('blog', 'en_US');

global $lang;

if(array_key_exists('pt_PT', $lang) && is_array($lang['pt_PT'])) {
	$lang['pt_PT'] = array_merge($lang['en_US'], $lang['pt_PT']);
} else {
	$lang['pt_PT'] = $lang['en_US'];
}

$lang['pt_PT']['BlogEntry']['PLURALNAME'] = 'Posts no Blog';
$lang['pt_PT']['BlogEntry.ss']['COMMENTS'] = 'Comentários';
$lang['pt_PT']['BlogEntry.ss']['EDITTHIS'] = 'Editar este post';
$lang['pt_PT']['BlogEntry.ss']['POSTEDBY'] = 'Inserido por';
$lang['pt_PT']['BlogEntry.ss']['POSTEDON'] = 'em';
$lang['pt_PT']['BlogEntry.ss']['UNPUBLISHTHIS'] = 'Não publicar este post';
$lang['pt_PT']['BlogHolder']['HAVENTPERM'] = 'A inserção de post é uma tarefa do administrador. Por favor faça o login.';
$lang['pt_PT']['BlogHolder']['RSSFEED'] = 'Feed RSS para este blog';
$lang['pt_PT']['BlogHolder']['SUCCONTENT'] = 'Parabéns, o módulo do blog do SilverStripe foi instalado com sucesso. Este post pode ser apagado. Poderá configurar as preferências do blog (assim como os widgets presentes no menu) através [url=admin]do CMS[/url].';
$lang['pt_PT']['BlogHolder']['SUCTITLE'] = 'O módulo do blog do SilverStripe foi instalado com sucesso.';
$lang['pt_PT']['BlogManagementWidget']['COMADM'] = 'Administração de comentários';
$lang['pt_PT']['BlogManagementWidget.ss']['LOGOUT'] = 'Sair';
$lang['pt_PT']['BlogManagementWidget']['UNM1'] = 'Existe 1 comentário por moderar';
$lang['pt_PT']['BlogManagementWidget']['UNMM'] = 'Existem %i comentários por moderar';
$lang['pt_PT']['BlogSummary.ss']['COMMENTS'] = 'Comentários';
$lang['pt_PT']['BlogSummary.ss']['POSTEDON'] = 'em';
$lang['pt_PT']['RSSWidget']['NTS'] = 'Número de items para mostrar';
$lang['pt_PT']['RSSWidget']['URL'] = 'Endereço (URL) do RSS Feed';
$lang['pt_PT']['TagCloudWidget']['SBAL'] = 'alfabeto';
$lang['pt_PT']['TagCloudWidget']['SORTBY'] = 'Ordenar por';
$lang['pt_PT']['TagCloudWidget']['TILE'] = 'Título';

?>