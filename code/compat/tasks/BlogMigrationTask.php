<?php

/**
 * Description of BlogMigrationTask
 *
 * @author dmooyman
 */
class BlogMigrationTask extends MigrationTask {

	/**
	 * Should this task be invoked automatically via dev/build?
	 *
	 * @config
	 * @var boolean
	 */
	private static $run_during_dev_build = true;

	protected function message($text) {
		if(Controller::curr() instanceof DatabaseAdmin) {
			DB::alteration_message($text, 'obsolete');
		} else {
			Debug::message($text);
		}
	}

	public function up() {
		$classes = ClassInfo::implementorsOf('MigratableObject');
		$this->message('Migrating legacy blog records');

		foreach($classes as $class) {
			// migrate objects in each stage
			if(is_subclass_of($class, 'SiteTree')) {
				foreach(array('Stage', 'Live') as $stage) {
					$oldMode = Versioned::get_reading_mode();
					Versioned::reading_stage($stage);
					$this->upClass($class, $stage);
					Versioned::set_reading_mode($oldMode);
				}
			} else {
				// Migrate object
				$this->upClass($class);
			}
		}
	}

	public function down() {
		$this->message('BlogMigrationTask::down() not implemented');
	}

	/**
	 * Migrate records of a single class
	 *
	 * @param type $class
	 * @param type $stage
	 */
	protected function upClass($class, $stage = null) {
		if(!class_exists($class)) return;

		// Migrate all records
		$items = $class::get();

		if($count = $items->count()) {
			$stageMessage = " in stage {$stage}";
			$this->message("Migrating {$count} legacy {$class} records{$stageMessage}.");
			foreach($items as $item) {
				// Cancel if onBeforeUp returns false
				$cancel = $item->extend('onBeforeUp');
				if($cancel && min($cancel) === false) continue;
				// Up
				$item->up();
				// Post extensions
				$item->extend('onAfterUp');
			}
		}
	}
}
