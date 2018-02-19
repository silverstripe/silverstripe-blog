<?php

namespace SilverStripe\Blog\Model;

use PageController;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPResponse_Exception;
use SilverStripe\Control\RSS\RSSFeed;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Security\Member;
use SilverStripe\View\Parsers\URLSegmentFilter;

class BlogController extends PageController
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'archive',
        'tag',
        'category',
        'rss',
        'profile'
    ];

    /**
     * @var array
     */
    private static $url_handlers = [
        'tag/$Tag!/$Rss' => 'tag',
        'category/$Category!/$Rss' => 'category',
        'archive/$Year!/$Month/$Day' => 'archive',
        'profile/$URLSegment!' => 'profile'
    ];

    /**
     * @var array
     */
    private static $casting = [
        'MetaTitle' => 'Text',
        'FilterDescription' => 'Text'
    ];

    /**
     * If enabled, blog author profiles will be turned off for this site
     *
     * @config
     * @var bool
     */
    private static $disable_profiles = false;

    /**
     * The current Blog Post DataList query.
     *
     * @var DataList
     */
    protected $blogPosts;

    /**
     * @return string
     */
    public function index()
    {
        /**
         * @var Blog $dataRecord
         */
        $dataRecord = $this->dataRecord;

        $this->blogPosts = $dataRecord->getBlogPosts();

        return $this->render();
    }

    /**
     * Renders a Blog Member's profile.
     *
     * @throws HTTPResponse_Exception
     *
     * @return string
     */
    public function profile()
    {
        if ($this->config()->get('disable_profiles')) {
            $this->httpError(404, 'Not Found');
        }

        $profile = $this->getCurrentProfile();

        if (!$profile) {
            return $this->httpError(404, 'Not Found');
        }

        $this->blogPosts = $this->getCurrentProfilePosts();

        return $this->render();
    }

    /**
     * Get the Member associated with the current URL segment.
     *
     * @return null|Member
     */
    public function getCurrentProfile()
    {
        $urlSegment = $this->request->param('URLSegment');

        if ($urlSegment) {
            $filter = URLSegmentFilter::create();

            return Member::get()
                ->filter('URLSegment', $filter->filter($urlSegment))
                ->first();
        }

        return null;
    }

    /**
     * Get posts related to the current Member profile.
     *
     * @return null|DataList
     */
    public function getCurrentProfilePosts()
    {
        $profile = $this->getCurrentProfile();

        if ($profile) {
            return $profile->BlogPosts()->filter('ParentID', $this->ID);
        }

        return null;
    }

    /**
     * Renders an archive for a specified date. This can be by year or year/month.
     *
     * @return null|string
     */
    public function archive()
    {
        /**
         * @var Blog $dataRecord
         */
        $dataRecord = $this->dataRecord;

        $year = $this->getArchiveYear();
        $month = $this->getArchiveMonth();
        $day = $this->getArchiveDay();

        if ($this->request->param('Month') && !$month) {
            $this->httpError(404, 'Not Found');
        }

        if ($month && $this->request->param('Day') && !$day) {
            $this->httpError(404, 'Not Found');
        }

        if ($year) {
            $this->blogPosts = $dataRecord->getArchivedBlogPosts($year, $month, $day);

            return $this->render();
        }

        $this->httpError(404, 'Not Found');

        return null;
    }

    /**
     * Fetches the archive year from the url.
     *
     * @return int
     */
    public function getArchiveYear()
    {
        if ($this->request->param('Year')) {
            if (preg_match('/^[0-9]{4}$/', $year = $this->request->param('Year'))) {
                return (int) $year;
            }
        } elseif ($this->request->param('Action') == 'archive') {
            return DBDatetime::now()->Year();
        }

        return null;
    }

    /**
     * Fetches the archive money from the url.
     *
     * @return null|int
     */
    public function getArchiveMonth()
    {
        $month = $this->request->param('Month');

        if (preg_match('/^[0-9]{1,2}$/', $month)) {
            if ($month > 0 && $month < 13) {
                if (checkdate($month, 01, $this->getArchiveYear())) {
                    return (int) $month;
                }
            }
        }

        return null;
    }

    /**
     * Fetches the archive day from the url.
     *
     * @return null|int
     */
    public function getArchiveDay()
    {
        $day = $this->request->param('Day');

        if (preg_match('/^[0-9]{1,2}$/', $day)) {
            if (checkdate($this->getArchiveMonth(), $day, $this->getArchiveYear())) {
                return (int) $day;
            }
        }

        return null;
    }

    /**
     * Renders the blog posts for a given tag.
     *
     * @return null|string
     */
    public function tag()
    {
        $tag = $this->getCurrentTag();

        if ($tag) {
            $this->blogPosts = $tag->BlogPosts();

            if ($this->isRSS()) {
                return $this->rssFeed($this->blogPosts, $tag->getLink());
            } else {
                return $this->render();
            }
        }

        $this->httpError(404, 'Not Found');

        return null;
    }

    /**
     * Tag Getter for use in templates.
     *
     * @return null|BlogTag
     */
    public function getCurrentTag()
    {
        /**
         * @var Blog $dataRecord
         */
        $dataRecord = $this->dataRecord;
        $tag = $this->request->param('Tag');
        if ($tag) {
            $filter = URLSegmentFilter::create();

            return $dataRecord->Tags()
                ->filter('URLSegment', [$tag, $filter->filter($tag)])
                ->first();
        }
        return null;
    }

    /**
     * Renders the blog posts for a given category.
     *
     * @return null|string
     */
    public function category()
    {
        $category = $this->getCurrentCategory();

        if ($category) {
            $this->blogPosts = $category->BlogPosts();

            if ($this->isRSS()) {
                return $this->rssFeed($this->blogPosts, $category->getLink());
            } else {
                return $this->render();
            }
        }

        $this->httpError(404, 'Not Found');

        return null;
    }

    /**
     * Category Getter for use in templates.
     *
     * @return null|BlogCategory
     */
    public function getCurrentCategory()
    {
        /**
         * @var Blog $dataRecord
         */
        $dataRecord = $this->dataRecord;
        $category = $this->request->param('Category');
        if ($category) {
            $filter = URLSegmentFilter::create();

            return $dataRecord->Categories()
                ->filter('URLSegment', [$category, $filter->filter($category)])
                ->first();
        }
        return null;
    }

    /**
     * Get the meta title for the current action.
     *
     * @return string
     */
    public function getMetaTitle()
    {
        $title = $this->data()->getTitle();
        $filter = $this->getFilterDescription();

        if ($filter) {
            $title = sprintf('%s - %s', $title, $filter);
        }

        $this->extend('updateMetaTitle', $title);

        return $title;
    }

    /**
     * Returns a description of the current filter.
     *
     * @return string
     */
    public function getFilterDescription()
    {
        $items = [];

        $list = $this->PaginatedList();
        $currentPage = $list->CurrentPage();

        if ($currentPage > 1) {
            $items[] = _t(
                'SilverStripe\\Blog\\Model\\Blog.FILTERDESCRIPTION_PAGE',
                'Page {page}',
                null,
                [
                    'page' => $currentPage
                ]
            );
        }

        if ($author = $this->getCurrentProfile()) {
            $items[] = _t(
                'SilverStripe\\Blog\\Model\\Blog.FILTERDESCRIPTION_AUTHOR',
                'By {author}',
                null,
                [
                    'author' => $author->Title
                ]
            );
        }

        if ($tag = $this->getCurrentTag()) {
            $items[] = _t(
                'SilverStripe\\Blog\\Model\\Blog.FILTERDESCRIPTION_TAG',
                'Tagged with {tag}',
                null,
                [
                    'tag' => $tag->Title
                ]
            );
        }

        if ($category = $this->getCurrentCategory()) {
            $items[] = _t(
                'SilverStripe\\Blog\\Model\\Blog.FILTERDESCRIPTION_CATEGORY',
                'In category {category}',
                null,
                [
                    'category' => $category->Title
                ]
            );
        }

        if ($this->owner->getArchiveYear()) {
            if ($this->owner->getArchiveDay()) {
                $date = $this->owner->getArchiveDate()->Nice();
            } elseif ($this->owner->getArchiveMonth()) {
                $date = $this->owner->getArchiveDate()->format('F, Y');
            } else {
                $date = $this->owner->getArchiveDate()->format('Y');
            }

            $items[] = _t(
                'SilverStripe\\Blog\\Model\\Blog.FILTERDESCRIPTION_DATE',
                'In {date}',
                null,
                [
                    'date' => $date,
                ]
            );
        }

        $result = '';

        if ($items) {
            $result = implode(', ', $items);
        }

        $this->extend('updateFilterDescription', $result);

        return $result;
    }

    /**
     * Returns a list of paginated blog posts based on the BlogPost dataList.
     *
     * @return PaginatedList
     */
    public function PaginatedList()
    {
        $allPosts = $this->blogPosts ?: ArrayList::create();
        $posts = PaginatedList::create($allPosts);

        // Set appropriate page size
        if ($this->PostsPerPage > 0) {
            $pageSize = $this->PostsPerPage;
        } elseif ($count = $allPosts->count()) {
            $pageSize = $count;
        } else {
            $pageSize = 99999;
        }
        $posts->setPageLength($pageSize);

        // Set current page
        $start = (int)$this->request->getVar($posts->getPaginationGetVar());
        $posts->setPageStart($start);

        return $posts;
    }


    /**
     * Returns the absolute link to the next page for use in the page meta tags. This helps search engines
     * find the pagination and index all pages properly.
     *
     * @example "<% if $PaginationAbsoluteNextLink %><link rel="next" href="$PaginationAbsoluteNextLink"><% end_if %>"
     *
     * @return string|null
     */
    public function PaginationAbsoluteNextLink()
    {
        $posts = $this->PaginatedList();
        if ($posts->NotLastPage()) {
            return Director::absoluteURL($posts->NextLink());
        }

        return null;
    }

     /**
     * Returns the absolute link to the previous page for use in the page meta tags. This helps search engines
     * find the pagination and index all pages properly.
     *
     * @example "<% if $PaginationAbsolutePrevLink %><link rel="prev" href="$PaginationAbsolutePrevLink"><% end_if %>"
     *
     * @return string|null
     */
    public function PaginationAbsolutePrevLink()
    {
        $posts = $this->PaginatedList();
        if ($posts->NotFirstPage()) {
            return Director::absoluteURL($posts->PrevLink());
        }

        return null;
    }

    /**
     * Displays an RSS feed of blog posts.
     *
     * @return string
     */
    public function rss()
    {
        /**
         * @var Blog $dataRecord
         */
        $dataRecord = $this->dataRecord;

        $this->blogPosts = $dataRecord->getBlogPosts();

        return $this->rssFeed($this->blogPosts, $this->Link());
    }

    /**
     * Returns the current archive date.
     *
     * @return null|DBDatetime
     */
    public function getArchiveDate()
    {
        $year = $this->getArchiveYear();
        $month = $this->getArchiveMonth();
        $day = $this->getArchiveDay();

        if ($year) {
            if ($month) {
                $date = sprintf('%s-%s-01', $year, $month);

                if ($day) {
                    $date = sprintf('%s-%s-%s', $year, $month, $day);
                }
            } else {
                $date = sprintf('%s-01-01', $year);
            }

            $obj = DBDatetime::create('date');
            $obj->setValue($date);
            return $obj;
        }

        return null;
    }

    /**
     * Returns a link to the RSS feed.
     *
     * @return string
     */
    public function getRSSLink()
    {
        return $this->Link('rss');
    }

    /**
     * Displays an RSS feed of the given blog posts.
     *
     * @param DataList $blogPosts
     * @param string $link
     *
     * @return string
     */
    protected function rssFeed($blogPosts, $link)
    {
        $rss = RSSFeed::create($blogPosts, $link, $this->MetaTitle, $this->MetaDescription);

        $this->extend('updateRss', $rss);

        return $rss->outputToBrowser();
    }

    /**
     * Returns true if the $Rss sub-action for categories/tags has been set to "rss"
     *
     * @return bool
     */
    protected function isRSS()
    {
        $rss = $this->request->param('Rss');
        return (is_string($rss) && strcasecmp($rss, 'rss') == 0);
    }
}
