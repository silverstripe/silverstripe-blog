<?php

/**
 * Icelandic (Iceland) language pack
 * @package blog
 * @subpackage i18n
 */

i18n::include_locale_file('blog', 'en_US');

global $lang;

if(array_key_exists('is_IS', $lang) && is_array($lang['is_IS'])) {
	$lang['is_IS'] = array_merge($lang['en_US'], $lang['is_IS']);
} else {
	$lang['is_IS'] = $lang['en_US'];
}

$lang['is_IS']['ArchiveWidget']['DispBY'] = 'Birta sem';
$lang['is_IS']['ArchiveWidget']['MONTH'] = 'mánuður';
$lang['is_IS']['ArchiveWidget']['YEAR'] = 'ár';
$lang['is_IS']['BlogEntry']['AU'] = 'Höfundur';
$lang['is_IS']['BlogEntry']['BBH'] = 'BBCode hjálp';
$lang['is_IS']['BlogEntry']['CN'] = 'Efni';
$lang['is_IS']['BlogEntry']['DT'] = 'Dags';
$lang['is_IS']['BlogEntry']['PLURALNAME'] = 'Blogg færslur';
$lang['is_IS']['BlogEntry']['SINGULARNAME'] = 'Blogg færsla';
$lang['is_IS']['BlogEntry.ss']['COMMENTS'] = 'Athugasemdir';
$lang['is_IS']['BlogEntry.ss']['EDITTHIS'] = 'Breyta þessari færslu';
$lang['is_IS']['BlogEntry.ss']['POSTEDBY'] = 'Skrifað af';
$lang['is_IS']['BlogEntry.ss']['POSTEDON'] = 'á';
$lang['is_IS']['BlogEntry.ss']['TAGS'] = 'Tög:';
$lang['is_IS']['BlogEntry.ss']['UNPUBLISHTHIS'] = 'Hætta birtingu þessarar færslu';
$lang['is_IS']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'Birta allar taggaðar færslur';
$lang['is_IS']['BlogEntry']['TS'] = 'Tög (komma til aðskilnaðar)';
$lang['is_IS']['BlogHolder']['HAVENTPERM'] = 'Birting bloggs er hlutverk stjórnanda. Vinsamlegast innskráðu þig.';
$lang['is_IS']['BlogHolder']['PLURALNAME'] = 'Blogg umhverfi';
$lang['is_IS']['BlogHolder']['POST'] = 'Birta blogg færslu';
$lang['is_IS']['BlogHolder']['RSSFEED'] = 'RSS þjónusta fyrir þetta blogg';
$lang['is_IS']['BlogHolder']['SINGULARNAME'] = 'Blogg umhverfi';
$lang['is_IS']['BlogHolder']['SJ'] = 'Málefni';
$lang['is_IS']['BlogHolder']['SPUC'] = 'Vinsamlegast notaðu kommu til að aðskilja tögin';
$lang['is_IS']['BlogHolder.ss']['NOENTRIES'] = 'Það eru engar blogg færslur';
$lang['is_IS']['BlogHolder.ss']['VIEWINGTAGGED'] = 'Skoða færslur sem eru taggaðar með';
$lang['is_IS']['BlogHolder']['SUCCONTENT'] = 'Til hamingju, uppsetningin á SilverStripe blogg einingunni tókst. 
Þessari blogg færslu er hægt að eyða á örugganhátt. Þú getur stillt útlit blogsins þíns (svo sem widgets) i  [url=admin] kefinu[/url].';
$lang['is_IS']['BlogHolder']['SUCTAGS'] = 'silverstripe, blogg';
$lang['is_IS']['BlogHolder']['SUCTITLE'] = 'Uppsetning á SilverStripe blogg einingunni tókst';
$lang['is_IS']['BlogHolder']['TE'] = 'Til dæmis: íþróttir, persónulegt, vísindasögur';
$lang['is_IS']['BlogManagementWidget']['COMADM'] = 'Athugasemda stjórnun';
$lang['is_IS']['BlogManagementWidget']['PLURALNAME'] = 'Blogg stjórnunar aukahlutur';
$lang['is_IS']['BlogManagementWidget']['SINGULARNAME'] = 'Blogg stjórnunar aukahlutur';
$lang['is_IS']['BlogManagementWidget.ss']['LOGOUT'] = 'Útskrá';
$lang['is_IS']['BlogManagementWidget.ss']['POSTNEW'] = 'Skrifa nýja færslu';
$lang['is_IS']['BlogManagementWidget']['UNM1'] = 'Þú átt 1 óskoðaða athugasemd';
$lang['is_IS']['BlogManagementWidget']['UNMM'] = 'Þú átt %i óskoðaða athugasemd';
$lang['is_IS']['BlogSummary.ss']['COMMENTS'] = 'Athugasemdir';
$lang['is_IS']['BlogSummary.ss']['POSTEDBY'] = 'Skráð af';
$lang['is_IS']['BlogSummary.ss']['POSTEDON'] = 'á';
$lang['is_IS']['BlogSummary.ss']['VIEWFULL'] = 'Skoða alla færslu -';
$lang['is_IS']['RSSWidget']['CT'] = 'Titill fyrir þjónustuna';
$lang['is_IS']['RSSWidget']['NTS'] = 'Fjöldi hluta til að sýna';
$lang['is_IS']['RSSWidget']['PLURALNAME'] = 'RSS aukahlutur';
$lang['is_IS']['RSSWidget']['SINGULARNAME'] = 'RSS aukahlutur';
$lang['is_IS']['RSSWidget']['URL'] = 'Slóð á RSS þjónustuna';
$lang['is_IS']['SubscribeRSSWidget']['PLURALNAME'] = 'Áskriftar RSS aukahlutur';
$lang['is_IS']['SubscribeRSSWidget']['SINGULARNAME'] = 'Áskriftar RSS aukahlutur';
$lang['is_IS']['SubscribeRSSWidget.ss']['SUBSCRIBETEXT'] = 'Gerast áskrifandi';
$lang['is_IS']['SubscribeRSSWidget.ss']['SUBSCRIBETITLE'] = 'Gerast áskrifandi að þessu bloggi í gegnum RSS';
$lang['is_IS']['TagCloudWidget']['LIMIT'] = 'Takmarka fjölda tag';
$lang['is_IS']['TagCloudWidget']['PLURALNAME'] = 'Tag ský aukahlutur';
$lang['is_IS']['TagCloudWidget']['SBAL'] = 'stafróf';
$lang['is_IS']['TagCloudWidget']['SBFREQ'] = 'tíðni';
$lang['is_IS']['TagCloudWidget']['SINGULARNAME'] = 'Tag ský aukahlutur';
$lang['is_IS']['TagCloudWidget']['SORTBY'] = 'Raða eftir';
$lang['is_IS']['TagCloudWidget']['TILE'] = 'Titill';

?>