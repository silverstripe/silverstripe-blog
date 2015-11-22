<?php

if (!class_exists('Widget')) {
    return;
}

/**
 * @deprecated since version 2.0
 *
 * @property string $DisplayMode
 * @property string $ArchiveType
 */
class ArchiveWidget extends BlogArchiveWidget implements MigratableObject
{
    /**
     * @var array
     */
    private static $db = array(
        'DisplayMode' => 'Varchar',
    );

    /**
     * @var array
     */
    private static $only_available_in = array(
        'none',
    );

    /**
     * {@inheritdoc}
     */
    public function canCreate($member = null)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        if ($this->DisplayMode) {
            $this->ArchiveType = 'Monthly';

            if ($this->DisplayMode === 'year') {
                $this->ArchiveType = 'Yearly';
            }
        }

        $this->ClassName = 'BlogArchiveWidget';
        $this->write();
        return "Migrated " . $this->ArchiveType . " archive widget";
    }
}
