<?php

/**
 * A blog category for generalising blog posts.
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @method Blog Blog()
 * @method ManyManyList BlogPosts()
 *
 * @property string $URLSegment
 * @property int $BlogID
 */
class BlogCategory extends DataObject implements CategorisationObject {
	/**
	 * @var array
	 */
	private static $db = array(
		'Title' => 'Varchar(255)',
	);

	/**
	 * @var array
	 */
	private static $has_one = array(
		'Blog' => 'Blog',
	);

	/**
	 * @var array
	 */
	private static $belongs_many_many = array(
		'BlogPosts' => 'BlogPost',
	);

	/**
	 * @var array
	 */
	private static $extensions = array(
		'URLSegmentExtension',
	);

	/**
	 * {@inheritdoc}
	 */
	public function getCMSFields() {
		$fields = new FieldList(
			TextField::create('Title', _t('BlogCategory.Title', 'Title'))
		);

		$this->extend('updateCMSFields', $fields);

		return $fields;
	}

	/**
	 * Returns a relative link to this category.
	 *
	 * @return string
	 */
	public function getLink() {
		return Controller::join_links($this->Blog()->Link(), 'category', $this->URLSegment);
	}

	/**
	 * Inherits from the parent blog or can be overwritten using a DataExtension.
	 *
	 * @param null|Member $member
	 *
	 * @return bool
	 */
	public function canView($member = null) {
		$extended = $this->extendedCan(__FUNCTION__, $member);

		if($extended !== null) {
			return $extended;
		}

		return $this->Blog()->canView($member);
	}

	/**
	 * Inherits from the parent blog or can be overwritten using a DataExtension.
	 *
	 * @param null|Member $member
	 *
	 * @return bool
	 */
	public function canCreate($member = null) {
		$extended = $this->extendedCan(__FUNCTION__, $member);

		if($extended !== null) {
			return $extended;
		}

		$permission = Blog::config()->grant_user_permission;

		return Permission::checkMember($member, $permission);
	}

	/**
	 * Inherits from the parent blog or can be overwritten using a DataExtension.
	 *
	 * @param null|Member $member
	 *
	 * @return bool
	 */
	public function canDelete($member = null) {
		$extended = $this->extendedCan(__FUNCTION__, $member);

		if($extended !== null) {
			return $extended;
		}

		return $this->Blog()->canEdit($member);
	}

	/**
	 * Inherits from the parent blog or can be overwritten using a DataExtension.
	 *
	 * @param null|Member $member
	 *
	 * @return bool
	 */
	public function canEdit($member = null) {
		$extended = $this->extendedCan(__FUNCTION__, $member);

		if($extended !== null) {
			return $extended;
		}

		return $this->Blog()->canEdit($member);
	}
}
