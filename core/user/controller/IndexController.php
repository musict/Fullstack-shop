<?php
namespace core\user\controller;

use core\admin\model\Model;
use core\base\controller\BaseController;

class IndexController extends BaseController {

    protected $name;

    protected function inputData(){
        $model = Model::instance();
        $res = $model->get('goods', [
            'where' => ['id' => '1,2'],
            'operand' => ['IN'],
            'join' => [
                'goods_filters' => [
                    'fields' => null,
                    'on' => ['id', 'goods_id']
                ],
                'filters f' => [
                    'fields' => ['name as filter_name'],
                    'on' => ['filters_id', 'id']
                ],
                [
                    'table' => 'filters',
                    'on' => ['parent_id', 'id']
                ]
            ],
            'join_structure' => true,
            'order' => ['id'],
            'order_direction' => ['DESC']
        ]);
        exit;
    }
}