<?php

/**
 * Spanish (Spain) language pack
 * @package blog
 * @subpackage i18n
 */

i18n::include_locale_file('blog', 'en_US');

global $lang;

if(array_key_exists('es_ES', $lang) && is_array($lang['es_ES'])) {
	$lang['es_ES'] = array_merge($lang['en_US'], $lang['es_ES']);
} else {
	$lang['es_ES'] = $lang['en_US'];
}

$lang['es_ES']['ArchiveWidget']['DispBY'] = 'Mostrar por';
$lang['es_ES']['ArchiveWidget']['MONTH'] = 'mes';
$lang['es_ES']['ArchiveWidget']['PLURALNAME'] = 'Archivar Widgets';
$lang['es_ES']['ArchiveWidget']['SINGULARNAME'] = 'Archivar Widget';
$lang['es_ES']['ArchiveWidget']['YEAR'] = 'año';
$lang['es_ES']['BlogEntry']['AU'] = 'Autor';
$lang['es_ES']['BlogEntry']['BBH'] = 'BBCode ayuda';
$lang['es_ES']['BlogEntry']['CN'] = 'Contenido';
$lang['es_ES']['BlogEntry']['DT'] = 'Fecha';
$lang['es_ES']['BlogEntry']['PLURALNAME'] = 'Entradas del Blog';
$lang['es_ES']['BlogEntry']['SINGULARNAME'] = 'Entrada del Blog';
$lang['es_ES']['BlogEntry.ss']['COMMENTS'] = 'Comentarios';
$lang['es_ES']['BlogEntry.ss']['EDITTHIS'] = 'Editar esta entrada';
$lang['es_ES']['BlogEntry.ss']['POSTEDBY'] = 'Publicado por';
$lang['es_ES']['BlogEntry.ss']['POSTEDON'] = 'en';
$lang['es_ES']['BlogEntry.ss']['TAGS'] = 'Etiquetas:';
$lang['es_ES']['BlogEntry.ss']['UNPUBLISHTHIS'] = 'Retirar esta entrada';
$lang['es_ES']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'Ver todas las publicaciones etiquetadas';
$lang['es_ES']['BlogEntry']['TS'] = 'Etiquetas (separados por comas)';
$lang['es_ES']['BlogHolder']['HAVENTPERM'] = 'Escribir en el blog es una tarea del administrador. Por favor, identifícate.';
$lang['es_ES']['BlogHolder']['PLURALNAME'] = 'Contenedores de Blog';
$lang['es_ES']['BlogHolder']['POST'] = 'Entrada del blog';
$lang['es_ES']['BlogHolder']['RSSFEED'] = 'RSS feed de este blog';
$lang['es_ES']['BlogHolder']['SINGULARNAME'] = 'Contenedor de Blog';
$lang['es_ES']['BlogHolder']['SJ'] = 'Asunto';
$lang['es_ES']['BlogHolder']['SPUC'] = 'Por favor, separa las etiquetas mediante comas.';
$lang['es_ES']['BlogHolder.ss']['NOENTRIES'] = 'No hay entradas';
$lang['es_ES']['BlogHolder.ss']['VIEWINGTAGGED'] = 'Ver entrada etiquetadas como';
$lang['es_ES']['BlogHolder']['SUCCONTENT'] = 'Felicitaciones, el módulo de blog de SilverStripe ha sido instalado correctamente. Esta entrada puede ser eliminada. Puedes configurar aspectos de tu blog (como los widgets mostrados en la barra lateral) en [url=admin]el CMS[/url].';
$lang['es_ES']['BlogHolder']['SUCTAGS'] = 'silverstripe, blog';
$lang['es_ES']['BlogHolder']['SUCTITLE'] = 'El módulo de blog de SilverStripe ha sido instalado correctamente';
$lang['es_ES']['BlogHolder']['TE'] = 'Por ejemplo: deporte, cine, tecnología';
$lang['es_ES']['BlogManagementWidget']['COMADM'] = 'Administración de comentarios';
$lang['es_ES']['BlogManagementWidget']['PLURALNAME'] = 'Widgets de gestión del Blog';
$lang['es_ES']['BlogManagementWidget']['SINGULARNAME'] = 'Widget de gestión del Blog';
$lang['es_ES']['BlogManagementWidget.ss']['LOGOUT'] = 'Salir';
$lang['es_ES']['BlogManagementWidget.ss']['POSTNEW'] = 'Escribir una nueva entrada del blog';
$lang['es_ES']['BlogManagementWidget']['UNM1'] = 'Tienes 1 comentario sin moderar';
$lang['es_ES']['BlogManagementWidget']['UNMM'] = 'Tienes %i comentarios sin moderar';
$lang['es_ES']['BlogSummary.ss']['COMMENTS'] = 'Comentarios';
$lang['es_ES']['BlogSummary.ss']['POSTEDBY'] = 'Publicado por';
$lang['es_ES']['BlogSummary.ss']['POSTEDON'] = 'en';
$lang['es_ES']['BlogSummary.ss']['VIEWFULL'] = 'Ver completo el post titulado -';
$lang['es_ES']['RSSWidget']['CT'] = 'Título personalizado  para el feed';
$lang['es_ES']['RSSWidget']['NTS'] = 'Número de registros para mostrar';
$lang['es_ES']['RSSWidget']['PLURALNAME'] = 'Widgets RSS';
$lang['es_ES']['RSSWidget']['SINGULARNAME'] = 'Widget RSS';
$lang['es_ES']['RSSWidget']['URL'] = 'URL del RSS Feed';
$lang['es_ES']['SubscribeRSSWidget']['PLURALNAME'] = 'Suscribir a Widgets RSS';
$lang['es_ES']['SubscribeRSSWidget']['SINGULARNAME'] = 'Suscribir a Widget RSS';
$lang['es_ES']['SubscribeRSSWidget.ss']['SUBSCRIBETEXT'] = 'Suscribir';
$lang['es_ES']['SubscribeRSSWidget.ss']['SUBSCRIBETITLE'] = 'Suscribirse a este blog vía RSS';
$lang['es_ES']['TagCloudWidget']['LIMIT'] = 'Limitar el número de etiquetas';
$lang['es_ES']['TagCloudWidget']['PLURALNAME'] = 'Nube de Etiquetas de Widgets';
$lang['es_ES']['TagCloudWidget']['SBAL'] = 'alfabeto';
$lang['es_ES']['TagCloudWidget']['SBFREQ'] = 'frecuencia';
$lang['es_ES']['TagCloudWidget']['SINGULARNAME'] = 'Nube de Etiquetas de Widget';
$lang['es_ES']['TagCloudWidget']['SORTBY'] = 'Ordenar por';
$lang['es_ES']['TagCloudWidget']['TILE'] = 'Título';
$lang['es_ES']['TrackBackPing']['PLURALNAME'] = 'Notificaciones de Trackback';
$lang['es_ES']['TrackBackPing']['SINGULARNAME'] = 'Notificación de Trackback';

?>