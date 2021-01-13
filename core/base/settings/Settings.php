<?php


namespace core\base\settings;

//класс настроек роутинга (singleton)
use core\base\controller\Singleton;

class Settings
{
    use Singleton;

    private $routes = [
      'admin' => [
          'alias' => 'admin',
          'path' => 'core/admin/controller/',
          'hrUrl' => false, //человекопонятные ссылки
          'routes' => [

          ]
      ],
      'settings' => [
          'path' => 'core/base/settings/'
      ],
      'plugins' => [
          'path' => 'core/plugins/',
          'hrUrl' => false,
          'dir' => false
      ],
      'user' =>[
          'path' => 'core/user/controller/',
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

    private $formTemplates = PATH . 'core/admin/view/include/form_templates/';

    private $expansion = 'core/admin/expansion/';

    private $messages = 'core/base/messages/';

    private $defaultTable = 'goods';

    private $projectTables = [
        'goods' => ['name' => 'Товары', 'img' => 'pages.png'],
        'filters' => ['name' => 'Фильтры', 'img' => 'pages.png']
    ];

    private $templateArr = [
        'text' => ['name'],
        'textarea' => ['content', 'keywords'],
        'radio' => ['visible'],
        'checkboxlist' => ['filters'],
        'select' => ['menu_position', 'parent_id'],
        'img' => ['img'],
        'gallery_img' => ['gallery_img']
    ];

    private $translate = [
        'name' => ['Название', 'Не более 100 символов'],
        'keywords' => ['Ключевые слова', 'Не более 70 символов'],
        'content' => []
    ];

    private $radio = [
      'visible' => ['Нет', 'Да', 'default' => 'Да']
    ];

    private $rootItems = [
        'name' => 'Корневая',
        'tables' => ['articles', 'filters']
    ];

    private $manyToMany = [
        'goods_filters' => ['goods', 'filters'] // 'type' => 'child' || 'root'
    ];

    private $blockNeedle = [
        'vg-rows' => [],
        'vg-img' => ['img'],
        'vg-content' => ['content']
    ];

    private $validation = [
        'name' => ['empty' => true, 'trim' => true],
        'price' => ['int' => true],
        'login' => ['empty' => true, 'trim' => true],
        'password' => ['crypt' => true, 'empty' => true],
        'keywords' => ['count' => 70, 'trim' => true],
        'description' => ['count' => 160, 'trim' => true]
    ];


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