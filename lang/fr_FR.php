<?php

/**
 * French (France) language pack
 * @package blog
 * @subpackage i18n
 */

i18n::include_locale_file('blog', 'en_US');

global $lang;

if(array_key_exists('fr_FR', $lang) && is_array($lang['fr_FR'])) {
	$lang['fr_FR'] = array_merge($lang['en_US'], $lang['fr_FR']);
} else {
	$lang['fr_FR'] = $lang['en_US'];
}

$lang['fr_FR']['ArchiveWidget']['DispBY'] = 'Afficher par';
$lang['fr_FR']['ArchiveWidget']['MONTH'] = 'mois';
$lang['fr_FR']['ArchiveWidget']['PLURALNAME'] = 'Widgets Archive';
$lang['fr_FR']['ArchiveWidget']['SINGULARNAME'] = 'Widget Archive';
$lang['fr_FR']['ArchiveWidget']['YEAR'] = 'années';
$lang['fr_FR']['BlogEntry']['AU'] = 'Auteur';
$lang['fr_FR']['BlogEntry']['BBH'] = 'Aide BBCode';
$lang['fr_FR']['BlogEntry']['CN'] = 'Contenu';
$lang['fr_FR']['BlogEntry']['DT'] = 'Date';
$lang['fr_FR']['BlogEntry']['PLURALNAME'] = 'Billets de blog';
$lang['fr_FR']['BlogEntry']['SINGULARNAME'] = 'Billet de blog';
$lang['fr_FR']['BlogEntry.ss']['COMMENTS'] = 'Commentaires';
$lang['fr_FR']['BlogEntry.ss']['EDITTHIS'] = 'Modifier ce message';
$lang['fr_FR']['BlogEntry.ss']['POSTEDBY'] = 'Posté par';
$lang['fr_FR']['BlogEntry.ss']['POSTEDON'] = 'sur';
$lang['fr_FR']['BlogEntry.ss']['TAGS'] = 'Tags:';
$lang['fr_FR']['BlogEntry.ss']['UNPUBLISHTHIS'] = 'Dépublier ce message';
$lang['fr_FR']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'Voir tous les messages marqués';
$lang['fr_FR']['BlogEntry']['TS'] = 'Tags (Séparer par une virgule)';
$lang['fr_FR']['BlogHolder']['HAVENTPERM'] = 'L\'envoi de blog est réservé aux administrateurs. Loggez vous s\'il vous plaît.';
$lang['fr_FR']['BlogHolder']['PLURALNAME'] = 'Conteneurs Blogs';
$lang['fr_FR']['BlogHolder']['POST'] = 'Poster une entrée sur le blog';
$lang['fr_FR']['BlogHolder']['RSSFEED'] = 'Flux RSS de ce blog';
$lang['fr_FR']['BlogHolder']['SINGULARNAME'] = 'Conteneur Blog';
$lang['fr_FR']['BlogHolder']['SJ'] = 'Sujet';
$lang['fr_FR']['BlogHolder']['SPUC'] = 'Veuillez séparer les tags en utilisant une virgule';
$lang['fr_FR']['BlogHolder.ss']['NOENTRIES'] = 'Il n\'y a aucune entrée dans le blog';
$lang['fr_FR']['BlogHolder.ss']['VIEWINGTAGGED'] = 'Affichage des entrées marquées avec';
$lang['fr_FR']['BlogHolder']['SUCCONTENT'] = 'Félicitations, le module de blog SilverStripe a été installé avec succès. Cette entrée du blog peut être supprimée sans problème. Vous pouvez configurer les aspects de votre blog (comme les gadgets affichés dans la barre de coté) dans  [url=admin]le CMS[/url].';
$lang['fr_FR']['BlogHolder']['SUCTAGS'] = 'blog, silverStripe';
$lang['fr_FR']['BlogHolder']['SUCTITLE'] = 'Le module de blog SilverStripe a été installé avec succès';
$lang['fr_FR']['BlogHolder']['TE'] = 'Par exemple: sport, personnel, science fiction';
$lang['fr_FR']['BlogManagementWidget']['COMADM'] = 'Administration des commentaires';
$lang['fr_FR']['BlogManagementWidget']['PLURALNAME'] = 'Widgets de Management Blog';
$lang['fr_FR']['BlogManagementWidget']['SINGULARNAME'] = 'Widget de Management Blog';
$lang['fr_FR']['BlogManagementWidget.ss']['LOGOUT'] = 'Déconnexion';
$lang['fr_FR']['BlogManagementWidget.ss']['POSTNEW'] = 'Publier une nouvelle entrée dans le blog';
$lang['fr_FR']['BlogManagementWidget']['UNM1'] = 'Vous avez 1 commentaire non modéré';
$lang['fr_FR']['BlogManagementWidget']['UNMM'] = 'Vous avez %i commentaires non modérés';
$lang['fr_FR']['BlogSummary.ss']['COMMENTS'] = 'Commentaires';
$lang['fr_FR']['BlogSummary.ss']['POSTEDBY'] = 'Posté par';
$lang['fr_FR']['BlogSummary.ss']['POSTEDON'] = 'sur';
$lang['fr_FR']['BlogSummary.ss']['VIEWFULL'] = 'Voir le titre du post en entier -';
$lang['fr_FR']['RSSWidget']['CT'] = 'Titre personnalisé pour le flux';
$lang['fr_FR']['RSSWidget']['NTS'] = 'Nombre d\'éléments à afficher';
$lang['fr_FR']['RSSWidget']['PLURALNAME'] = 'Widgets de flux RSS';
$lang['fr_FR']['RSSWidget']['SINGULARNAME'] = 'Widget de flux RSS';
$lang['fr_FR']['RSSWidget']['URL'] = 'URL du flux RSS';
$lang['fr_FR']['SubscribeRSSWidget']['PLURALNAME'] = 'Widgets d\'abonnement RSS';
$lang['fr_FR']['SubscribeRSSWidget']['SINGULARNAME'] = 'Widget d\'abonnement RSS';
$lang['fr_FR']['SubscribeRSSWidget.ss']['SUBSCRIBETEXT'] = 'Souscrire';
$lang['fr_FR']['SubscribeRSSWidget.ss']['SUBSCRIBETITLE'] = 'Souscrire à ce blog par RSS';
$lang['fr_FR']['TagCloudWidget']['LIMIT'] = 'Nombre limite des tags';
$lang['fr_FR']['TagCloudWidget']['PLURALNAME'] = 'Widgets Nuage de Tags';
$lang['fr_FR']['TagCloudWidget']['SBAL'] = 'alphabet';
$lang['fr_FR']['TagCloudWidget']['SBFREQ'] = 'fréquence';
$lang['fr_FR']['TagCloudWidget']['SINGULARNAME'] = 'Widget Nuage de Tags';
$lang['fr_FR']['TagCloudWidget']['SORTBY'] = 'Trier par';
$lang['fr_FR']['TagCloudWidget']['TILE'] = 'Titre';

?>