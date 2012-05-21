<?php

/**
 * English (United Kingdom) language pack
 * @package blog
 * @subpackage i18n
 */

i18n::include_locale_file('blog', 'en_US');

global $lang;

if(array_key_exists('en_GB', $lang) && is_array($lang['en_GB'])) {
	$lang['en_GB'] = array_merge($lang['en_US'], $lang['en_GB']);
} else {
	$lang['en_GB'] = $lang['en_US'];
}

$lang['en_GB']['ArchiveWidget']['DispBY'] = 'Display by';
$lang['en_GB']['ArchiveWidget']['MONTH'] = 'month';
$lang['en_GB']['ArchiveWidget']['PLURALNAME'] = 'Archive Widgets';
$lang['en_GB']['ArchiveWidget']['SINGULARNAME'] = 'Archive Widget';
$lang['en_GB']['ArchiveWidget']['YEAR'] = 'year';
$lang['en_GB']['BlogEntry']['AU'] = 'Author';
$lang['en_GB']['BlogEntry']['BBH'] = 'BBCode help';
$lang['en_GB']['BlogEntry']['CN'] = 'Content';
$lang['en_GB']['BlogEntry']['DT'] = 'Date';
$lang['en_GB']['BlogEntry']['PLURALNAME'] = 'Blog Entries';
$lang['en_GB']['BlogEntry']['SINGULARNAME'] = 'Blog Entry';
$lang['en_GB']['BlogEntry.ss']['COMMENTS'] = 'Comments';
$lang['en_GB']['BlogEntry.ss']['EDITTHIS'] = 'Edit this post';
$lang['en_GB']['BlogEntry.ss']['POSTEDBY'] = 'Posted by';
$lang['en_GB']['BlogEntry.ss']['POSTEDON'] = 'on';
$lang['en_GB']['BlogEntry.ss']['TAGS'] = 'Tags:';
$lang['en_GB']['BlogEntry.ss']['UNPUBLISHTHIS'] = 'Unpublish this post';
$lang['en_GB']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'View all posts tagged';
$lang['en_GB']['BlogEntry']['TS'] = 'Tags (comma sep.)';
$lang['en_GB']['BlogHolder']['HAVENTPERM'] = 'Posting blogs is an administrator task. Please log in.';
$lang['en_GB']['BlogHolder']['PLURALNAME'] = 'Blog Holders';
$lang['en_GB']['BlogHolder']['POST'] = 'Post blog entry';
$lang['en_GB']['BlogHolder']['RSSFEED'] = 'RSS feed of this blog';
$lang['en_GB']['BlogHolder']['SINGULARNAME'] = 'Blog Holder';
$lang['en_GB']['BlogHolder']['SJ'] = 'Subject';
$lang['en_GB']['BlogHolder']['SPUC'] = 'Please separate tags using commas.';
$lang['en_GB']['BlogHolder.ss']['NOENTRIES'] = 'There are no blog entries';
$lang['en_GB']['BlogHolder.ss']['VIEWINGTAGGED'] = 'Viewing entries tagged with';
$lang['en_GB']['BlogHolder']['SUCCONTENT'] = 'Congratulations, the SilverStripe blog module has been successfully installed. This blog entry can be safely deleted. You can configure aspects of your blog (such as the widgets displayed in the sidebar) in [url=admin]the CMS[/url].';
$lang['en_GB']['BlogHolder']['SUCTAGS'] = 'silverstripe, blog';
$lang['en_GB']['BlogHolder']['SUCTITLE'] = 'SilverStripe blog module successfully installed';
$lang['en_GB']['BlogHolder']['TE'] = 'For example: sport, personal, science fiction';
$lang['en_GB']['BlogManagementWidget']['COMADM'] = 'Comment administration';
$lang['en_GB']['BlogManagementWidget']['PLURALNAME'] = 'Blog Management Widgets';
$lang['en_GB']['BlogManagementWidget']['SINGULARNAME'] = 'Blog Management Widget';
$lang['en_GB']['BlogManagementWidget.ss']['LOGOUT'] = 'Logout';
$lang['en_GB']['BlogManagementWidget.ss']['POSTNEW'] = 'Post a new blog entry';
$lang['en_GB']['BlogManagementWidget']['UNM1'] = 'You have 1 unmoderated comment';
$lang['en_GB']['BlogManagementWidget']['UNMM'] = 'You have %i unmoderated comments';
$lang['en_GB']['BlogSummary.ss']['COMMENTS'] = 'Comments';
$lang['en_GB']['BlogSummary.ss']['POSTEDBY'] = 'Posted by';
$lang['en_GB']['BlogSummary.ss']['POSTEDON'] = 'on';
$lang['en_GB']['BlogSummary.ss']['VIEWFULL'] = 'View full post titled -';
$lang['en_GB']['RSSWidget']['CT'] = 'Custom title for the feed';
$lang['en_GB']['RSSWidget']['NTS'] = 'Number of Items to show';
$lang['en_GB']['RSSWidget']['PLURALNAME'] = 'RSS Widgets';
$lang['en_GB']['RSSWidget']['SINGULARNAME'] = 'RSS Widget';
$lang['en_GB']['RSSWidget']['URL'] = 'URL of RSS Feed';
$lang['en_GB']['SubscribeRSSWidget']['PLURALNAME'] = 'Subscript to RSS Widgets';
$lang['en_GB']['SubscribeRSSWidget']['SINGULARNAME'] = 'Subscript to an RSS Widget';
$lang['en_GB']['SubscribeRSSWidget.ss']['SUBSCRIBETEXT'] = 'Subscribe';
$lang['en_GB']['SubscribeRSSWidget.ss']['SUBSCRIBETITLE'] = 'Subscribe to this blog via RSS';
$lang['en_GB']['TagCloudWidget']['LIMIT'] = 'Limit number of tags';
$lang['en_GB']['TagCloudWidget']['PLURALNAME'] = 'Tag Cloud Widgets';
$lang['en_GB']['TagCloudWidget']['SBAL'] = 'alphabet';
$lang['en_GB']['TagCloudWidget']['SBFREQ'] = 'frequency';
$lang['en_GB']['TagCloudWidget']['SINGULARNAME'] = 'Tag Cloud Widget';
$lang['en_GB']['TagCloudWidget']['SORTBY'] = 'Sort by';
$lang['en_GB']['TagCloudWidget']['TILE'] = 'Title';
$lang['en_GB']['TrackBackPing']['PLURALNAME'] = 'Track Back Pings';
$lang['en_GB']['TrackBackPing']['SINGULARNAME'] = 'Track Back Ping';

?>