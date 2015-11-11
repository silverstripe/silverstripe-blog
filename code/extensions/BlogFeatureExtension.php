<?php

class BlogFeatureExtension extends DataExtension {
	/**
	 * @config
	 *
	 * @var int
	 */
	private static $excluded_feature_posts = 1;

	/**
	 * @return DataList
	 */
	public function getFeaturedBlogPosts() {
		return BlogPost::get()
			->filter('ParentID', $this->owner->ID)
			->filter('IsFeatured', true);
	}

	/**
	 * @param DataList $posts
	 * @param null|string $context Context for these blog posts (e.g 'rss')
	 *
	 * @return DataList
	 */
	public function updateGetBlogPosts(DataList &$posts, $context = null) {
		if($context === 'rss') {
			return;
		}

		$excluded = (int) Config::inst()->get('BlogFeatureExtension', 'excluded_feature_posts');

		if($excluded > 0) {
			$taken = $this->getFeaturedBlogPosts()->limit($excluded);

			if ($taken->count()) {
				$posts = $posts->exclude(array('ID' => $taken->getIDList()));
			}
		}
	}
}
