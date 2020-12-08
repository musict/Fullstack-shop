<?php
defined('VG_ACCESS') or die('Access denied');
const TEMPLATE = 'templates/default/'; //шаблоны пользователей
const ADMIN_TEMPLATES = 'core/admin/views/'; //шаблоны администратора
const COOKIE_VERSION = '1.0.0'; //для сброса авторизации всех пользователей
const CRYPT_KEY = ''; //ключ шифрования
const COOKIE_TIME = 60; //для установки времени бездействия администратора
const BLOCK_TIME = 3; //время блокировки при неправильном вводе пароля
const QTY = 8; // количество товаров на странице
const QTY_LINKS = 3; // колличество ссылок на странице
const ADMIN_CSS_JS = [
    'styles' => [],
    'scripts' => []
];
const USER_CSS_JS = [
  'styles' => [],
  'scripts' => []
];

