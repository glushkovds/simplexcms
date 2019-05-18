<?php

class SFFMultiKey extends SFField {

    private static $buffer;

    public function __construct($row) {
        parent::__construct($row);
        $this->isVirtual = true;
    }

    public function input($value) {
        $fields = array("*");
        $value = (int) @$_GET[$this->tablePk];
        if ($value) {
            $fields[] = "(select count(*) from {$this->params['table_relations']} where $this->tablePk = $value and {$this->params['key']} = t.{$this->params['key']}) checked";
        }
        $q = "SELECT " . implode(', ', $fields) . " FROM {$this->params['table_values']} t";
        $rows = SFDB::assoc($q);
        echo '<div class="checkbox-list">';
        foreach ($rows as $row) {
            echo '<label class="checkbox-inline"><input type="checkbox" name="' . $this->name . '[' . $row[(string) $this->params['key']] . ']" value="' . $row[(string) $this->params['key']] . '"' . (!empty($row['checked']) ? ' checked' : '') . ' /> ' . $row[(string) $this->params['key_alias']] . '</label>';
        }
        echo '</div>';
    }

    public function getPOST($simple = false, $group = null) {
        $pkValue = (int) @$_REQUEST[$this->tablePk];
        $values = isset($_POST[$this->name]) ? $_POST[$this->name] : array();
        $q = "DELETE FROM {$this->params['table_relations']} where $this->tablePk = $pkValue";
        SFDB::query($q);
        foreach ($values as $value) {
            $value = (int) $value;
            $q = "INSERT INTO {$this->params['table_relations']} set $this->tablePk = $pkValue, {$this->params['key']} = $value";
            SFDB::query($q);
        }
        return '';
    }

    public function show($row) {
        $pkValue = (int) $row[$this->tablePk];
        if (!isset(self::$buffer)) {
            $q = "SELECT {$this->params['key']}, {$this->params['key_alias']} FROM {$this->params['table_values']}";
            $values = SFDB::assoc($q, $this->params['key']);
            $where = AdminBase::$currentWhere;
            $q = "
                SELECT $this->tablePk,
                    (SELECT {$this->params['key_alias']} FROM {$this->params['table_values']} WHERE {$this->params['key']} = r.{$this->params['key']}) label
                FROM {$this->params['table_relations']} r WHERE $this->tablePk IN (
                    SELECT $this->tablePk FROM $this->table $where
                )
            ";
            $rels = SFDB::assoc($q, $this->tablePk, 'label');
            foreach ($rels as $key => $keyAliases) {
                self::$buffer[$key] = implode(', ', array_keys($keyAliases));
            }
        }
        echo (string) @self::$buffer[$pkValue];
    }

    public function selectQueryField() {
        return '';
    }

}
