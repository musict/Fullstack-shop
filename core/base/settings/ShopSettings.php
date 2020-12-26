<?php

namespace core\base\settings;

class ShopSettings
{
    use BaseSettings;

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
}