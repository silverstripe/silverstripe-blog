<?php

/**
 * Dutch (Netherlands) language pack
 * @package modules: blog
 * @subpackage i18n
 */

i18n::include_locale_file('modules: blog', 'en_US');

global $lang;

if(array_key_exists('nl_NL', $lang) && is_array($lang['nl_NL'])) {
	$lang['nl_NL'] = array_merge($lang['en_US'], $lang['nl_NL']);
} else {
	$lang['nl_NL'] = $lang['en_US'];
}

$lang['nl_NL']['ArchiveWidget']['DispBY'] = 'Tonen door';
$lang['nl_NL']['ArchiveWidget']['MONTH'] = 'maand';
$lang['nl_NL']['ArchiveWidget']['YEAR'] = 'jaar';
$lang['nl_NL']['BlogEntry']['AU'] = 'Auteur';
$lang['nl_NL']['BlogEntry']['BBH'] = 'BBCode hulp';
$lang['nl_NL']['BlogEntry']['CN'] = 'Inhoud';
$lang['nl_NL']['BlogEntry']['DT'] = 'Datum';
$lang['nl_NL']['BlogEntry.ss']['COMMENTS'] = 'Reacties';
$lang['nl_NL']['BlogEntry.ss']['EDITTHIS'] = 'Bewerk dit post';
$lang['nl_NL']['BlogEntry.ss']['POSTEDBY'] = 'Auteur';
$lang['nl_NL']['BlogEntry.ss']['POSTEDON'] = 'Aan';
$lang['nl_NL']['BlogEntry.ss']['TAGS'] = 'Tags:';
$lang['nl_NL']['BlogEntry.ss']['UNPUBLISHTHIS'] = 'onpubliceer dit post';
$lang['nl_NL']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'Bekijk alle posten getiteld';
$lang['nl_NL']['BlogEntry']['TS'] = 'Tags (Komma gescheiden)';
$lang['nl_NL']['BlogHolder']['HAVENTPERM'] = 'Het plaatsen van blogartikelen is een beheerder taak. Log aub in.';
$lang['nl_NL']['BlogHolder']['POST'] = 'Blogartikel plaatsen';
$lang['nl_NL']['BlogHolder']['RSSFEED'] = 'RSS-feed van deze blog';
$lang['nl_NL']['BlogHolder']['SJ'] = 'Onderwerp';
$lang['nl_NL']['BlogHolder']['SPUC'] = 'Scheid de tags met behulp van komma\'s.';
$lang['nl_NL']['BlogHolder.ss']['NOENTRIES'] = 'Er zijn geen blog entrees ';
$lang['nl_NL']['BlogHolder.ss']['VIEWINGTAGGED'] = 'U bekijkt entrees tagged met';
$lang['nl_NL']['BlogHolder']['SUCCONTENT'] = 'Gefeliciteerd, de SilverStripe blog module is met succes geïnstalleerd. Dit blogartikel kan veilig worden verwijderd. U kunt aspecten van uw blog (zoals de widgets weergegeven in de zijbalk) in [url=admin]het CMS[/url] veranderen.';
$lang['nl_NL']['BlogHolder']['SUCTAGS'] = 'silverstripe, blog';
$lang['nl_NL']['BlogHolder']['SUCTITLE'] = 'SilverStripe blog module met succes geïnstalleerd';
$lang['nl_NL']['BlogHolder']['TE'] = 'Bijvoorbeeld: sport, persoonlijke, science fiction';
$lang['nl_NL']['BlogManagementWidget']['COMADM'] = 'Opmerking administratie';
$lang['nl_NL']['BlogManagementWidget.ss']['LOGOUT'] = 'Afmelden';
$lang['nl_NL']['BlogManagementWidget.ss']['POSTNEW'] = 'Publiceer een nieuw blog entree';
$lang['nl_NL']['BlogManagementWidget']['UNM1'] = 'U heeft 1 niet gecontroleerde opmerking';
$lang['nl_NL']['BlogManagementWidget']['UNMM'] = 'U heeft %i niet gecontroleerde opmerkingen';
$lang['nl_NL']['BlogSummary.ss']['COMMENTS'] = 'Reacties';
$lang['nl_NL']['BlogSummary.ss']['POSTEDBY'] = 'Geplaatst door';
$lang['nl_NL']['BlogSummary.ss']['POSTEDON'] = 'Aan';
$lang['nl_NL']['BlogSummary.ss']['VIEWFULL'] = 'Bekijk het gehele post getitled';
$lang['nl_NL']['RSSWidget']['CT'] = 'Aangepaste titel voor de RSS-feed';
$lang['nl_NL']['RSSWidget']['NTS'] = 'Aantal objecten tonen';
$lang['nl_NL']['RSSWidget']['URL'] = 'URL van de RSS-feed';
$lang['nl_NL']['TagCloudWidget']['LIMIT'] = 'Beperk aantal tags';
$lang['nl_NL']['TagCloudWidget']['SBAL'] = 'alfabet';
$lang['nl_NL']['TagCloudWidget']['SBFREQ'] = 'frequentie';
$lang['nl_NL']['TagCloudWidget']['SORTBY'] = 'Sorteer bij';
$lang['nl_NL']['TagCloudWidget']['TILE'] = 'Titel';

?>