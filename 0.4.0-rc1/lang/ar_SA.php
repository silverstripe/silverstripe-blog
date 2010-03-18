<?php

/**
 * Arabic (Saudi Arabia) language pack
 * @package blog
 * @subpackage i18n
 */

i18n::include_locale_file('blog', 'en_US');

global $lang;

if(array_key_exists('ar_SA', $lang) && is_array($lang['ar_SA'])) {
	$lang['ar_SA'] = array_merge($lang['en_US'], $lang['ar_SA']);
} else {
	$lang['ar_SA'] = $lang['en_US'];
}

$lang['ar_SA']['ArchiveWidget']['DispBY'] = 'استعراض بواسطة';
$lang['ar_SA']['ArchiveWidget']['MONTH'] = 'شهر';
$lang['ar_SA']['ArchiveWidget']['PLURALNAME'] = 'مربعات الأرشيف';
$lang['ar_SA']['ArchiveWidget']['SINGULARNAME'] = 'مربع الأرشيف';
$lang['ar_SA']['ArchiveWidget']['YEAR'] = 'سنة';
$lang['ar_SA']['BlogEntry']['AU'] = 'الكاتب';
$lang['ar_SA']['BlogEntry']['BBH'] = 'مساعدة BBCode';
$lang['ar_SA']['BlogEntry']['CN'] = 'المحتوى';
$lang['ar_SA']['BlogEntry']['DT'] = 'تاريخ';
$lang['ar_SA']['BlogEntry']['PLURALNAME'] = 'تدوينات المدونة';
$lang['ar_SA']['BlogEntry']['SINGULARNAME'] = 'تدوينة المدونة';
$lang['ar_SA']['BlogEntry.ss']['COMMENTS'] = 'التعليقات';
$lang['ar_SA']['BlogEntry.ss']['EDITTHIS'] = 'تحرير التدوينة';
$lang['ar_SA']['BlogEntry.ss']['POSTEDBY'] = 'نشرت بواسطة';
$lang['ar_SA']['BlogEntry.ss']['POSTEDON'] = 'في';
$lang['ar_SA']['BlogEntry.ss']['TAGS'] = 'الوسوم:';
$lang['ar_SA']['BlogEntry.ss']['UNPUBLISHTHIS'] = 'عدم نشر التدوينة';
$lang['ar_SA']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'عرض جميع التدوينات';
$lang['ar_SA']['BlogEntry']['TS'] = 'وسوم (فاصلة,بين,الوسوم)';
$lang['ar_SA']['BlogHolder']['HAVENTPERM'] = 'تدوين المدونات يعتبر مهمة إدارية. فضلاً قم بتسجيل الدخول';
$lang['ar_SA']['BlogHolder']['PLURALNAME'] = 'حاويات المدونة';
$lang['ar_SA']['BlogHolder']['POST'] = 'Post blog entry';
$lang['ar_SA']['BlogHolder']['RSSFEED'] = 'RSS لهذه المدونة';
$lang['ar_SA']['BlogHolder']['SINGULARNAME'] = 'حاوية المدونة';
$lang['ar_SA']['BlogHolder']['SJ'] = 'الموضوع';
$lang['ar_SA']['BlogHolder']['SPUC'] = 'فضلاً افصل بين الوسوم بفاصلة';
$lang['ar_SA']['BlogHolder.ss']['NOENTRIES'] = 'لا يوجد مدخلات';
$lang['ar_SA']['BlogHolder.ss']['VIEWINGTAGGED'] = 'عرض المخلات الموسومة بـ';
$lang['ar_SA']['BlogHolder']['SUCCONTENT'] = 'مبروك, تم تركيب Silverstripe blog بنجاح. هذه المدونة يمكن حذفها بأمان.يمكن تعديل المدونة عبر عبر رابط [url=admin]إدارة المحتوى[/url]';
$lang['ar_SA']['BlogHolder']['SUCTAGS'] = 'َsilverstripe , blog';
$lang['ar_SA']['BlogHolder']['SUCTITLE'] = 'تم تركيب SilverStripe Blog بنجاح';
$lang['ar_SA']['BlogHolder']['TE'] = 'مثال:رياضة,شخصية,علمية';
$lang['ar_SA']['BlogManagementWidget']['COMADM'] = 'إدارة التعليقات';
$lang['ar_SA']['BlogManagementWidget']['PLURALNAME'] = 'مربعات إدارة المدونة';
$lang['ar_SA']['BlogManagementWidget']['SINGULARNAME'] = 'مربع إدارة المدونة';
$lang['ar_SA']['BlogManagementWidget.ss']['LOGOUT'] = 'خروج';
$lang['ar_SA']['BlogManagementWidget.ss']['POSTNEW'] = 'نشر تدوينة جديدة';
$lang['ar_SA']['BlogManagementWidget']['UNM1'] = 'يوجد تعليق واحد لا يحتاج إلى موافقة';
$lang['ar_SA']['BlogManagementWidget']['UNMM'] = 'يوجد %i تعليقات لا تحتاج إلى موافقة';
$lang['ar_SA']['BlogSummary.ss']['COMMENTS'] = 'التعليقات';
$lang['ar_SA']['BlogSummary.ss']['POSTEDBY'] = 'بواسطة';
$lang['ar_SA']['BlogSummary.ss']['POSTEDON'] = 'في';
$lang['ar_SA']['BlogSummary.ss']['VIEWFULL'] = 'عرض كامل التدوينة';
$lang['ar_SA']['RSSWidget']['CT'] = 'العنوان المخصص للخلاصة';
$lang['ar_SA']['RSSWidget']['NTS'] = 'عدد العناصر لعرضها';
$lang['ar_SA']['RSSWidget']['PLURALNAME'] = 'مربعات الخلاصات RSS';
$lang['ar_SA']['RSSWidget']['SINGULARNAME'] = 'مربع الخلاصات RSS';
$lang['ar_SA']['RSSWidget']['URL'] = 'رابط الخلاصة';
$lang['ar_SA']['SubscribeRSSWidget']['PLURALNAME'] = 'مربعات الاشتراك في الخلاصات RSS';
$lang['ar_SA']['SubscribeRSSWidget']['SINGULARNAME'] = 'مربع الاشتراك في الخلاصات RSS';
$lang['ar_SA']['SubscribeRSSWidget.ss']['SUBSCRIBETEXT'] = 'اشتراك';
$lang['ar_SA']['SubscribeRSSWidget.ss']['SUBSCRIBETITLE'] = 'الاشتراك في المدونة عن طريق الخلاصات RSS';
$lang['ar_SA']['TagCloudWidget']['LIMIT'] = 'العدد المحدد للوسوم';
$lang['ar_SA']['TagCloudWidget']['PLURALNAME'] = 'مربعات الوسوم السحابية';
$lang['ar_SA']['TagCloudWidget']['SBAL'] = 'هجائي';
$lang['ar_SA']['TagCloudWidget']['SBFREQ'] = 'تكرار';
$lang['ar_SA']['TagCloudWidget']['SINGULARNAME'] = 'مربع الوسوم السحابيةَ';
$lang['ar_SA']['TagCloudWidget']['SORTBY'] = 'ترتيب';
$lang['ar_SA']['TagCloudWidget']['TILE'] = 'العنوان';
$lang['ar_SA']['TrackBackPing']['PLURALNAME'] = 'تنبيهات التعقيبات';
$lang['ar_SA']['TrackBackPing']['SINGULARNAME'] = 'تنبيه التعقيبات';

?>