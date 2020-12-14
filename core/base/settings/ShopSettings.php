<?php


namespace core\base\settings;

use core\base\controller\Singleton;
use core\base\settings\Settings;

//класс добавляет к общим настройкам настройки плагина (singleton)
class ShopSettings
{
    use Singleton;

    private $baseSettings;
    private $routes = [
      'plugins' => [
          'dir' => false,
          'routes' => [
                'product' => 'goods'

          ]
      ]
    ];
    private $templateArr = [
        'text' => ['name', 'phone', 'address', 'price', 'short'],
        'textarea' => ['content', 'keywords', 'goods_content']
    ];

    //функция возвращает любое запрошенное свойство этого класса
    static public function get($property){
        return self::getInstance() -> $property;
    }
    static private $_instance;

    static private function getInstance(){
        if (self::$_instance instanceof self){
            return self::$_instance;
        }
        //добавление настроек (свойств) другого класса

        self::instance()->baseSettings = Settings::instance();
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

}