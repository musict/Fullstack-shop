<?php


namespace core\base\model;


use core\base\controller\Singleton;
use core\base\exceptions\DBException;

class BaseModel
{
    use Singleton;
    protected $db;

    private function __construct(){
        $this->db = @new \mysqli(HOST, USER, PASS, DB_NAME);
        if ($this->db->connect_error){
            throw new DBException('Ошибка подключения к базе данных '
             . $this->db->connect_errno . ' ' . $this->db->connect_error);
        }
        $this->db->query("SET NAMES UTF8");
    }

    final public function query($query, $crud = 'r', $return_id = false){
        $result = $this->db->query($query);
        if ($this->db->affected_rows === -1){
            throw new DBException('Ошибка в SQL запросе: '
            . $query . ' ' . $this->db->errno . ' ' . $this->db->error);
        }
        switch ($crud){
            case 'r':
                if ($result->num_rows){
                    $res = [];
                    for ($i = 0; $i<$result->num_rows; $i++){
                        $res[] = $result->fetch_assoc();
                    }
                    return $res;
                }
                return false;

            case 'c':
                if ($return_id) return $this->db->insert_id;
                return true;

            default:
                return true;
        }

    }

    /**
    @param $table - Таблица БД
    @param array $set
    'fields' => ['id', 'name'],
    'where' => ['id' => 1, 'name' => 'Masha'],
    'operand' => ['=', '<>'],
    'condition' => ['AND'],
    'order' => ['fio', 'name'],
    'order_direction' => ['ASC', 'DESC'],
    'limit' => '1'
     */
    final public function get($table, $set = []){
        $fields = $this->createFields($table, $set);
        $order = $this->createOrder($table, $set);
        $where = $this->createWhere($table, $set);
        $join_arr = $this->createJoin($table, $set);
        $fields .= $join_arr['fields'];
        $join = $join_arr['join'];
        $where .= $join_arr['where'];
        $fields = rtrim($fields, ',');

        $limit = $set['limit'] ? $set['limit'] : '';

        $query = "SELECT $fields FROM $table $join $where $order $limit";
        return $this->query($query);
    }

    protected function createFields($table = false, $set){
        $set['fields'] = (is_array($set['fields']) && !empty($set['fields'])) ? $set['fields'] : ['*'];
        $table = $table ? $table . '.' : '';
        $fields = '';

        foreach ($set['fields'] as $field){
            $fields .= $table . $field . ',';
        }
        return $fields;
    }

    protected function createOrder($table = false, $set){
        $table = $table ? $table . '.' : '';
        $order_by = '';
        if (is_array($set['order']) && !empty($set['order'])){
            $set['order_direction'] = (is_array($set['order_direction']) &&
                !empty($set['order_direction'])) ? $set['order_direction'] : ['ASC'];
            $direct_count = 0;
            $order_by = 'ORDER BY ';
            foreach ($set['order'] as $order){
                if ($set['order_direction'][$direct_count]){
                    $order_direction = strtoupper($set['order_direction'][$direct_count]);
                    $direct_count++;
                } else{
                    $order_direction = strtoupper($set['order_direction'][$direct_count - 1]);
                }
                $order_by .= $table . $order . ' ' . $order_direction . ',';
            }
            $order_by = rtrim($order_by, ',');
        }
        return $order_by;
    }

    protected function createWhere($table = false, $set, $instruction = 'WHERE'){
        $table = $table ? $table . '.' : '';
        $where = '';
        if (is_array($set['where']) && !empty($set['where'])){
            $set['operand'] = (is_array($set['operand']) && !empty($set['operand'])) ? $set['operand'] : ['='];
            $set['condition'] = (is_array($set['condition']) && !empty($set['condition'])) ? $set['condition'] : ['AND'];
            $where = $instruction;
            $operand_count = 0;
            $condition_count = 0;
            foreach ($set['where'] as $key => $item){
                $where .= ' ';
                if ($set['operand'][$operand_count]){
                    $operand = $set['operand'][$operand_count];
                    $operand_count++;
                }else{
                    $operand = $set['operand'][$operand_count -1];
                }

                if ($set['condition'][$condition_count]){
                    $condition = $set['condition'][$condition_count];
                    $condition_count++;
                }else{
                    $condition = $set['condition'][$condition_count -1];
                }
                if ($operand === 'IN' || $operand === 'NOT IN'){
                    if (is_string($item) && strpos($item, 'SELECT')){
                        $in_str = $item;
                    }else{
                        if(is_array($item)) $temp_item = $item;
                        else $temp_item = explode(',', $item);
                        $in_str = '';
                        foreach ($temp_item as $value){
                            $in_str .= "'" . trim($value) . "',";
                        }
                    }
                    $where .= $table . $key . ' ' . $operand . ' (' . trim($in_str, ',') . ') ' . $condition;

                }elseif (strpos($operand, 'LIKE') !== false){
                    $like_template = explode('%', $operand);
                    foreach ($like_template as $lt_key => $lt){
                        if (!$lt){
                            if (!$lt_key){
                                $item = '%' . $item;
                            }else{
                                $item .= '%';
                            }
                        }
                    }

                    $where .= $table . $key . ' LIKE ' . "'" . $item . "' $condition";
                }else{
                    if (strpos($item, 'SELECT') === 0){
                        $where .= $table . $key . $operand . ' (' . $item . ") $condition";
                    } else{
                        $where .= $table . $key . $operand . "'" . $item . "' $condition";
                    }
                }

            }
            $where = substr($where, 0, strrpos($where, $condition));
        }
        return $where;
    }
}