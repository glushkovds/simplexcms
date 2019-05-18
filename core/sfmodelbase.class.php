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
abstract class SFBaseModel implements ArrayAccess {

    protected $id;
    protected $data = [];
    protected $table;
    protected $primaryKeyName;

    public function getId() {
        return $this->id;
    }

    public function __construct($id = null) {
        if ($id) {
            $this->load($id);
        }
    }

    public function load($id) {
        $id = (int) $id;
        $q = "SELECT * FROM $this->table WHERE $this->primaryKeyName = $id";
        if ($row = SFDB::result($q)) {
            $this->fill($row);
            return true;
        }
        return false;
    }

    /**
     * 
     * @param string|array|SFDBWhere $where
     * @param bool $idInKeys [optional = false] get result array with model id's in keys
     * @return SFModBase[]
     */
    public static function find($where, $idInKeys = false) {
        $whereObj = new SFDBWhere($where);
        $q = "SELECT * FROM $this->table $whereObj";
        $result = [];
        foreach (SFDB::assoc($q) as $row) {
            $model = new static($row);
            $idInKeys ? $result[$row[$this->primaryKeyName]] = $model : $result[] = $model;
        }
        return $result;
    }

    /**
     * 
     * @param string|array|SFDBWhere $where
     * @return SFModBase|null
     */
    public static function findOne($where) {
        $whereObj = new SFDBWhere($where);
        $q = "SELECT * FROM $this->table $whereObj LIMIT 1";
        $row = SFDB::result($q);
        return $row ? new static($row) : null;
    }

    public function fill(array $data) {
        $this->data = $data;
        $this->id = isset($this->data[$this->primaryKeyName]) ? (int) $this->data[$this->primaryKeyName] : null;
    }

    /**
     * 
     * @param array $data [optional]
     * @return bool
     */
    public function save(array $data = null) {
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
    protected function prepareValue($value) {
        is_array($value) || is_object($value) && $value = print_r($value, true);
        is_numeric($value) && $setValue = $value;
        is_null($value) && $setValue = 'NULL';
        is_string($value) && $setValue = '"' . SFDB::escape($value) . '"';
        return $setValue;
    }

    /**
     * 
     * @param array $data [optional]
     * @return bool
     */
    public function insert(array $data = null) {
        $data && $this->fill($data);
        $result = false;
        if ($this->beforeInsert()) {
            $set = [];
            foreach ($this->data as $key => $value) {
                $set[] = "`$key` = " . $this->prepareValue($value);
            }
            $q = "INSERT INTO $this->table SET " . implode(',', $set);
            $result = SFDB::query($q);
        }
        $this->afterInsert($result);
        return $result;
    }

    /**
     * 
     * @param array $data [optional]
     * @return bool
     */
    public function update(array $data = null) {
        if (!$this->id) {
            return false;
        }
        $data && $this->fill($data);
        $result = false;
        if ($this->beforeUpdate()) {
            $set = [];
            foreach ($this->data as $key => $value) {
                $set[] = "`$key` = " . $this->prepareValue($value);
            }
            $q = "UPDATE $this->table SET " . implode(',', $set) . " WHERE `$this->primaryKeyName` = $this->id";
            $result = SFDB::query($q);
        }
        $this->afterUpdate($result);
        return $result;
    }

    /**
     * 
     * @return boolean
     */
    public function delete() {
        if (!$this->id) {
            return false;
        }
        $result = false;
        if ($this->beforeDelete()) {
            $q = "DELETE FROM $this->table WHERE `$this->primaryKeyName` = $this->id";
            $result = SFDB::query($q);
            $this->fill([]);
        }
        $this->afterDelete($result);
        return $result;
    }

    protected function beforeInsert() {
        return true;
    }

    protected function afterInsert(boolean $success);

    protected function beforeUpdate() {
        return true;
    }

    protected function afterUpdate(boolean $success);

    protected function beforeDelete() {
        return true;
    }

    protected function afterDelete(boolean $success);

    protected function beforeSave() {
        return true;
    }

    protected function afterSave();

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
            if ($offset == $this->primaryKeyName) {
                $this->id = (int) $value;
            }
        }
    }

    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->data[$offset]);
        if ($offset == $this->primaryKeyName) {
            $this->id = null;
        }
    }

    public function offsetGet($offset) {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

}
