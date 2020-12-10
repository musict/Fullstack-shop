<?php


namespace core\base\settings;

use core\base\settings\Settings;

//класс добавляет к общим настройкам настройки плагина (singleton)
class ShopSettings
{
    private $baseSettings;
    private $routes = [
      'admin' => [
          'name' => 'sudo',
      ],
      'vasya' => [
          'name' => 'vasya'
      ]
    ];
    private $templateArr = [
        'text' => ['name', 'phone', 'address', 'price', 'short'],
        'textarea' => ['content', 'keywords', 'goods_content']
    ];

    //функция возвращает любое запрошенное свойство этого класса
    static public function get($property){
        return self::instance() -> $property;
    }
    static private $_instance;

    static public function instance(){
        if (self::$_instance instanceof self){
            return self::$_instance;
        }
        //добавление настроек (свойств) другого класса
        self::$_instance = new self;
        self::$_instance->baseSettings = Settings::instance();
        $baseProperties = self::$_instance->baseSettings->mergeProperties(get_class());
        self::$_instance->setProperty($baseProperties);
        return self::$_instance;
    }

    protected function setProperty($properties){
        if ($properties){
            foreach ($properties as $name => $property){
                $this->$name = $property;
            }
        }
    }

    private function __construct()
    {
    }
    private function __clone(){

    }



}