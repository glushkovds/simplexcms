<?php

class SFDB
{

    /**
     *
     * @var SFDBMySQL
     */
    private static $db = false;
    private static $queries = array();

    private function __construct()
    {

    }

    private static function create()
    {
        switch (SFConfig::$db_type) {
            case 'mysql' :
                return new SFDBMySQL();
            default :
                die("<b>Error! Unknown Database type.</b>");
        }
    }

    public static function connect()
    {
        self::$db = self::create();
        self::$db->connect();
    }

    public static function bind($vars)
    {
        if (is_array($vars)) {
            return self::$db->bind($vars);
        }
        return false;
    }

    public static function &query($q)
    {
        $t = microtime(1);
        $r = self::$db->query($q);
        if (self::$db->errno()) {
            if (imDev()) {
                echo '<pre>', $q, '</pre>';
                echo self::$db->error();
            }
        }
        if (imDev()) {
            self::$queries[] = array('time' => microtime(1) - $t, 'sql' => $q, 'error' => self::$db->errno() ? self::$db->error() : '');
        }
        return $r;
    }

    public static function fetch(&$result)
    {
        return self::$db->fetch($result);
    }

    public static function result($q, $field = '')
    {
        return self::$db->result(self::query($q), $field);
    }

    public static function assoc($q, $field1 = false, $field2 = false)
    {
        return self::$db->assoc(self::query($q), $field1, $field2, $q);
    }

    public static function insertID()
    {
        return self::$db->insertID();
    }

    public static function getTime($time, $length = 4)
    {
        $a = explode(' ', $time);
        $b = explode(' ', microtime());
        return substr($b[0] - $a[0] + $b[1] - $a[1], 0, $length + 2);
    }

    public static function debug($time, $length = 4)
    {
        if (SFUser::ican('debug')) {
            $time = self::getTime($time, $length);
            echo '<div style="position:absolute;z-index:10000;top:18px;right:50%;margin-right:-600px;cursor:pointer;border:1px dashed #999;padding:2px 7px;line-height:1.2;background-color:#EEE;font-size:11px;color:#363"  onclick="document.getElementById(\'debug-box\').style.display = document.getElementById(\'debug-box\').style.display==\'block\' ? \'none\' : \'block\'"><span style="color:#444">', count(self::$queries), '</span> / <span style="color:#666">', number_format($time, $length), '</span></div>';
            echo '<div id="debug-box" style="display:none;position:absolute;z-index:10000;top:48px;right:50%;margin-right:-600px;width:300px;height:500px;overflow:auto;border:1px dashed #999;padding:2px 7px;line-height:1.2;background-color:#EEE;font-size:11px;color:#363">';
            echo '<table style="table-layout:auto;">';
            $sumTime = 0;
            foreach (self::$queries as $key => $val) {
                $sumTime += $val['time'];
                echo '<tr>';
                echo '<td style="color:#999;padding:2px 4px;vertical-align:top">', $key + 1, '</td>';
                echo '<td style="color:#999;padding:2px 4px;vertical-align:top">', number_format($val['time'], $length), '</td>';
                echo '<td style="white-space:nowrap;color:#666;padding:2px 4px">', nl2br(trim($val['sql'])), '<br /><b>', nl2br($val['error']), '</b></td>';
                echo '</tr>';
            }
            echo '<tr>';
            echo '<td style="color:#999;padding:2px 4px;vertical-align:top">!</td>';
            echo '<td style="color:#999;padding:2px 4px;vertical-align:top">', number_format($sumTime, $length), '</td>';
            echo '<td style="white-space:nowrap;color:#666;padding:2px 4px">Общее время на запросы</td>';
            echo '</tr>';
            echo '</table>';
            echo '</div>';

            if (SFCore::uri(0) != 'admin') {
                echo '<div style="position:absolute;z-index:10000;top:48px;right:50%;margin-right:-600px;padding:1px 8px;background:#EEE;border:1px dashed #666;font-size:11px;color:#666">', number_format(memory_get_peak_usage() / 1024, 1, ',', ' '), ' - ', number_format((memory_get_usage() - $GLOBALS['m0']) / 1024, 1, ',', ' '), '</div>';
            }
        }
    }

    public static function errno()
    {
        return self::$db->errno();
    }

    public static function error()
    {
        return self::$db->error();
    }

    public static function escape($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $index => $str) {
                $mixed[$index] = self::escape($str);
            }
            return $mixed;
        }

        return self::$db->escape($mixed);
    }

    public static function enumValues($table, $field)
    {
        $buffer = &$_ENV['enum_values'][$table][$field];

        if (!isset($buffer)) {
            $q = "SHOW FULL COLUMNS FROM `$table` LIKE '$field'";
            $row = SFDB::result($q);
            $enumArray = array();
            preg_match_all("/'(.*?)'/", $row['Type'], $enumArray);
            $enumFields = $enumArray[1];
            $names = explode(';;', $row['Comment']);
            if (count($names) == count($enumFields)) {
                $ret = array();
                foreach ($names as $index => $name) {
                    $ret[$enumFields[$index]] = trim($name);
                }
                $buffer = $ret;
            } else {
                $buffer = array();
                foreach ($enumFields as $name) {
                    $buffer[$name] = $name;
                }
            }
        }

        return $buffer;
    }

    public static function columnInfo($table, $field)
    {
        $q = "SHOW FULL COLUMNS FROM `$table` LIKE '$field'";
        $row = SFDB::result($q);
        return $row;
    }

    public static function affectedRows()
    {
        return self::$db->affectedRows();
    }

    public static function transactionStart()
    {
        self::query('BEGIN');
    }

    public static function transactionCommit()
    {
        self::query('COMMIT');
    }

    public static function transactionRollback()
    {
        self::query('ROLLBACK');
    }

    /**
     *
     * @param bool $success true - commit, false - rollback
     */
    public static function transactionEnd($success)
    {
        $success ? self::transactionCommit() : self::transactionRollback();
    }

    public static function seek(&$r, $index)
    {
        return self::$db->seek($r, $index);
    }

    public static function fetchReset(&$r)
    {
        return self::seek($r, 0);
    }

}

class SFDBMySQL
{

    /**
     *
     * @var mysqli
     */
    private $link;

    public function connect()
    {
        $this->link = mysqli_connect(SFConfig::$db_host, SFConfig::$db_user, SFConfig::$db_pass) or die("<b>Error! Can not connect to MySQL.</b>");
        mysqli_select_db($this->link, SFConfig::$db_name) or die("<b>Error! Can not select database.</b>");
        mysqli_query($this->link, "SET names utf8");
        mysqli_query($this->link, "SET time_zone = '" . date('P') . "'");
    }

    public function bind($vars)
    {
        $q = 'SET';
        foreach ($vars as $key => $val) {
            $q .= ' @' . $key . '=' . (is_numeric($val) ? $val : "'" . mysqli_escape_string($this->link, $val) . "'") . ',';
        }
        return mysqli_query($this->link, substr($q, 0, -1));
    }

    public function query($q)
    {
        $_ENV['lastq'] = $q;
        return mysqli_query($this->link, $q);
    }

    public function fetch(&$result)
    {
        return mysqli_fetch_assoc($result);
    }

    public function seek(&$r, $index)
    {
        return @mysqli_data_seek($r, $index);
    }

    public function result($r, $field = '')
    {
        if (is_int($field)) {
            $result = mysqli_fetch_row($r);
            return $result[$field] ? $result[$field] : false;
        }
        $result = mysqli_fetch_assoc($r);
        return $field ? (isset($result[$field]) ? $result[$field] : false) : $result;
    }

    public function assoc(&$r, $field1 = false, $field2 = false, $q = FALSE)
    {
        $rows = array();
        if ($field1) {
            if ($field2 === false) {
//                if(!$r){
//                    echo $q;die;
//                }
                while ($row = mysqli_fetch_assoc($r)) {
                    $rows[$row[$field1]] = $row;
                }
            } elseif ($field2) {
                while ($row = mysqli_fetch_assoc($r)) {
                    $rows[$row[$field1]][$row[$field2]] = $row;
                }
            } else {
                while ($row = mysqli_fetch_assoc($r)) {
                    $rows[$row[$field1]][] = $row;
                }
            }
        } else {
            if ($r instanceof mysqli_result) {
                while ($row = mysqli_fetch_assoc($r)) {
                    $rows[] = $row;
                }
            } else {

            }
        }
        return $rows;
    }

    public function insertID()
    {
        $ret = mysqli_fetch_row($this->query("SELECT LAST_INSERT_ID()"));
        return $ret[0];
    }

    public function errno()
    {
        return mysqli_errno($this->link);
    }

    public function error()
    {
        $errs = array(
            1451 => 'Нельзя удалить запись, имеются связанные записи'
        );
        $n = mysqli_errno();
        return $n > 0 ? 'Ошибка. Код: ' . $n . '. ' . (isset($errs[$n]) ? $errs[$n] : mysql_error()) : '';
    }

    public function escape($str)
    {
        return mysqli_escape_string($this->link, $str);
    }

    public function affectedRows()
    {
        return mysqli_affected_rows($this->link);
    }

}

class SFDBWhere implements ArrayAccess
{

    private $data = array();

    /**
     *
     * @param mixed $where
     */
    public function __construct($where = [])
    {
        if ($where instanceof static) {
            $this->data = array_filter($where->toArray());
        }
        if (is_string($where)) {
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
        if ($data = static::prepareData($this->data)) {
            return "WHERE " . implode(' AND ', $data);
        }
        return '';
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return static::prepareData($this->data);
    }

    /**
     * @param array $data
     * @return array
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

/**
 * Class SFDBAQ
 * Simplex Framework DataBase Active Query
 */
class SFDBAQ
{

    protected $select = '*';
    protected $from;
    protected $where = '';
    protected $orderBy = '';
    protected $limit = '';
    protected $asArray = false;
    protected $modelClass;

    /**
     * @param bool $asArray
     * @return $this
     */
    public function asArray(bool $asArray = true)
    {
        $this->asArray = $asArray;
        return $this;
    }

    /**
     * @param string|array $fields
     * @return $this
     */
    public function select($fields)
    {
        $this->select = $fields;
        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function from($table)
    {
        $this->from = $table;
        return $this;
    }

    /**
     * @param string|array|SFDBWhere $where
     * @return $this
     */
    public function where($where)
    {
        $this->where = $where;
        return $this;
    }

    /**
     * @param string|array|SFDBWhere $where
     * @return $this
     */
    public function andWhere($where)
    {
        $this->where = new SFDBWhere($this->where);
        $this->where->add($where);
        return $this;
    }

    /**
     * @param string $orderBy
     * @return $this
     */
    public function orderBy($orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * @param string $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function setModelClass(string $class)
    {
        $this->modelClass = $class;
        return $this;
    }

    protected function getSelect()
    {
        if (is_array($this->select)) {
            return implode(', ', $this->select);
        }
        if (is_string($this->select)) {
            return $this->select;
        }
        throw new \Exception('Bad select statement');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function build()
    {
        if (empty($this->from) || !is_string($this->from)) {
            throw new \Exception('Bad from statement');
        }
        $q[] = 'SELECT ' . $this->getSelect();
        $q[] = "FROM `$this->from`";
        $q[] = new SFDBWhere($this->where);
        if ($orderBy = (string)$this->orderBy) {
            $q[] = 'ORDER BY ' . SFDB::escape($orderBy);
        }
        if ($limit = (string)$this->limit) {
            $q[] = 'LIMIT ' . SFDB::escape($limit);
        }
        return implode(' ', $q);
    }

    /**
     * @param string|array|null $assocKey
     * @return array|SFModelBase[]
     * @throws Exception
     */
    public function all($assocKey = null)
    {

        if ((is_bool($assocKey) || !$this->asArray) && empty($this->modelClass)) {
            throw new \Exception('Model class not specified');
        }
        $q = $this->build();
        $r = SFDB::query($q);
        $result = [];
        while ($row = SFDB::fetch($r)) {
            $payload = $this->asArray ? $row : (new $this->modelClass)->fill($row);
            if ($assocKey === true) {
                $result[$row[$this->modelClass::$primaryKeyName]] = $payload;
            } elseif (is_array($assocKey)) {
                $assocKey = array_values($assocKey);
                switch (count($assocKey)) {
                    case 1:
                        $result[$row[$assocKey[0]]] = $payload;
                        break;
                    case 2:
                        $result[$row[$assocKey[0]]][$row[$assocKey[1]]] = $payload;
                        break;
                    case 3:
                        $result[$row[$assocKey[0]]][$row[$assocKey[1]]][$row[$assocKey[2]]] = $payload;
                        break;
                    case 0:
                    default:
                        $result[] = $payload;
                }
            } elseif (is_string($assocKey)) {
                $result[$row[$assocKey]] = $payload;
            } else {
                $result[] = $payload;
            }
        }
        return $result;
    }

    public function __toString()
    {
        return $this->build();
    }

}

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
