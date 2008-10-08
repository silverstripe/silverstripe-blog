<?php

/**
 * Turkish (Turkey) language pack
 * @package modules: blog
 * @subpackage i18n
 */

i18n::include_locale_file('modules: blog', 'en_US');

global $lang;

if(array_key_exists('tr_TR', $lang) && is_array($lang['tr_TR'])) {
	$lang['tr_TR'] = array_merge($lang['en_US'], $lang['tr_TR']);
} else {
	$lang['tr_TR'] = $lang['en_US'];
}

$lang['tr_TR']['ArchiveWidget']['DispBY'] = 'Görüntüle';
$lang['tr_TR']['ArchiveWidget']['MONTH'] = 'ay';
$lang['tr_TR']['ArchiveWidget']['YEAR'] = 'yıl';
$lang['tr_TR']['BlogEntry']['AU'] = 'Yazar';
$lang['tr_TR']['BlogEntry']['BBH'] = 'BBCode yardımı';
$lang['tr_TR']['BlogEntry']['CN'] = 'İçerik';
$lang['tr_TR']['BlogEntry']['DT'] = 'Tarih';
$lang['tr_TR']['BlogEntry.ss']['COMMENTS'] = 'Yorumlar';
$lang['tr_TR']['BlogEntry.ss']['EDITTHIS'] = 'Bu girdiyi yeniden düzenle';
$lang['tr_TR']['BlogEntry.ss']['POSTEDBY'] = 'Gönderen ';
$lang['tr_TR']['BlogEntry.ss']['POSTEDON'] = 'üzerinde';
$lang['tr_TR']['BlogEntry.ss']['TAGS'] = 'Etiketler:';
$lang['tr_TR']['BlogEntry.ss']['UNPUBLISHTHIS'] = 'Bu girdiyi yayından kaldır';
$lang['tr_TR']['BlogEntry.ss']['VIEWALLPOSTTAGGED'] = 'Etiketlenen tüm girdileri görüntüle';
$lang['tr_TR']['BlogEntry']['TS'] = 'Etiketler (virgülle ayrılmış)';
$lang['tr_TR']['BlogHolder']['HAVENTPERM'] = 'Sadece yöneticiler blog girebilirler. Lütfen oturum açın.';
$lang['tr_TR']['BlogHolder']['POST'] = 'Blog girdisi gönderin';
$lang['tr_TR']['BlogHolder']['RSSFEED'] = 'Bu blog\'un RSS beslemesi';
$lang['tr_TR']['BlogHolder']['SJ'] = 'Konu';
$lang['tr_TR']['BlogHolder']['SPUC'] = 'Lüfen etiketleri virgülle ayırın.';
$lang['tr_TR']['BlogHolder.ss']['NOENTRIES'] = 'Blog girdileri mevcut değil';
$lang['tr_TR']['BlogHolder.ss']['VIEWINGTAGGED'] = 'Girdiler görüntüleniyor, etiket:';
$lang['tr_TR']['BlogHolder']['SUCCONTENT'] = 'Tebrikler, SilverStripe blog modülü başarıyla kuruldu. Bu blog girdisini silebilirsiniz. Ayrıca, isterseniz [url=admin]CMS[/url] içerisinde blog\'unuzun görünümünde degişiklikler yapabilirsiniz.';
$lang['tr_TR']['BlogHolder']['SUCTAGS'] = 'silverstripe, blog';
$lang['tr_TR']['BlogHolder']['SUCTITLE'] = 'SilverStripe blog modülü başarıyla kuruldu';
$lang['tr_TR']['BlogHolder']['TE'] = 'Örneğin: spor, kişisel, bilim kurgu';
$lang['tr_TR']['BlogManagementWidget']['COMADM'] = 'Yorum yönetimi';
$lang['tr_TR']['BlogManagementWidget.ss']['LOGOUT'] = 'Oturumu kapat';
$lang['tr_TR']['BlogManagementWidget.ss']['POSTNEW'] = 'Yeni bir blog girdisi oluştur';
$lang['tr_TR']['BlogManagementWidget']['UNM1'] = '1 adet onay bekleyen yorumunuz var';
$lang['tr_TR']['BlogManagementWidget']['UNMM'] = '%i adet onay bekleyen yorumunuz var';
$lang['tr_TR']['BlogSummary.ss']['COMMENTS'] = 'Yorumlar';
$lang['tr_TR']['BlogSummary.ss']['POSTEDON'] = 'üzerinde';
$lang['tr_TR']['BlogSummary.ss']['VIEWFULL'] = 'Postun tamamını görüntüle -';
$lang['tr_TR']['RSSWidget']['CT'] = 'Besleme için özel başlık';
$lang['tr_TR']['RSSWidget']['NTS'] = 'Görüntülenecek öğe adedi';
$lang['tr_TR']['RSSWidget']['URL'] = 'RSS beslemesi\'nin URL\'i';
$lang['tr_TR']['TagCloudWidget']['LIMIT'] = 'Etiket sayısini sınırla';
$lang['tr_TR']['TagCloudWidget']['SBAL'] = 'alfabe';
$lang['tr_TR']['TagCloudWidget']['SBFREQ'] = 'frekans';
$lang['tr_TR']['TagCloudWidget']['SORTBY'] = 'Sıralama';
$lang['tr_TR']['TagCloudWidget']['TILE'] = 'Başlık';

?>