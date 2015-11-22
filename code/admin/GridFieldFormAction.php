<?php

class GridFieldFormAction extends GridField_FormAction
{
    /**
     * @var array
     */
    protected $extraAttributes = array();

    /**
     * @param array $attributes
     */
    public function setExtraAttributes(array $attributes)
    {
        $this->extraAttributes = $attributes;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $attributes = parent::getAttributes();

        return array_merge(
            $attributes,
            $this->extraAttributes
        );
    }
}
