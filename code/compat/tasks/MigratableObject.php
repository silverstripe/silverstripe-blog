<?php

interface MigratableObject
{
    /**
     * Migrate the object up to the current version.
     */
    public function up();
}
