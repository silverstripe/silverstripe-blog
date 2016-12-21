<?php

use SilverStripe\Core\ClassInfo;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\DatabaseAdmin;
use SilverStripe\ORM\DB;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\MigrationTask;

class BlogMigrationTask extends MigrationTask
{
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
    public function up()
    {
        $classes = ClassInfo::implementorsOf('MigratableObject');

        $this->message('Migrating legacy blog records');

        foreach ($classes as $class) {
            $this->upClass($class);
        }
    }

    /**
     * @param string $text
     */
    protected function message($text)
    {
        if (Controller::curr() instanceof DatabaseAdmin) {
            DB::alteration_message($text, 'obsolete');
        } else {
            echo $text . "<br/>";
        }
    }

    /**
     * Migrate records of a single class
     *
     * @param string $class
     * @param null|string $stage
     */
    protected function upClass($class)
    {
        if (!class_exists($class)) {
            return;
        }

        if (is_subclass_of($class, 'SilverStripe\\CMS\\Model\\SiteTree')) {
            $items = SiteTree::get()->filter('ClassName', $class);
        } else {
            $items = $class::get();
        }

        if ($count = $items->count()) {
            $this->message(
                sprintf(
                    'Migrating %s legacy %s records.',
                    $count,
                    $class
                )
            );

            foreach ($items as $item) {
                $cancel = $item->extend('onBeforeUp');

                if ($cancel && min($cancel) === false) {
                    continue;
                }

                /**
                 * @var MigratableObject $item
                 */
                $result = $item->up();
                $this->message($result);

                $item->extend('onAfterUp');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->message('BlogMigrationTask::down() not implemented');
    }
}
