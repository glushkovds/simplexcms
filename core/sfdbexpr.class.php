<?php

/**
 * Class SFDBExpr
 * Use instance of this class to unescape your statement
 */
class SFDBExpr
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->value;
    }
}