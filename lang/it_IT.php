<?php

/**
 * Italian (Italy) language pack
 * @package blog
 * @subpackage i18n
 */

i18n::include_locale_file('blog', 'en_US');

global $lang;

if(array_key_exists('it_IT', $lang) && is_array($lang['it_IT'])) {
	$lang['it_IT'] = array_merge($lang['en_US'], $lang['it_IT']);
} else {
	$lang['it_IT'] = $lang['en_US'];
}

$lang['it_IT']['ArchiveWidget']['DispBY'] = 'Visualizzato da';
$lang['it_IT']['ArchiveWidget']['MONTH'] = 'mese';
$lang['it_IT']['ArchiveWidget']['PLURALNAME'] = 'Widget Archiviazione';
$lang['it_IT']['ArchiveWidget']['SINGULARNAME'] = 'Widget Archiviazione';
$lang['it_IT']['ArchiveWidget']['YEAR'] = 'anno';
$lang['it_IT']['BlogEntry']['AU'] = 'Autore';
$lang['it_IT']['BlogEntry']['BBH'] = 'Aiuto BBCode';
$lang['it_IT']['BlogEntry']['CN'] = 'Contenuto';
$lang['it_IT']['BlogEntry']['DT'] = 'Data';
$lang['it_IT']['BlogEntry']['PLURALNAME'] = 'Registrazioni blog';
$lang['it_IT']['BlogEntry']['SINGULARNAME'] = 'Registrazione blog';
$lang['it_IT']['BlogEntry.ss']['COMMENTS'] = 'Commenti';
$lang['it_IT']['BlogEntry.ss']['EDITTHIS'] = 'Modifica questo post';
$lang['it_IT']['BlogEntry.ss']['POSTEDBY'] = 'Inserito da';
$lang['it_IT']['BlogEntry.ss']['POSTEDON'] = 'su';
$lang['it_IT']['BlogEntry.ss']['TAGS'] = 'Etichette:';
$lang['it_IT']['BlogEntry.ss']['UNPUBLISHTHIS'] = 'Non pubblicare questo post';
$lang['it_IT']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'Visualizza tutti i post con etichetta';
$lang['it_IT']['BlogEntry']['TS'] = 'Etichette (separate da virgola)';
$lang['it_IT']['BlogHolder']['HAVENTPERM'] = 'Scrivere nel blog è un\'attività dell\'amministratore. Accedi come amministratore.';
$lang['it_IT']['BlogHolder']['PLURALNAME'] = 'Proprietari blog';
$lang['it_IT']['BlogHolder']['POST'] = 'Salva il post nel blog';
$lang['it_IT']['BlogHolder']['RSSFEED'] = 'RSS feed di questo blog';
$lang['it_IT']['BlogHolder']['SINGULARNAME'] = 'Proprietario blog';
$lang['it_IT']['BlogHolder']['SJ'] = 'Soggetto';
$lang['it_IT']['BlogHolder']['SPUC'] = 'Per favore separa le etichette usando virgole.';
$lang['it_IT']['BlogHolder.ss']['NOENTRIES'] = 'Non ci sono voci nel blog';
$lang['it_IT']['BlogHolder.ss']['VIEWINGTAGGED'] = 'Visualizza voci con etichetta';
$lang['it_IT']['BlogHolder']['SUCCONTENT'] = 'Congratulazioni, il blog SilverStripe è stato installato correttamente. Questo messaggio del blog può essere eliminato. Puoi configurare l\'aspetto del tuo blog (come la visualizzazione dei widgets nella sidebar) in [url=admin] del CMS[/url]';
$lang['it_IT']['BlogHolder']['SUCTAGS'] = 'silverstripe, blog';
$lang['it_IT']['BlogHolder']['SUCTITLE'] = 'Modulo blog SilverStripe installato correttamente';
$lang['it_IT']['BlogHolder']['TE'] = 'Ad esempio: sport, personale, fantascienza';
$lang['it_IT']['BlogManagementWidget']['COMADM'] = 'Amministrazione commenti';
$lang['it_IT']['BlogManagementWidget']['PLURALNAME'] = 'Widget di amministrazione blog';
$lang['it_IT']['BlogManagementWidget']['SINGULARNAME'] = 'Widget di amministrazione blog';
$lang['it_IT']['BlogManagementWidget.ss']['LOGOUT'] = 'Esci';
$lang['it_IT']['BlogManagementWidget.ss']['POSTNEW'] = 'Inserisci un nuovo post';
$lang['it_IT']['BlogManagementWidget']['UNM1'] = 'Hai 1 commento da moderare';
$lang['it_IT']['BlogManagementWidget']['UNMM'] = 'Hai %i commenti non approvati';
$lang['it_IT']['BlogSummary.ss']['COMMENTS'] = 'Commenti';
$lang['it_IT']['BlogSummary.ss']['POSTEDBY'] = 'Inserito da';
$lang['it_IT']['BlogSummary.ss']['POSTEDON'] = 'su';
$lang['it_IT']['BlogSummary.ss']['VIEWFULL'] = 'Visualizza post intitolato - ';
$lang['it_IT']['BlogTree']['PLURALNAME'] = 'Alberi dei blog';
$lang['it_IT']['BlogTree']['SINGULARNAME'] = 'Albero del blog';
$lang['it_IT']['RSSWidget']['CT'] = 'Titolo del feed';
$lang['it_IT']['RSSWidget']['NTS'] = 'Numero di argomenti da visualizzare';
$lang['it_IT']['RSSWidget']['PLURALNAME'] = 'Widget RSS';
$lang['it_IT']['RSSWidget']['SINGULARNAME'] = 'Widget RSS';
$lang['it_IT']['RSSWidget']['URL'] = 'URL per il Feed RSS';
$lang['it_IT']['SubscribeRSSWidget']['PLURALNAME'] = 'Sottoscrivi Widget RSS';
$lang['it_IT']['SubscribeRSSWidget']['SINGULARNAME'] = 'Sottoscrivi Widget RSS';
$lang['it_IT']['SubscribeRSSWidget.ss']['SUBSCRIBETEXT'] = 'Sottoscrivi';
$lang['it_IT']['SubscribeRSSWidget.ss']['SUBSCRIBETITLE'] = 'Sottoscrivi questo blog via RSS';
$lang['it_IT']['TagCloudWidget']['LIMIT'] = 'Limitare il numero dei tag a';
$lang['it_IT']['TagCloudWidget']['PLURALNAME'] = 'Widget Nuvola di Tag';
$lang['it_IT']['TagCloudWidget']['SBAL'] = 'alfabeto';
$lang['it_IT']['TagCloudWidget']['SBFREQ'] = 'frequenza';
$lang['it_IT']['TagCloudWidget']['SINGULARNAME'] = 'Widget Nuvola di Tag';
$lang['it_IT']['TagCloudWidget']['SORTBY'] = 'Ordina per';
$lang['it_IT']['TagCloudWidget']['TILE'] = 'Titolo';
$lang['it_IT']['TrackBackPing']['PLURALNAME'] = 'Rileva Ping';
$lang['it_IT']['TrackBackPing']['SINGULARNAME'] = 'Rileva Ping';

?>