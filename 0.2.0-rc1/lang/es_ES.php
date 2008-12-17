<?php

/**
 * Spanish (Spain) language pack
 * @package modules: blog
 * @subpackage i18n
 */

i18n::include_locale_file('modules: blog', 'en_US');

global $lang;

if(array_key_exists('es_ES', $lang) && is_array($lang['es_ES'])) {
	$lang['es_ES'] = array_merge($lang['en_US'], $lang['es_ES']);
} else {
	$lang['es_ES'] = $lang['en_US'];
}

$lang['es_ES']['ArchiveWidget']['MONTH'] = 'mes';
$lang['es_ES']['ArchiveWidget']['YEAR'] = 'año';
$lang['es_ES']['BlogEntry']['AU'] = 'Autor';
$lang['es_ES']['BlogEntry']['BBH'] = 'BBCode ayuda';
$lang['es_ES']['BlogEntry']['CN'] = 'Contenido';
$lang['es_ES']['BlogEntry']['DT'] = 'Fecha';
$lang['es_ES']['BlogEntry.ss']['COMMENTS'] = 'Comentarios';
$lang['es_ES']['BlogEntry.ss']['POSTEDBY'] = 'Publicado por';
$lang['es_ES']['BlogEntry.ss']['TAGS'] = 'Etiquetas:';
$lang['es_ES']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'Ver todas las publicaciones etiquetadas';
$lang['es_ES']['BlogEntry']['TS'] = 'Etiquetas (separados por comas)';
$lang['es_ES']['BlogHolder']['SUCTAGS'] = 'silverstripe, blog';
$lang['es_ES']['BlogHolder']['TE'] = 'Por ejemplo: deporte, cine, tecnología';
$lang['es_ES']['BlogManagementWidget.ss']['LOGOUT'] = 'Salir';
$lang['es_ES']['BlogSummary.ss']['COMMENTS'] = 'Comentarios';
$lang['es_ES']['TagCloudWidget']['SBAL'] = 'alfabeto';
$lang['es_ES']['TagCloudWidget']['SBFREQ'] = 'frecuencia';
$lang['es_ES']['TagCloudWidget']['SORTBY'] = 'Ordenar por';
$lang['es_ES']['TagCloudWidget']['TILE'] = 'Título';

?>