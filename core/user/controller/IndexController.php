<?php
namespace core\user\controller;

use core\admin\model\Model;
use core\base\controller\BaseController;

class IndexController extends BaseController {

    protected $name;

    protected function inputData(){
        $model = Model::instance();
        $res = $model->get('teachers', [
            'where' => ['id' => '1,2'],
            'operand' => ['IN'],
            'join' => [
                'stud_teach' => ['on' => ['id', 'teachers']],
                'students' => [
                    'fields' => ['name as student_name'],
                    'on' => ['students', 'id']
                ]
            ],
            'join_structure' => true
        ]);
        exit;
    }
}