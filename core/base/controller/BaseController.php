<?php

namespace core\base\controller;

use core\base\exceptions\RouteException;

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
        //метод принимает входящие данные и отправляет на обработку
        $this->$inputData();
        //собираем в переменную обработанные данные
        $this->page = $this->$outputData();
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
            $path = TEMPLATE . explode('controller', strtolower((new \ReflectionClass($this))->getShortName()))[0];
        }
        //буфер обмена для доступа данных этого метода в шаблоне страницы
        ob_start();
        if (!@include_once $path . '.php'){
            throw new RouteException('Отсутствует шаблон - ' . $path);
        }
        return ob_get_clean();
    }

    protected function getPage(){
        exit($this->page);
    }
}