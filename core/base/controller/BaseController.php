<?php

namespace core\base\controller;

use core\base\exceptions\RouteException;
use core\base\settings\Settings;


abstract class BaseController {

    protected $controller;
    protected $inputMethod;
    protected $outputMethod;
    protected $parameters;
    protected $page;
    protected $errors;

    public function route(){
        $controller = str_replace('/', '\\', $this->controller);
        //динамическое подключение контроллеров расширение Reflection
        try {
            $object = new \ReflectionMethod($controller, 'request');
            $args = [
                'parameters' => $this->parameters,
                'inputMethod' => $this->inputMethod,
                'outputMethod' => $this->outputMethod
            ];
            $object->invoke(new $controller, $args);

        } catch (\ReflectionException $e){
            throw new RouteException($e->getMessage());
        }

    }

    public function request($args){
        $this->parameters = $args['parameters'];
        $inputData = $args['inputMethod'];
        $outputData = $args['outputMethod'];
        $data = $this->$inputData();
        if (method_exists($this, $outputData)) {
            $page = $this->$outputData($data);
            if ($page) {
                $this->page = $page;
            }
        } elseif ($data){
            $this->page = $data;
        }

        if ($this->errors){
            $this->writeLog();
        }

        $this->getPage();
    }
    //формируем страницу
    protected function render($path = '', $parameters = []) {
        //распаковка массива в переменные по ключам
        extract($parameters);
        if (!$path){

            $class = new \ReflectionClass($this);
            $space = str_replace('\\', '/',$class->getNamespaceName() . '\\');
            $routes = Settings::get('routes');
            if ($space === $routes['user']['path']){
                $template = TEMPLATE;
            }else {
                $template = ADMIN_TEMPLATE;
            }
            $path = $template . explode('controller', strtolower($class->getShortName()))[0];
        }
        //буфер обмена для доступа данных этого метода в шаблоне страницы
        ob_start();
        if (!@include_once $path . '.php'){
            throw new RouteException('Отсутствует шаблон - ' . $path);
        }
        return ob_get_clean();
    }

    protected function getPage(){
        if (is_array($this->page)){
            foreach ($this->page as $block) echo $block;
        } else{
            echo $this->page;
        }
        exit();
    }
}