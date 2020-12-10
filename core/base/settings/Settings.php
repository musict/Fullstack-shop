<?php


namespace core\base\settings;

//класс настроек роутинга (singleton)
class Settings
{
    static private $_instance;

    private function __construct()
    {
    }
    private function __clone(){

    }
    static public function instance(){
        if (self::$_instance instanceof self){
            return self::$_instance;
        }
        return self::$_instance = new self;
    }


    private $routes = [
      'admin' => [
          'alias' => 'admin',
          'path' => 'core/admin/controller',
          'hrUrl' => false //человекопонятные ссылки
      ],
      'settings' => [
          'path' => 'core/base/settings'
      ],
      'plugins' => [
          'path' => 'core/plugins',
          'hrUrl' => false
      ],
      'user' =>[
          'path' => 'core/user/controller',
          'hrUrl' => true,
          'routes' => [

          ]
      ],
      'default' => [
          'controller' => 'IndexController',
          'inputMethod' => 'inputData',
          'outputMethod' => 'outputData'
      ]
    ];

    private $templateArr = [
        'text' => ['name', 'phone', 'address'],
        'textarea' => ['content', 'keywords']
    ];

    private $lalala = 'lalala';

    static public function get($property){
        return self::instance() -> $property;
    }

    //объединяет свойста нескольких классов настроек
    public function mergeProperties($class){
        $baseProperties = [];
        foreach ($this as $name => $item){
            $property = $class::get($name);
            if (is_array($property) && is_array($item)){
                $baseProperties[$name] = $this->arrayMergeRecursive($this->$name, $property);
                continue;
            }
            if (!$property) $baseProperties[$name] = $this->$name;
        }
        return $baseProperties;
    }

    //объединяет первый входящий массив с остальными входящими, повторы заменяет, отсутствущие добавляет
    public function arrayMergeRecursive()
    {
        $arrays = func_get_args();
        $baseArray = array_shift($arrays);

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_array($value) && is_array($baseArray[$key])) {
                    $baseArray[$key] = $this->arrayMergeRecursive($baseArray[$key], $value);
                } else {
                    if (is_int($key)) {
                        if (!in_array($value, $baseArray)) array_push($baseArray, $value);
                        continue;
                    }
                    $baseArray[$key] = $value;
                }
            }
        }
        return $baseArray;
    }
}