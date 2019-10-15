<?php

namespace SilverStripe\Blog\Tasks;

use SilverStripe\Blog\Model\BlogCategory;
use SilverStripe\Blog\Model\BlogTag;
use SilverStripe\Control\Director;
use SilverStripe\Core\Convert;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\Queries\SQLSelect;
use SilverStripe\View\HTML;

class FixBlogDuplicatesTask extends BuildTask
{
    private static $segment = 'FixBlogDuplicatesTask';

    protected $title = 'Fix blog duplicate categories / tags';

    protected $description = 'Merge categories and tags with the same title';

    public function run($request)
    {
        $this->dedupe(BlogTag::class, 'BlogPost_Tags', 'BlogTagID');
        $this->dedupe(BlogCategory::class, 'BlogPost_Categories', 'BlogCategoryID');
    }

    /**
     * Log message to console / page
     *
     * @param string $message
     */
    protected function message($message)
    {
        if (Director::is_cli()) {
            echo "{$message}\n";
        } else {
            echo HTML::createTag('p', [], Convert::raw2xml($message));
        }
    }

    /**
     * Progress used for CLI output
     *
     * @var int
     */
    protected $printedDots = 0;

    /**
     * Render progress dots (20 dots per column)
     *
     * @param        $current
     * @param int    $total
     * @param string $character
     */
    protected function progress($current, $total = 0, $character = '.')
    {
        if (!Director::is_cli()) {
            return;
        }
        while ($this->printedDots < $current) {
            echo $character;
            $this->printedDots++;
            if ($this->printedDots % 80 === 0) {
                if ($total) {
                    $len = strlen($total);
                    echo str_pad("{$this->printedDots}/{$total}", $len * 2 + 2, ' ', STR_PAD_LEFT);
                }
                echo "\n";
            }
        }
    }

    /**
     * Deduplicate the given class
     *
     * @param string $class         Class name to dedupe
     * @param string $mappingTable  Table name mapping
     * @param string $relationField Name of foreign key relation field on mapping table
     */
    protected function dedupe($class, $mappingTable, $relationField)
    {
        $this->printedDots = 0;

        // Find all duplicates
        $itemTable = DataObject::getSchema()->tableName($class);
        $duplicates = SQLSelect::create()
            ->setSelect([
                'Title' => '"Title"',
                'Count' => 'COUNT(*)',
                'UseID' => 'MIN("ID")',
            ])
            ->setFrom($itemTable)
            ->setGroupBy('"Title"')
            ->setHaving('"Count" > 1');

        $count = $duplicates->count();

        $this->message("Found {$count} items with duplicates for type {$class}");

        if (!$count) {
            return;
        }

        $done = 0;
        foreach ($duplicates->execute() as $duplicate) {
            $title = $duplicate['Title'];
            $id = $duplicate['UseID'];

            DB::prepared_query(
                <<<SQL
UPDATE "{$mappingTable}"
INNER JOIN "{$itemTable}" ON "{$mappingTable}"."{$relationField}" = "{$itemTable}"."ID"
SET "{$mappingTable}"."{$relationField}" = ? 
WHERE "{$itemTable}"."Title" = ? 
SQL
                ,
                [$id, $title]
            );

            // Delete duplicates
            $duplicateItems = DataObject::get($class)->filter([
                'Title'  => $title,
                'ID:not' => $id,
            ]);
            /** @var DataObject $duplicateItem */
            foreach ($duplicateItems as $duplicateItem) {
                $duplicateItem->delete();
            }

            // Update progress bar
            $done++;
            $this->progress($done, $count);
        }

        $this->progress("");
        $this->progress("Completed cleaning duplicates for {$class}");
    }
}
