<?php

class SFDBWhere implements ArrayAccess
{

    private $data = array();

    /**
     *
     * @param mixed $where
     * @throws Exception
     */
    public function __construct($where = [])
    {
        if ($where instanceof static) {
            $this->data = array_filter($where->toArray());
        }
        if (is_string($where) || $where instanceof SFDBExpr) {
            $this->data[] = $where;
        }
        if (is_array($where)) {
            $this->data = array_filter($where);
        }
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @param bool $withWhereWord = true
     * @return string
     * @throws Exception
     */
    public function toString($withWhereWord = true)
    {
        if ($data = static::prepareData($this->data)) {
            return ($withWhereWord ? 'WHERE ' : '') . implode(' AND ', $data);
        }
        return '';
    }

    /**
     * @return array
     * @throws Exception
     */
    public function toArray()
    {
        return static::prepareData($this->data);
    }

    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected static function prepareData(array $data)
    {
        $result = [];
        foreach ($data as $index => $value) {
            if (
                // Skip condition if empty array
                is_array($value) && !$value
                // Or if empty trimmed string
                || !is_array($value) && !($value = trim($value))
            ) {
                continue;
            }
            if ($isAssociative = (string)$index !== (string)(int)$index) {
                if (is_array($value)) {
                    $values = implode(',', array_map(['SFModelBase', 'prepareValue'], $value));
                    $result[] = "`$index` IN ($values)";
                } else {
                    $result[] = "`$index` = " . SFModelBase::prepareValue($value);
                }
            } else {
                if (is_array($value)) {
                    throw new \Exception('SFDBWhere: array-valued statement must be associative.');
                } else {
                    $result[] = $value;
                }
            }
        }
        return $result;
    }

    /**
     * @param string|array|SFDBWhere $where
     */
    public function add($where)
    {
        $this->data = array_filter(array_merge($this->toArray(), (new SFDBWhere($where))->toArray()));
    }

}

