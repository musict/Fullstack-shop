<?php

namespace core\admin\controller;

use core\base\controller\BaseController;
use core\admin\model\Model;

class IndexController extends BaseController
{
    protected function inputData(){
        $db = Model::instance();
        $table = 'teachers';

//        for ($i = 0; $i < 10; $i++){
//            $s_id = $db->add('students', [
//                'fields' => ['name' => 'student - ' . $i, 'age' => $i],
//                'return_id' => true
//            ]);
//            $db->add('teachers', [
//                'fields' => ['name' => 'teacher - ' . $i, 'age' => $i, 'student_id' => $s_id],
//                'return_id' => true
//            ]);
//        }

        $res = $db->delete($table, [
            'where' => ['id' => 24],
            'join' => [
                    ['table' => 'students',
                    'on' => ['student_id', 'id']
                ]
            ]
        ]);
        exit('ок');
    }
}