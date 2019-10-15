<?php

namespace SilverStripe\Blog\Model;

use PageController;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPResponse_Exception;
use SilverStripe\Control\RSS\RSSFeed;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\ORM\SS_List;
use SilverStripe\Security\Member;
use SilverStripe\View\Parsers\URLSegmentFilter;

/**
 * @method Blog data()
 */
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
        'tag/$Tag!/$Rss'             => 'tag',
        'category/$Category!/$Rss'   => 'category',
        'archive/$Year!/$Month/$Day' => 'archive',
        'profile/$Profile!'          => 'profile'
    ];

    /**
     * @var array
     */
    private static $casting = [
        'MetaTitle'         => 'Text',
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
     * Renders a Blog Member's profile.
     *
     * @throws HTTPResponse_Exception
     * @return $this
     */
    public function profile()
    {
        if ($this->config()->get('disable_profiles')) {
            $this->httpError(404, 'Not Found');
        }

        // Get profile posts
        $posts = $this->getCurrentProfilePosts();
        if (!$posts) {
            $this->httpError(404, 'Not Found');
        }

        $this->setFilteredPosts($posts);
        return $this;
    }

    /**
     * Get the Member associated with the current URL segment.
     *
     * @return null|Member|BlogMemberExtension
     */
    public function getCurrentProfile()
    {
        $segment = $this->getCurrentProfileURLSegment();
        if (!$segment) {
            return null;
        }

        /** @var Member $profile */
        $profile = Member::get()
            ->find('URLSegment', $segment);
        return $profile;
    }

    /**
     * Get URL Segment of current profile
     *
     * @return null|string
     */
    public function getCurrentProfileURLSegment()
    {
        $segment = isset($this->urlParams['Profile'])
            ? $this->urlParams['Profile']
            : null;
        if (!$segment) {
            return null;
        }

        // url encode unless it's multibyte (already pre-encoded in the database)
        // see https://github.com/silverstripe/silverstripe-cms/pull/2384
        return URLSegmentFilter::singleton()->getAllowMultibyte()
            ? $segment
            : rawurlencode($segment);
    }

    /**
     * Get posts related to the current Member profile.
     *
     * @return null|DataList|BlogPost[]
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
     * @return $this
     * @throws HTTPResponse_Exception
     */
    public function archive()
    {
        $year = $this->getArchiveYear();
        $month = $this->getArchiveMonth();
        $day = $this->getArchiveDay();

        // Validate all values
        if ($year === false || $month === false || $day === false) {
            $this->httpError(404, 'Not Found');
        }

        $posts = $this->data()->getArchivedBlogPosts($year, $month, $day);
        $this->setFilteredPosts($posts);
        return $this;
    }

    /**
     * Fetches the archive year from the url.
     *
     * Returns int if valid, current year if not provided, false if invalid value
     *
     * @return int|false
     */
    public function getArchiveYear()
    {
        if (isset($this->urlParams['Year'])
            && preg_match('/^[0-9]{4}$/', $this->urlParams['Year'])
        ) {
            return (int)$this->urlParams['Year'];
        }

        if ($this->urlParams['Action'] === 'archive') {
            return DBDatetime::now()->Year();
        }

        return false;
    }

    /**
     * Fetches the archive money from the url.
     *
     * Returns int if valid, null if not provided, false if invalid value
     *
     * @return null|int|false
     */
    public function getArchiveMonth()
    {
        $month = isset($this->urlParams['Month'])
            ? $this->urlParams['Month']
            : null;

        if (preg_match('/^[0-9]{1,2}$/', $month)
            && $month > 0
            && $month < 13
        ) {
            return (int)$month;
        }

        return false;
    }

    /**
     * Fetches the archive day from the url.
     *
     * Returns int if valid, null if not provided, false if invalid value
     *
     * @return null|int|false
     */
    public function getArchiveDay()
    {
        $day = isset($this->urlParams['Day'])
            ? $this->urlParams['Day']
            : null;

        // Cannot calculate day without month and year
        $month = $this->getArchiveMonth();
        $year = $this->getArchiveYear();
        if (!$month || !$year) {
            return false;
        }

        if (preg_match('/^[0-9]{1,2}$/', $day) && checkdate($month, $day, $year)) {
            return (int)$day;
        }

        return false;
    }

    /**
     * Renders the blog posts for a given tag.
     *
     * @return DBHTMLText|$this
     * @throws HTTPResponse_Exception
     */
    public function tag()
    {
        // Ensure tag exists
        $tag = $this->getCurrentTag();
        if (!$tag) {
            $this->httpError(404, 'Not Found');
        }

        // Get posts with this tag
        $posts = $this
            ->data()
            ->getBlogPosts()
            ->filter(['Tags.URLSegment' => $tag->URLSegment]); // Soft duplicate handling

        $this->setFilteredPosts($posts);

        // Render as RSS if provided
        if ($this->isRSS()) {
            return $this->rssFeed($posts, $tag->getLink());
        }

        return $this;
    }

    /**
     * Get BlogTag assigned to current filter
     *
     * @return null|BlogTag
     */
    public function getCurrentTag()
    {
        $segment = $this->getCurrentTagURLSegment();
        if (!$segment) {
            return null;
        }

        /** @var BlogTag $tag */
        $tag = $this
            ->data()
            ->Tags(false)// Show "no results" instead of "404"
            ->find('URLSegment', $segment);
        return $tag;
    }

    /**
     * Get URLSegment of selected category (not: URLEncoded based on multibyte)
     *
     * @return string|null
     */
    public function getCurrentTagURLSegment()
    {
        $segment = isset($this->urlParams['Tag'])
            ? $this->urlParams['Tag']
            : null;

        // url encode unless it's multibyte (already pre-encoded in the database)
        // see https://github.com/silverstripe/silverstripe-cms/pull/2384
        return URLSegmentFilter::singleton()->getAllowMultibyte()
            ? $segment
            : rawurlencode($segment);
    }

    /**
     * Renders the blog posts for a given category.
     *
     * @return DBHTMLText|$this
     * @throws HTTPResponse_Exception
     */
    public function category()
    {
        $category = $this->getCurrentCategory();

        if (!$category) {
            $this->httpError(404, 'Not Found');
        }

        // Get posts with this category
        $posts = $this
            ->data()
            ->getBlogPosts()
            ->filter(['Categories.URLSegment' => $category->URLSegment]); // Soft duplicate handling
        $this->setFilteredPosts($posts);

        if ($this->isRSS()) {
            return $this->rssFeed($posts, $category->getLink());
        }
        return $this;
    }

    /**
     * Category Getter for use in templates.
     *
     * @return null|BlogCategory
     */
    public function getCurrentCategory()
    {
        $segment = $this->getCurrentCategoryURLSegment();
        if (!$segment) {
            return null;
        }

        /** @var BlogCategory $category */
        $category = $this
            ->data()
            ->Categories(false)// Show "no results" instead of "404"
            ->find('URLSegment', $segment);
        return $category;
    }

    /**
     * Get URLSegment of selected category
     *
     * @return string|null
     */
    public function getCurrentCategoryURLSegment()
    {
        $segment = isset($this->urlParams['Category'])
            ? $this->urlParams['Category']
            : null;

        // url encode unless it's multibyte (already pre-encoded in the database)
        // see https://github.com/silverstripe/silverstripe-cms/pull/2384
        return URLSegmentFilter::singleton()->getAllowMultibyte()
            ? $segment
            : rawurlencode($segment);
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

        if ($this->getArchiveYear()) {
            if ($this->getArchiveDay()) {
                $date = $this->getArchiveDate()->Nice();
            } elseif ($this->getArchiveMonth()) {
                $date = $this->getArchiveDate()->format('MMMM, y');
            } else {
                $date = $this->getArchiveDate()->format('y');
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
     * Get filtered blog posts
     *
     * @return DataList|BlogPost[]
     */
    public function getFilteredPosts()
    {
        return $this->blogPosts ?: $this->data()->getBlogPosts();
    }

    /**
     * Set filtered posts
     *
     * @param SS_List|BlogPost[] $posts
     * @return $this
     */
    public function setFilteredPosts($posts)
    {
        $this->blogPosts = $posts;
        return $this;
    }

    /**
     * Returns a list of paginated blog posts based on the BlogPost dataList.
     *
     * @return PaginatedList
     */
    public function PaginatedList()
    {
        $allPosts = $this->getFilteredPosts();
        $posts = PaginatedList::create($allPosts);

        // Set appropriate page size
        if ($this->data()->PostsPerPage > 0) {
            $pageSize = $this->data()->PostsPerPage;
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
        return $this->rssFeed($this->getFilteredPosts(), $this->Link());
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
     * @param string   $link
     *
     * @return DBHTMLText
     */
    protected function rssFeed($blogPosts, $link)
    {
        $rss = RSSFeed::create(
            $blogPosts,
            $link,
            $this->getMetaTitle(),
            $this->data()->MetaDescription
        );

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
        return isset($this->urlParams['RSS']) && strcasecmp($this->urlParams['RSS'], 'rss') == 0;
    }
}
