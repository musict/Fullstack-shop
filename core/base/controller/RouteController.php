<?php

namespace core\base\controller;
use core\base\exceptions\RouteException;
use core\base\settings\Settings;
use core\base\settings\ShopSettings;

//Главный контроллер (singleton)
class RouteController
{
    static private $_instance;

    protected $routes;
    protected $controller;
    protected $inputMethod;
    protected $outputMethod;
    protected $parameters;

    private function __clone(){}

    static public function getInstance(){
        if (self::$_instance instanceof self){
            return self::$_instance;
        }
        return self::$_instance = new self;
    }

    private function __construct(){
        $address_str = $_SERVER['REQUEST_URI'];
        //убираем слеш, чтобы не дублировались страницы
        if(strrpos($address_str, '/') === strlen($address_str) -1 && strrpos($address_str, '/') !== 0){
            $this->redirect(rtrim($address_str, '/'), 301);
        }
        $path = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], 'index.php'));
        if ($path === PATH){
            $this->routes = Settings::get('routes');
            //если не получили настройки
            if (!$this->routes) throw new RouteException('Ведутся технические работы');
            //если заходят в админку
            if (strrpos($address_str, $this->routes['admin']['alias']) == strlen(PATH)){
                //todo
            } else{
                $url = explode('/', substr($address_str, strlen(PATH)));
                $hrUrl = $this->routes['user']['hrUrl'];
                $this->controller = $this->routes['user']['path'];
                $route = 'user';
            }
            $this->createRoute($route, $url);
            exit();
        } else{
            try {
                throw new \Exception('Некорректная директория сайта');
            } catch (\Exception $e){
                exit($e->getMessage());
            }
        }
    }
    private function createRoute($var, $arr){
        $route = [];
        if (!empty($arr[0])){
            //если в маршрутах есть такой алиас - передаем управление соответствующему контроллеру
            if ($this->routes[$var]['routes'][$arr[0]]){

                $route = explode('/', $this->routes[$var]['routes'][$arr[0]]);
                $this->controller .= ucfirst($route[0].'Controller');
            }
            else{
                $this->controller .= ucfirst($arr[0].'Controller');
            }
        //иначе используем контроллер по умолчанию
        } else{
            $this->controller .= $this->routes['default']['controller'];
        }
        $this->inputMethod = $route[1] ? $route[1] : $this-$this->routes['default']['inputMethod'];
        $this->outputMethod = $route[2] ? $route[2] : $this-$this->routes['default']['outputMethod'];

    }
}