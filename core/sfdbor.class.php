<?php

/**
 * Class SFDBOr
 * Usage
 * $where = new SFDBWhere(['param' => 1, new SFDBOr(['param2' => 2, 'param2' => 3]));
 * Model::findOne($where);
 * With inner SFDBExpr: Model::findOne(new SFDBOr(['param2' => 2, new SFDBExpr('param2 IN (4,5)'])));
 */
class SFDBOr extends SFDBExpr
{
    public function __toString()
    {
        $values = [];
        foreach ($this->value as $key => $value) {
            $values[] = (new SFDBWhere([$key => $value]))->toString(false);
        }
        return '(' . join(' OR ', $values) . ')';
    }
}