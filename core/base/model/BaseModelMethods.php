<?php


namespace core\base\model;


abstract class BaseModelMethods
{
    protected $sqlFunc = ['NOW()'];


    protected function createFields($set, $table = false){
        $set['fields'] = (is_array($set['fields']) && !empty($set['fields'])) ? $set['fields'] : ['*'];
        $table = $table ? $table . '.' : '';
        $fields = '';

        foreach ($set['fields'] as $field){
            $fields .= $table . $field . ',';
        }
        return $fields;
    }

    protected function createOrder($set, $table = false){
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
                if (is_int($order)){
                    $order_by .= $order . ' ' . $order_direction . ',';
                }else{
                    $order_by .= $table . $order . ' ' . $order_direction . ',';
                }
            }
            $order_by = rtrim($order_by, ',');
        }
        return $order_by;
    }

    protected function createWhere($set, $table = false, $instruction = 'WHERE'){
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
                    if (is_string($item) && strpos($item, 'SELECT') === 0){
                        $in_str = $item;
                    }else{
                        if(is_array($item)) $temp_item = $item;
                        else $temp_item = explode(',', $item);
                        $in_str = '';
                        foreach ($temp_item as $value){
                            $in_str .= "'" . addslashes(trim($value)) . "',";
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

                    $where .= $table . $key . ' LIKE ' . "'" . addslashes($item) . "' $condition";
                }else{
                    if (strpos($item, 'SELECT') === 0){
                        $where .= $table . $key . $operand . ' (' . $item . ") $condition";
                    } else{
                        $where .= $table . $key . $operand . "'" . addslashes($item) . "' $condition";
                    }
                }

            }
            $where = substr($where, 0, strrpos($where, $condition));
        }
        return $where;
    }

    protected function createJoin($set, $table, $new_where = false){
        $fields = '';
        $join = '';
        $where = '';
        $tables = '';
        if ($set['join']){
            $join_table = $table;
            foreach ($set['join'] as $key => $item){
                if(is_int($key)){
                    if (!$item['table']) continue;
                    else $key = $item['table'];
                }
                if ($join) $join .= ' ';
                if($item['on']){
                    $join_fields = [];
                    switch (2){
                        case count($item['on']['fields']):
                            $join_fields = $item['on']['fields'];
                            break;
                        case count($item['on']):
                            $join_fields = $item['on'];
                            break;
                        default:
                            continue 2;
                    }
                    if (!$item['type']) $join .= 'LEFT JOIN ';
                    else $join .= trim(strtoupper($item['type'])) . ' JOIN ';
                    $join .= $key . ' ON ';
                    if ($item['on']['table']) $join .= $item['on']['table'];
                    else $join .= $join_table;
                    $join .= '.' . $join_fields[0] . '=' . $key . '.' . $join_fields[1];
                    $join_table = $key;
                    $tables .= ', ' . trim($join_table);
                    if ($new_where){
                        if ($item['where']) {
                            $new_where = false;
                        }
                        $group_condition = 'WHERE';
                    }else{
                        $group_condition = $item['group_condition'] ? strtoupper($item['group_condition']) : 'AND';
                    }
                    $fields .= $this->createFields($item, $key );
                    $where .= $this->createWhere($item, $key,  $group_condition);
                }
            }
        }
        return compact('fields', 'join', 'where', 'tables');
    }

    protected function createInsert($fields, $files, $except){

        $insert_arr = [];
        if ($fields){
            foreach ($fields as $row => $value){
                if ($except && in_array($row, $except)) continue;
                $insert_arr['fields'] .= $row . ',';
                if (in_array($value, $this->sqlFunc)){
                    $insert_arr['values'] .= $value . ',';
                }else{
                    $insert_arr['values'] .= "'" . addslashes($value) . "',";
                }
            }
        }
        if ($files){
            foreach ($files as $row => $file){
                $insert_arr['fields'] .= $row . ',';
                if (is_array($file)) $insert_arr['values'] .= "'" . addslashes(json_encode($file)) . "',";
                    else $insert_arr['values'] .= "'" . addslashes($file) . "',";
            }
        }
        foreach ($insert_arr as $key => $arr) $insert_arr[$key] = rtrim($arr, ',');
        return $insert_arr;
    }

    protected function createUpdate($fields, $files, $except){
        $update = '';
        if ($fields){
            foreach ($fields as $row => $value){
                if ($except && in_array($row, $except)) continue;
                $update .= $row . '=';
                if (in_array($value, $this->sqlFunc)){
                    $update .= $value . ',';
                }elseif ($value === NULL){
                    $update .= "NULL" . ',';
                }
                else {
                    $update .= "'" . addslashes($value) . "',";
                }
            }
        }
        if ($files){
            foreach ($files as $row => $file){
                $update .= $row . '=';
                if (is_array($file)) $update .= "'" . addslashes(json_encode($file)) . "',";
                else $update .= "'" . addslashes($file) . "',";
            }
        }
        return rtrim($update, ',');
    }

    /**
    $res = $db->get($table, [
    'fields' => ['id', 'name'],
    'where' => ['name' => 'Masha', 'surname' => 'Ivanova', 'fio' => 'Andrey', 'car' => 'Porshe', 'color' => $color],
    'operand' => ['IN', 'LIKE%', '<>', '=', 'NOT IN'],
    'condition' => ['OR','AND'],
    'join' => [
    [
    'table' => 'join_table1',
    'fields' => ['id as j_id', 'name as j_name'],
    'type' => 'left',
    'where' => ['name' => 'Sasha'],
    'operand' => ['='],
    'condition' => ['OR'],
    'on' => ['id', 'parent_id'],
    'group_condition' => 'AND'

    ],

    'join_table2' => [
    'table' => 'join_table2',
    'fields' => ['id as j2_id', 'name as j2_name'],
    'type' => 'left',
    'where' => ['name' => 'Sasha'],
    'operand' => ['='],
    'condition' => ['AND'],
    'on' => [
    'table' => 'teachers',
    'fields' => ['id', 'parent_id']
    ]
    ]

    ]
    ]);

     */

    public function delete($table, $set){
        $table = trim($table);
        $where = $this->createWhere($set, $table);
        $columns = $this->showColumns($table);
        if (!$columns) return false;
        if (is_array($set['fields']) && !empty($set['fields'])){
            if ($columns['id_row']){
                $key = array_search($columns['id_row'], $set['fields']);
                if ($key !== false) unset($set['fields'][$key]);
            }
            $fields = [];
            foreach ($set['fields'] as $field){
                $fields[$field] = $columns[$field]['Default'];
            }
            $update = $this->createUpdate($fields, false, false);
            $query = "UPDATE $table SET $update $where";
        }else{
            $join_arr = $this->createJoin($set, $table);
            $join = $join_arr['join'];
            $join_tables = $join_arr['tables'];

            $query = 'DELETE ' . $table . $join_tables . ' FROM ' . $table . ' ' . $join . ' ' . $where;
        }
        return $this->query($query, 'u');
    }
}