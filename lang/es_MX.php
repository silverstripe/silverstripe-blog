<?php

/**
 * Spanish (Mexico) language pack
 * @package blog
 * @subpackage i18n
 */

i18n::include_locale_file('blog', 'en_US');

global $lang;

if(array_key_exists('es_MX', $lang) && is_array($lang['es_MX'])) {
	$lang['es_MX'] = array_merge($lang['en_US'], $lang['es_MX']);
} else {
	$lang['es_MX'] = $lang['en_US'];
}

$lang['es_MX']['ArchiveWidget']['DispBY'] = 'Mostrar por';
$lang['es_MX']['ArchiveWidget']['MONTH'] = 'mes';
$lang['es_MX']['ArchiveWidget']['PLURALNAME'] = 'Archivos de Widgets';
$lang['es_MX']['ArchiveWidget']['SINGULARNAME'] = 'Archivo de Widget';
$lang['es_MX']['ArchiveWidget']['YEAR'] = 'año';
$lang['es_MX']['BlogEntry']['AU'] = 'Autor';
$lang['es_MX']['BlogEntry']['BBH'] = 'Ayuda de BBCode';
$lang['es_MX']['BlogEntry']['CN'] = 'Contenido';
$lang['es_MX']['BlogEntry']['DT'] = 'Fecha';
$lang['es_MX']['BlogEntry']['PLURALNAME'] = 'Entradas del Blog';
$lang['es_MX']['BlogEntry']['SINGULARNAME'] = 'Entrada del BLog';
$lang['es_MX']['BlogEntry.ss']['COMMENTS'] = 'Comentarios';
$lang['es_MX']['BlogEntry.ss']['EDITTHIS'] = 'Editar este mensaje';
$lang['es_MX']['BlogEntry.ss']['POSTEDBY'] = 'Enviado por';
$lang['es_MX']['BlogEntry.ss']['POSTEDON'] = 'en';
$lang['es_MX']['BlogEntry.ss']['TAGS'] = 'Etiquetas:';
$lang['es_MX']['BlogEntry.ss']['UNPUBLISHTHIS'] = 'Ocultar este mensaje';
$lang['es_MX']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'Ver todos los mensajes marcados con la etiqueta';
$lang['es_MX']['BlogEntry']['TS'] = 'Etiquetas (separadas por coma)';
$lang['es_MX']['BlogHolder']['HAVENTPERM'] = 'La corrección de la bitácora es tarea del administrador. Por favor ingresa como tal.';
$lang['es_MX']['BlogHolder']['PLURALNAME'] = 'Titulares del Blog';
$lang['es_MX']['BlogHolder']['POST'] = 'Enviar entrada a la bitácora';
$lang['es_MX']['BlogHolder']['RSSFEED'] = 'Alimentar al RSS con esta bitácora';
$lang['es_MX']['BlogHolder']['SINGULARNAME'] = 'Titular del Blog';
$lang['es_MX']['BlogHolder']['SJ'] = 'Asunto';
$lang['es_MX']['BlogHolder']['SPUC'] = 'Por favor separa etiquetas utilizando comas.';
$lang['es_MX']['BlogHolder.ss']['NOENTRIES'] = 'Bitácora vacía';
$lang['es_MX']['BlogHolder.ss']['VIEWINGTAGGED'] = 'Ver entradas etiquetadas con';
$lang['es_MX']['BlogHolder']['SUCCONTENT'] = 'Felicidades, el módulo bitácora de Silverstripe se ha instalado satisfactoriamente. Esta entrada de la bitácora se puede eliminar con seguridad. Puedes configurar aspectos de tu nueva bitácora (tal cómo reproductores mostrados en la barra lateral) en [url=admin] el CMS[/url].';
$lang['es_MX']['BlogHolder']['SUCTAGS'] = 'bitácora, silverstripe';
$lang['es_MX']['BlogHolder']['SUCTITLE'] = 'El Módulo bitácora Silverstripe se ha instalado satisfactoriamente.';
$lang['es_MX']['BlogHolder']['TE'] = 'Por ejemplo: deportes, personal. ciencia ficción';
$lang['es_MX']['BlogManagementWidget']['COMADM'] = 'Administración de comentarios';
$lang['es_MX']['BlogManagementWidget']['PLURALNAME'] = 'Widget para la Gestión de Blogs';
$lang['es_MX']['BlogManagementWidget']['SINGULARNAME'] = 'Wisdget para la Gestion del Blog';
$lang['es_MX']['BlogManagementWidget.ss']['LOGOUT'] = 'Salir';
$lang['es_MX']['BlogManagementWidget.ss']['POSTNEW'] = 'Enviar nueva entrada a la bitácora';
$lang['es_MX']['BlogManagementWidget']['UNM1'] = 'Tienes 1 comentario pendiente de moderación';
$lang['es_MX']['BlogManagementWidget']['UNMM'] = 'Tienes %i comentarios pendientes de moderación';
$lang['es_MX']['BlogSummary.ss']['COMMENTS'] = 'Comentarios';
$lang['es_MX']['BlogSummary.ss']['POSTEDBY'] = 'Enviado por';
$lang['es_MX']['BlogSummary.ss']['POSTEDON'] = 'en';
$lang['es_MX']['BlogSummary.ss']['VIEWFULL'] = 'Ver completo el mensaje titulado -';
$lang['es_MX']['RSSWidget']['CT'] = 'Título personalizado para el alimentador';
$lang['es_MX']['RSSWidget']['NTS'] = 'Número de elementos a mostrar:';
$lang['es_MX']['RSSWidget']['PLURALNAME'] = 'Widgets R S S';
$lang['es_MX']['RSSWidget']['SINGULARNAME'] = 'Widget R S S';
$lang['es_MX']['RSSWidget']['URL'] = 'URL del RSS alimentado';
$lang['es_MX']['SubscribeRSSWidget']['PLURALNAME'] = 'Widgets para Suscripción R S S';
$lang['es_MX']['SubscribeRSSWidget']['SINGULARNAME'] = 'Widget para Suscrición R S S';
$lang['es_MX']['SubscribeRSSWidget.ss']['SUBSCRIBETEXT'] = 'Suscribe';
$lang['es_MX']['SubscribeRSSWidget.ss']['SUBSCRIBETITLE'] = 'Suscribirme a este blog vía RSS';
$lang['es_MX']['TagCloudWidget']['LIMIT'] = 'Limitar el número de etiquetas';
$lang['es_MX']['TagCloudWidget']['PLURALNAME'] = 'Widgets de Nube de Etiquetas';
$lang['es_MX']['TagCloudWidget']['SBAL'] = 'alfabeto';
$lang['es_MX']['TagCloudWidget']['SBFREQ'] = 'frecuencia';
$lang['es_MX']['TagCloudWidget']['SINGULARNAME'] = 'Widget Nube de Etiquetas';
$lang['es_MX']['TagCloudWidget']['SORTBY'] = 'Ordenar por';
$lang['es_MX']['TagCloudWidget']['TILE'] = 'Título';
$lang['es_MX']['TrackBackPing']['PLURALNAME'] = 'Volver a la Pista de Pings';
$lang['es_MX']['TrackBackPing']['SINGULARNAME'] = 'Volver a la Pista de Pings';

?>