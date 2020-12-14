<?php

namespace core\base\controller;
use core\base\exceptions\RouteException;
use core\base\settings\Settings;

//Главный контроллер (singleton)
class RouteController extends BaseController
{
    use Singleton;
    protected $routes;

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
            if (!$this->routes) throw new RouteException('Отсутствуют маршруты в базовых настройках', 1);
            $url = explode('/', substr($address_str, strlen(PATH)));

            //если заходят в админку
            if ($url[0] && $url[0] === $this->routes['admin']['alias']){
                array_shift($url);
                //если после admin/ идет название плагина, смотрим есть ли такая папка для его подключения
                if ($url[0] && is_dir($_SERVER['DOCUMENT_ROOT'] . PATH . $this->routes['plugins']['path'] . $url[0])){
                    //берем название плагина из адреса
                    $plugin = array_shift($url);
                    //ищем настройки для данного плагина
                    $pluginSettings = $this->routes['settings']['path'] . ucfirst($plugin . "Settings");
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . PATH . $pluginSettings . '.php')){
                        $pluginSettings = str_replace('/', '\\', $pluginSettings);
                        $this->routes = $pluginSettings::get('routes');
                    }
                    $dir = $this->routes['plugins']['dir'] ? '/' . $this->routes['plugins']['dir'] . '/' : '/';
                    $dir = str_replace('//', '/', $dir);
                    $this->controller = $this->routes['plugins']['path'] . $plugin . $dir;
                    $hrUrl = $this->routes['plugins']['hrUrl'];
                    $route = 'plugins';
                } else{
                    $this->controller = $this->routes['admin']['path'];
                    $hrUrl = $this->routes['admin']['hrUrl'];
                    $route = 'admin';
                }

            } else{
                $hrUrl = $this->routes['user']['hrUrl'];
                $this->controller = $this->routes['user']['path'];
                $route = 'user';
            }
            $this->createRoute($route, $url);
            //проверяем наличие параметров в адресе
            if ($url[1]){
                $count = count($url);
                $key = '';

                if (!$hrUrl){
                    $i = 1;
                } else{
                    $this->parameters['alias'] = $url[1];
                    $i = 2;
                }
                //проходим по массиву адреса, находим ключи и значения
                for (; $i < $count; $i++){
                    //добавляем ключ
                    if (!$key){
                        $key = $url[$i];
                        $this->parameters[$key] = '';
                    }
                    //добавляем значение
                    else{
                        $this->parameters[$key] = $url[$i];
                        $key = '';
                    }

                }
            }
        } else{
            throw new RouteException('Некорректная директория сайта', 1);

        }
    }
    private function createRoute($var, $arr){
        $route = [];
        if (!empty($arr[0])){
            //если в маршрутах есть такой алиас - передаем управление соответствующему контроллеру
            if ($this->routes[$var]['routes'][$arr[0]]){

                $route = explode('/', $this->routes[$var]['routes'][$arr[0]]);
                $this->controller .= ucfirst($route[0] . 'Controller');
            }
            else{
                $this->controller .= ucfirst($arr[0] . 'Controller');
            }
        //иначе используем контроллер по умолчанию
        } else{
            $this->controller .= $this->routes['default']['controller'];
        }
        $this->inputMethod = $route[1] ? $route[1] : $this->routes['default']['inputMethod'];
        $this->outputMethod = $route[2] ? $route[2] : $this->routes['default']['outputMethod'];

    }
}