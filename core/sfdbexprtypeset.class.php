<?php

/**
 * Class SFDBExprTypeSet
 * Syntax sugar for work with MySQL datatype SET
 * @example We need to remove 'value1' from field `tags` which value is 'value1,value2,value3':
 *          $model->update(['tags' => SFDBExprTypeSet::remove('tags', 'value1')]);
 */
class SFDBExprTypeSet extends SFDBExpr
{
    /**
     * @param string $fieldName
     * @param string $value
     * @return static
     */
    public static function add($fieldName, $value)
    {
        return new static("CONCAT(`$fieldName`,'," . SFDB::escape($value) . "')");
    }

    /**
     * @param string $fieldName
     * @param string $value
     * @return static
     */
    public static function remove($fieldName, $value)
    {
        return new static("
            TRIM(BOTH ',' FROM
              REPLACE(
                REPLACE(CONCAT(',',REPLACE(`$fieldName`, ',', ',,'), ','),'," . SFDB::escape($value) . ",', ''), ',,', ','
              )
            )");
    }

    /**
     * @param string $fieldName
     * @param string $value
     * @return static
     */
    public static function in($fieldName, $value)
    {
        return new static("FIND_IN_SET('" . SFDB::escape($value) . "',`$fieldName`)");
    }
}
