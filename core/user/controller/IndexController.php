<?php
namespace core\user\controller;

use core\base\controller\BaseController;

class IndexController extends BaseController {

    protected $name;

    protected function inputData(){
        $str = '1234567890abcdefg';
        $en_str = \core\base\model\Crypt::instance()->encrypt($str);
        $dec_str = \core\base\model\Crypt::instance()->decrypt($en_str);
        exit();
    }
}