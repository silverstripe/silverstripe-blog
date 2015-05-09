<?php

class BlogMigrationTask extends MigrationTask {
	/**
	 * Should this task be invoked automatically via dev/build?
	 *
	 * @config
	 *
	 * @var bool
	 */
	private static $run_during_dev_build = true;

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		$classes = ClassInfo::implementorsOf('MigratableObject');

		$this->message('Migrating legacy blog records');

		foreach($classes as $class) {
			if(is_subclass_of($class, 'SiteTree')) {
				foreach(array('Stage', 'Live') as $stage) {
					$oldMode = Versioned::get_reading_mode();
					Versioned::reading_stage($stage);
					$this->upClass($class, $stage);
					Versioned::set_reading_mode($oldMode);
				}
			} else {
				$this->upClass($class);
			}
		}
	}

	/**
	 * @param string $text
	 */
	protected function message($text) {
		if(Controller::curr() instanceof DatabaseAdmin) {
			DB::alteration_message($text, 'obsolete');
		} else {
			Debug::message($text);
		}
	}

	/**
	 * Migrate records of a single class
	 *
	 * @param string $class
	 * @param null|string $stage
	 */
	protected function upClass($class, $stage = null) {
		if(!class_exists($class)) {
			return;
		}

		$items = $class::get();

		if($count = $items->count()) {
			$this->message(
				sprintf(
					'Migrating %s legacy %s records in stage %s.',
					$count,
					$class,
					$stage
				)
			);

			foreach($items as $item) {
				$cancel = $item->extend('onBeforeUp');

				if($cancel && min($cancel) === false) {
					continue;
				}

				/**
				 * @var MigratableObject $item
				 */
				$item->up();

				$item->extend('onAfterUp');
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		$this->message('BlogMigrationTask::down() not implemented');
	}
}
