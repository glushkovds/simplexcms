<?php

/**
 * Usage
 *
 * 1. Add file to your extension. For example: /ext/test/modeltest.class.php
 * 2. class ModelTest extends SFBaseModel {
 * protected $table = 'test';
 * protected $primaryKeyName = 'test_id';
 * }
 * 3. $model = new ModelTest(); or $model = new ModelTest(123);
 *    $model['text'] = '234'; or $model->fill(['text' => '234']); or $model->load(123);
 *    $model->save(); or $model->insert(); or $model->update(); or $model->delete(); or $model->update(['text' => '234']);
 *    $model = ModelTest::findOne('text = "234"'); or $models = ModelTest::find(['text = "234"']);
 */
abstract class SFModelBase implements ArrayAccess
{

    const FLAG_IGNORE = 1;

    protected $id;
    protected $data = [];
    protected $dataBeforeUpdate = [];
    protected static $table;
    protected static $primaryKeyName;
    protected $lastError = ['code' => 0, 'text' => ''];

    /**
     * @return string
     */
    public static function getPrimaryKeyName()
    {
        return static::$primaryKeyName;
    }

    public function getId()
    {
        return $this->id;
    }

    public function __construct($id = null)
    {
        if ($id) {
            $this->load($id);
        }
    }

    public function load($id)
    {
        $id = (int)$id;
        $q = "SELECT * FROM " . static::$table . " WHERE " . static::$primaryKeyName . " = $id";
        if ($row = SFDB::result($q)) {
            $this->fill($row);
            return true;
        }
        return false;
    }

    public function reload()
    {
        return $this->id && $this->load($this->id);
    }

    /**
     * @param string $fieldName
     * @param bool $withBuffer
     * @return array
     */
    public static function enumValues($fieldName, $withBuffer = true)
    {
        $closure = function () use ($fieldName) {
            return SFDB::enumValues(static::$table, $fieldName);
        };
        if ($withBuffer && class_exists('SFBuffer')) {
            return SFBuffer::getOrSet('enumValues.' . static::$table . ".$fieldName", $closure);
        }
        return $closure();
    }

    /**
     *
     * @param string|array|SFDBWhere $where
     * @param null $orderBy
     * @param null $limit
     * @param bool|string $assocKey [optional = false] get result array with model id's (or other field) in keys
     * @return SFModelBase[]
     * @throws Exception
     */
    public static function find($where, $orderBy = null, $limit = null, $assocKey = false)
    {
        return static::findAdv()->andWhere($where)->orderBy($orderBy)->limit($limit)->all($assocKey);
    }

    /**
     * @return SFDBAQ
     */
    public static function findAdv()
    {
        $query = (new SFDBAQ())->from(static::$table)->setModelClass(static::class);
        return $query;
    }

    /**
     *
     * @param string|array|SFDBWhere $where
     * @return SFModelBase|null
     */
    public static function findOne($where, $returnModelIfNotFound = false)
    {
        $whereObj = new SFDBWhere($where);
        $q = "SELECT * FROM " . static::$table . " $whereObj LIMIT 1";
        $row = SFDB::result($q);
        return $row ? (new static)->fill($row) : ($returnModelIfNotFound ? new static : null);
    }

    /**
     * Заполнить модель данными
     * @param array $data
     * @return $this
     */
    public function fill(array $data)
    {
        $this->data = $data + $this->data;
        $this->id = isset($this->data[static::$primaryKeyName]) ? (int)$this->data[static::$primaryKeyName] : null;
        $this->afterFill();
        return $this;
    }

    /**
     * Вызывается после того, как модель заполнена данными
     */
    protected function afterFill()
    {
    }

    /**
     *
     * @param array $data [optional]
     * @return bool
     */
    public function save(array $data = null)
    {
        $result = false;
        if ($this->beforeSave()) {
            if ($this->id) {
                $result = $this->update($data);
            } else {
                $result = $this->insert($data);
            }
        }
        $this->afterSave($result);
        return $result;
    }

    /**
     *
     * @param mixed $value
     * @return string
     */
    public static function prepareValue($value)
    {
        if ($value instanceof SFDBExpr) {
            return (string)$value;
        }
        is_array($value) || is_object($value) && $value = print_r($value, true);
        is_numeric($value) && $setValue = $value;
        is_null($value) && $setValue = 'NULL';
        is_string($value) && $setValue = "'" . SFDB::escape($value) . "'";
        return $setValue;
    }

    /**
     *
     * @param array $data [optional]
     * @return bool
     */
    public function insert(array $data = null, $flags = 0)
    {
        $data && $this->fill($data);
        $result = false;
        if ($this->beforeInsert()) {
            $set = [];
            foreach ($this->data as $key => $value) {
                $set[] = "`$key` = " . self::prepareValue($value);
            }
            $ignore = $flags & static::FLAG_IGNORE ? ' IGNORE' : '';
            $q = "INSERT$ignore INTO " . static::$table . " SET " . implode(', ', $set);
            if ($result = $this->query($q)) {
                $this->id = SFDB::insertID();
            }
        }
        $this->afterInsert($result);
        return $result;
    }

    /**
     *
     * @param array $data
     * @return bool
     */
    public function update(array $data)
    {
        if (!$this->id) {
            return false;
        }
        $result = false;
        if ($this->beforeUpdate($data)) {
            $set = static::prepareSet($data);
            $q = "UPDATE " . static::$table . " SET " . implode(', ', $set) . " WHERE `" . static::$primaryKeyName . "` = $this->id";
            $result = $this->query($q);
            if ($result) {
                $this->reload();
            }
        }
        $this->afterUpdate($result);
        return $result;
    }

    /**
     * @param array $data
     * @return array
     */
    protected static function prepareSet($data)
    {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "`$key` = " . static::prepareValue($value);
        }
        return $set;
    }

    /**
     *
     * @return boolean
     */
    public function delete()
    {
        if (!$this->id) {
            return false;
        }
        $result = false;
        $oldData = $this->toArray();
        if ($this->beforeDelete()) {
            $q = "DELETE FROM " . static::$table . " WHERE `" . static::$primaryKeyName . "` = $this->id";
            $result = $this->query($q);
            $this->fill([]);
        }
        $this->afterDelete($result, $oldData);
        return $result;
    }

    public static function bulkDelete($where, $viaModels = false)
    {
        if ($viaModels) {
            $success = true;
            foreach (static::find($where) as $model) {
                $success &= $model->delete();
            }
            return $success;
        } else {
            $whereObj = new SFDBWhere($where);
            $q = "DELETE FROM " . static::$table . " $whereObj";
            return SFDB::query($q);
        }
    }

    /**
     * @param array $set
     * @param string|array|SFDBWhere $where
     * @param bool $viaModels
     * @return bool
     */
    public static function bulkUpdate(array $set, $where, $viaModels = false)
    {
        if ($viaModels) {
            $success = true;
            foreach (static::find($where) as $model) {
                $success &= $model->update($set);
            }
            return $success;
        } else {
            $set = static::prepareSet($set);
            $whereObj = new SFDBWhere($where);
            $q = "UPDATE " . static::$table . " SET " . implode(', ', $set) . " $whereObj";
            return SFDB::query($q);
        }
    }

    /**
     * @return bool
     */
    public static function truncate()
    {
        $q = "TRUNCATE TABLE " . static::$table;
        return (bool)SFDB::query($q);
    }

    /**
     *
     * @param string $q
     * @return boolean
     */
    protected function query($q)
    {
        $success = SFDB::query($q);
        if (!$success) {
            $this->lastError['code'] = SFDB::errno();
            $this->lastError['text'] = SFDB::error();
        }
        return $success;
    }

    protected function setError($text, $code = null)
    {
        if (is_array($text)) {
            $code = (int)@$text['code'];
            $text = (string)@$text['text'];
        }
        $this->lastError['code'] = $code;
        $this->lastError['text'] = $text;
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    protected function beforeInsert()
    {
        return true;
    }

    protected function afterInsert($success)
    {

    }

    protected function beforeUpdate($data)
    {
        $this->dataBeforeUpdate = $this->data;
        return true;
    }

    protected function afterUpdate($success)
    {

    }

    protected function beforeDelete()
    {
        return true;
    }

    protected function afterDelete($success, array $oldData)
    {

    }

    protected function beforeSave()
    {
        return true;
    }

    protected function afterSave($success)
    {

    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
            if ($offset == static::$primaryKeyName) {
                $this->id = (int)$value;
            }
        }
    }

    protected static function underscoreToCamelCase($str, $firstIsLower = true)
    {
        if ($firstIsLower) {
            $parts = explode('_', $str);
            $result = $parts[0] . implode('', array_map('ucfirst', array_slice($parts, 1)));
        } else {
            $result = str_replace('_', '', ucwords($str, '_'));
        }
        return $result;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
        if ($offset == static::$primaryKeyName) {
            $this->id = null;
        }
    }

    public function offsetGet($offset)
    {
        if ($this->id && !isset($this->data[$offset])) {
            if (method_exists($this, $method = 'offsetGet' . lcfirst($offset))) {
                $this->data[$offset] = $this->$method();
            }
            if (method_exists($this, $method = 'offsetGet' . $this->underscoreToCamelCase($offset, false))) {
                $this->data[$offset] = $this->$method();
            }
        }
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    public function __unset($name)
    {
        $this->offsetUnset($name);
    }

    public function toArray()
    {
        return $this->data;
    }

}
