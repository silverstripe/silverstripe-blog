<?php
/*
 * BlogMigrationTask
 *
 * Migrates Blog 1.0 to the 2.0 data structure and maps existing content.
 *
 * Includes extension points as it is a common pattern to have subclassed BlogEntry, BlogHolder etc
 * and customised the data model. You can add an Extension to BlogMigrationTask to run custom
 * migration before or after the main migration or after the cleanup operation.
 *
 * @package silverstripe
 * @subpackage blog
 *
 * @method none run() Runs the migration, must pass ?migration=1 to trigger.
 * @method int migrateTags() Iterates through all comma-separated tags from blog 1.0 and creates BlogTag objects.
 * @method array tagNames() Splits passed in comma separated string of tags
 * @method none migrateWidgets() Migrates legacy Widgets if Widget module is installed.
 * @method boolean cleanUp() Clears out blog 1.0 obsolete tables if you pass ?cleanup=1 to the task.
 */

class BlogMigrationTask extends BuildTask
{
	/**
	 * @var string $title Shown in the overview on the {@link TaskRunner}
	 * HTML or CLI interface. Should be short and concise, no HTML allowed.
	 */
	protected $title = 'Blog 2.0 migration';

	/**
	 * @var string $description Describe the implications the task has,
	 * and the changes it makes. Accepts HTML formatting.
	 */
	protected $description = 'Migrates blog 1.0 to 2.0 data structure.';

	/*
	 * Run the migrate
	 *
	 * @param object $request
	 */
	public function run($request) {
		// end of line output depending on CLI or browser run.
		$this->eol = Director::is_cli() ? PHP_EOL : "<br>";

		//PRE-FLIGHT CHECK
		// Ensure a dev build has been run by check for some expected tables.
		try {
			DB::query('SELECT "ID" FROM "Blog"');
			DB::query('SELECT "ID" FROM "BlogPost"');
			echo "Blog and BlogPost tables exist, you are good to migrate" . $this->eol;
		} catch (Exception $e) {
			echo 'Ensure you have run a <a href="/dev/build" target="_blank">dev/build</a>' . $this->eol;
		}

		//THE MIGRATION
		if($request->getVar('migration') != 1){
			echo $this->eol . 'Ready to run the migration? ' . $this->eol . $this->eol .
				'<a href="/dev/tasks/'.__CLASS__.'/?migration=1">Run migration only</a>' . $this->eol . $this->eol .
				'<a href="/dev/tasks/'.__CLASS__.'/?migration=1&cleanup=1">Run migration and clean old blog tables</a>' . $this->eol;
			exit;
		}

		$this->extend('onBeforeBlogMigration',$request, $this->eol);

		//BlogPost Migration
		//Migrate BlogEntry to BlogPost include _Live and _versions
		try {
			DB::query('
              INSERT INTO "BlogPost" ("ID", "PublishDate", "AuthorNames")
              (
              	SELECT "ID", "Date", "Author"
              	FROM "BlogEntry"
              )
            ');
			echo "Migrated BlogEntry to BlogPost" . $this->eol;
		} catch (Exception $e) {
			echo "BlogEntry to BlogPost migration already run, moving along..." . $this->eol;
		}
		//BlogPost_Live
		try {
			DB::query('
              INSERT INTO "BlogPost_Live" ("ID", "PublishDate", "AuthorNames")
              (
              	SELECT "ID", "Date", "Author"
              	FROM "BlogEntry_Live"
              )
            ');
			echo "Migrated BlogEntry_Live to BlogPost_Live" . $this->eol;
		} catch (Exception $e) {
			echo "BlogEntry_Live to BlogPost_Live migration already run, moving along..." . $this->eol;
		}
		//BlogPost_version
		try {
			DB::query('
              INSERT INTO "BlogPost_versions" ("ID", "RecordID", "Version", "PublishDate", "AuthorNames")
              (
              	SELECT "ID", "RecordID", "Version", "Date", "Author"
              	FROM "BlogEntry_versions"
              )
            ');
			echo "Migrated BlogEntry_versions to BlogPost_versions" . $this->eol;
		} catch (Exception $e) {
			echo "BlogEntry_versions to BlogPost_versions migration already run, moving along..." . $this->eol;
		}
		//SiteTree ClassName BlogEntry to BlogPost
		try {
			DB::query('UPDATE "SiteTree" SET "ClassName" = \'BlogPost\' WHERE "ClassName" = \'BlogEntry\'');
			DB::query('UPDATE "SiteTree_Live" SET "ClassName" = \'BlogPost\' WHERE "ClassName" = \'BlogEntry\'');
			DB::query('UPDATE "SiteTree_versions" SET "ClassName" = \'BlogPost\' WHERE "ClassName" = \'BlogEntry\'');
			echo "Updated ClassName reference to BlogPost" . $this->eol;
		} catch (Exception $e) {
			echo $e;
			echo "SiteTree BlogPost ClassName migration already run, moving along..." . $this->eol;
		}

		//Migrate BlogHolder to Blog
		//Migrate BlogHolder to Blog include _Live and _versions
		try {
			DB::query('
              INSERT INTO "Blog" ("ID", "PostsPerPage")
              (
              	SELECT "BlogHolder"."ID", 10
              	FROM "BlogHolder"
              )
            ');
			echo "Migrated BlogHolder to Blog" . $this->eol;
		} catch (Exception $e) {
			echo "BlogHolder to Blog migration already run, moving along..." . $this->eol;
		}
		//Blog_Live
		try {
			DB::query('
              INSERT INTO "Blog_Live" ("ID", "PostsPerPage")
              (
              	SELECT "BlogHolder_Live"."ID", 10
              	FROM "BlogHolder_Live"
              )
            ');
			echo "Migrated_Live BlogHolder to Blog_Live" . $this->eol;
		} catch (Exception $e) {
			echo "BlogHolder to Blog migration already run, moving along..." . $this->eol;
		}
		//Blog_version
		try {
			DB::query('
              INSERT INTO "Blog_versions" ("ID", "RecordID", "Version", "PostsPerPage")
              (
              	SELECT "BlogHolder_versions"."ID", "BlogHolder_versions"."RecordID", "BlogHolder_versions"."Version", 10
              	FROM "BlogHolder_versions"
              )
            ');
			echo "Migrated BlogHolder versions to Blog_versions" . $this->eol;
		} catch (Exception $e) {
			echo "BlogHolder to Blog migration already run, moving along..." . $this->eol;
		}
		//SiteTree ClassName BlogEntry to BlogPost
		try {
			DB::query('UPDATE "SiteTree" SET "ClassName" = \'Blog\' WHERE "ClassName" = \'BlogHolder\'');
			DB::query('UPDATE "SiteTree_Live" SET "ClassName" = \'Blog\' WHERE "ClassName" = \'BlogHolder\'');
			DB::query('UPDATE "SiteTree_versions" SET "ClassName" = \'Blog\' WHERE "ClassName" = \'BlogHolder\'');
			echo "Updated ClassName reference to Blog" . $this->eol;
		} catch (Exception $e) {
			echo $e;
		}

		//Migrate BlogTree to Page
		try {
			DB::query('UPDATE "SiteTree" SET "ClassName" = \'Page\' WHERE "ClassName" = \'SiteTree\'');
			DB::query('UPDATE "SiteTree_Live" SET "ClassName" = \'Page\' WHERE "ClassName" = \'SiteTree\'');
			DB::query('UPDATE "SiteTree_versions" SET "ClassName" = \'Page\' WHERE "ClassName" = \'SiteTree\'');
			echo "Migrated BlogTree to Page" . $this->eol;
		} catch (Exception $e) {
			echo $e;
		}
		//Tags migration
		try {
			$tagcount = $this->migrateTags();
			echo "Migrated " . $tagcount . " tags" . $this->eol;
		} catch (Exception $e) {
			echo "Error in tag migration (may have already run), moving along..." . $this->eol;
		}

		//Legacy Widget migration
		if(class_exists('Widget')) {
			try {
				$this->migrateWidgets();
			} catch (Exception $e) {
				echo "Error migrating legacy widgets" . $this->eol;
			}
		}

		$this->extend('onAfterBlogMigration', $request, $this->eol);

		//IT'S CLEANUP TIME
		if($request->getVar('cleanup') == 1){
			try {
				$this->cleanUp();
				echo "Cleaned up all old blog tables" . $this->eol;
			} catch (Exception $e) {
				echo "Error with blog table cleanup (may have already cleaned up), moving along..." . $this->eol;
			}

		}

		echo "Migration complete." . $this->eol;
		exit;

	}

	/*
     * Migrate the tags
	 *
	 * @return int number of migrated tags
     */
	protected function migrateTags(){
		//1. Get all BlogEntry ID and comma separated tags into an array
		$blogtags = DB::query('SELECT ID, Tags FROM BlogEntry_Live')->map('ID','Tags');
		$tagcount = 0;
		//2. Foreach split the tags into own array
		foreach($blogtags as $blogpostid => $tags){
			foreach($this->tagNames($tags) as $tag) {
				//3a. If it's an existing tag, connect the BlogPost and BlogTag as many_many
				$existingTagID = DB::query('SELECT ID FROM BlogTag WHERE Title = \'' .$tag. '\'')->value();
				if($existingTagID) {
					$tagID = $existingTagID;
				} else {
					//3b. If it's a new tag, add the BlogTag and then connect the BlogPost and BlogTag as many_many

					//Get the ParentID of the BlogPost
					$parentID = DB::query('
                    SELECT "ParentID"
                    FROM "BlogPost_Live"
                    LEFT JOIN "SiteTree_Live" ON "SiteTree_Live"."ID" = "BlogPost_Live"."ID"
                    WHERE "BlogPost_Live"."ID" = \'' . $blogpostid . '\'')->value();

					//Write the new tag using ORM to ensure the URLSegment is generated correctly.
					$tagObject = BlogTag::create();
					$tagObject->Title = $tag;
					$tagObject->BlogID = $parentID;
					$tagObject->write();
					$tagID = $tagObject->ID;

					$tagcount++;

				}
				// 4. Add the tag to the blogpost
				DB::query('
                    INSERT INTO "BlogPost_Tags"
                    SET
                    "BlogPostID" = \'' . $blogpostid . '\',
                    "BlogTagID" = \'' . $tagID . '\''
				);


			}

		}

		return $tagcount;
	}

	/**
	 * Safely split and parse all distinct tags assigned to a BlogEntry.
	 *
	 * @param string comma-separated tags
	 * @return array
	 */
	protected function tagNames($tags) {
		$tags = preg_split('/\s*,\s*/', trim($tags));

		$results = array();

		foreach($tags as $tag) {
			if($tag) $results[mb_strtolower($tag)] = $tag;
		}

		return $results;
	}


	/*
     * Migrate ArchiveWidget
     *
     * We set the BlogID to the first Live BlogID that can be found as we cannot know this value from
     * the old blog data structure.
     *
     * Set some default values for the NumberToDisplay as this was also not part of Blog 1.0 data.
     */
	protected function migrateWidgets() {
		//Get the first Blog ID else set to 0.
		$widgetblogid = DB::query('SELECT "ID" FROM "Blog_Live" ORDER BY "ID" LIMIT 1')->value();
		if (!$widgetblogid) {
			$widgetblogid = 0;
		}

		//ArchiveWidget to BlogArchiveWidget
		//Yearly
		DB::query('
			INSERT INTO "BlogArchiveWidget" ("ID", "NumberToDispay", "ArchiveType", "BlogID")
			(
				SELECT "ID", 10, \'Yearly\', ' . $widgetblogid . '
				FROM "ArchiveWidget"
				WHERE "DisplayMode" = \'year\'
			)
		');

		//Monthly
		DB::query('
			INSERT INTO "BlogArchiveWidget" ("ID", "NumberToDispay", "ArchiveType", "BlogID")
			(
				SELECT "ID", 10, \'Monthly\', ' . $widgetblogid . '
				FROM "ArchiveWidget"
				WHERE "DisplayMode" = \'month\'
			)
		');
		//Update the Widget ClassName
		DB::query('UPDATE "Widget" SET "ClassName" = \'BlogArchiveWidget\' WHERE "ClassName" = \'ArchiveWidget\'');
		echo "Migrated ArchiveWidget to BlogArchiveWidget" . $this->eol;


		// TagCloudWidget to BlogTagsWidget - sort set to Title ASC as no equivalent of 'frequency' exists in blog 2.0
		DB::query('
			INSERT INTO "BlogTagsWidget" ("ID", "Limit", "Order", "Direction", "BlogID")
			(
				SELECT "ID", "Limit", \'Title\', \'ASC\', ' . $widgetblogid . '
				FROM "TagCloudWidget"
			)
		');

		//Update the Widget ClassName
		DB::query('UPDATE "Widget" SET "ClassName" = \'BlogTagsWidget\' WHERE "ClassName" = \'TagCloudWidget\'');
		echo "Migrated TagCloudWidget to BlogTagsWidget" . $this->eol;
	}

	/*
     * Clean up old tables
	 *
	 * @return boolean true of the clean up ran without an issue.
     */
	protected function cleanUp() {
		DB::query('DROP TABLE "BlogEntry"');
		DB::query('DROP TABLE "BlogEntry_Live"');
		DB::query('DROP TABLE "BlogEntry_versions"');
		DB::query('DROP TABLE "BlogHolder"');
		DB::query('DROP TABLE "BlogHolder_Live"');
		DB::query('DROP TABLE "BlogHolder_versions"');
		DB::query('DROP TABLE "BlogTree"');
		DB::query('DROP TABLE "BlogTree_Live"');
		DB::query('DROP TABLE "BlogTree_versions"');
		DB::query('DROP TABLE "ArchiveWidget"');
		DB::query('DROP TABLE "TagCloudWidget"');

		$this->extend('updateTablesToCleanUp');

		return true;

	}
}