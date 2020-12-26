<?php


namespace core\base\settings;

use core\base\controller\Singleton;

trait BaseSettings
{
    use Singleton {
        instance as SingletonInstance;
    }

    private $baseSettings;

    static public function get($property){
        return self::instance() -> $property;
    }

    static public function instance(){
        if (self::$_instance instanceof self){
            return self::$_instance;
        }
        //добавление настроек (свойств) другого класса

        self::SingletonInstance()->baseSettings = Settings::instance();
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