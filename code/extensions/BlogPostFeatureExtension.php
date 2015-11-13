<?php

class BlogPostFeatureExtension extends DataExtension {
	/**
	 * @var array
	 */
	private static $db = array(
		'IsFeatured' => 'Boolean',
	);

	/**
	 * @inheritdoc
	 *
	 * @param FieldList
	 */
	public function updateCMSFields(FieldList $fields) {
		$sidebar = $fields->fieldByName('blog-admin-sidebar');

		$sidebar->insertBefore('PublishDate', new CheckboxField('IsFeatured', 'Mark as featured post'));
	}
}
