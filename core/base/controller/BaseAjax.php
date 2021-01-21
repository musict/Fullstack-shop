<?php


namespace core\base\controller;


use core\base\settings\Settings;

class BaseAjax extends BaseController
{
    public function route(){

        $route = Settings::get('routes');

        $controller = $route['user']['path'] . 'AjaxController';

        $data = $this->isPost() ? $_POST : $_GET;

        $http_referer = str_replace('/', '\/', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . PATH . $route['admin']['alias']);

        if (isset($data['ADMIN_MODE']) || preg_match('/^' . $http_referer . '(\/?|$)/', $_SERVER['HTTP_REFERER'])){

            unset($data['ADMIN_MODE']);
            $controller = $route['admin']['path'] . 'AjaxController';
        }

        $controller = str_replace('/', '\\', $controller);

        $ajax = new $controller;
        $ajax->data = $data;

        return ($ajax->ajax());
    }

    protected function createAjaxData($data){
        $this->data = $data;
    }
}