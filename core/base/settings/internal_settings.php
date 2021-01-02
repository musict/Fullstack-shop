<?php
defined('VG_ACCESS') or die('Access denied'); //переменная безопасности

const TEMPLATE = 'templates/default/'; //шаблоны пользователей
const ADMIN_TEMPLATE = 'core/admin/view/'; //шаблоны администратора
const UPLOAD_DIR = 'userfiles/';
const COOKIE_VERSION = '1.0.0'; //для сброса авторизации всех пользователей
const CRYPT_KEY = ''; //ключ шифрования
const COOKIE_TIME = 60; //для установки времени бездействия администратора
const BLOCK_TIME = 3; //время блокировки при неправильном вводе пароля
const QTY = 8; // количество товаров на странице
const QTY_LINKS = 3; // колличество ссылок на странице
const ADMIN_CSS_JS = [
    'styles' => ['css/main.css'],
    'scripts' => ['js/frameworkfunctions.js', 'js/scripts.js']
];
const USER_CSS_JS = [
  'styles' => [],
  'scripts' => []
];

use core\base\exceptions\RouteException;

function autoloadMainClasses($class_name){
    $class_name = str_replace('\\', '/', $class_name);
    if (!@include_once $class_name . '.php'){
        throw new RouteException('Неверное имя файла для подключения - ' . $class_name);
    }
}
spl_autoload_register('autoloadMainClasses');