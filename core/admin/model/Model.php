<?php


namespace core\admin\model;
use core\base\controller\Singleton;
use core\base\model\BaseModel;

class Model extends BaseModel
{
    use Singleton;

    public function showForeignKeys($table, $key = false){
        $db = DB_NAME;
        if ($key) $where = "AND COLUMN_NAME = '$key' LIMIT 1";
        $query = "SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
                  FROM information_schema.KEY_COLUMN_USAGE
                  WHERE TABLE_SCHEMA = '$db' AND TABLE_NAME = '$table' AND
                  CONSTRAINT_NAME <> 'PRIMERY' AND REFERENCED_TABLE_NAME is not null $where";
        return $this->query($query);
    }
}