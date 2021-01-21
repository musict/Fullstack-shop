<?php
error_reporting(0);
define('VG_ACCESS', true);

header('Content-Type:text/html; charset=utf-8');
session_start();

require_once 'config.php';
require_once 'core/base/settings/internal_settings.php';

use core\base\exceptions\RouteException;
use core\base\exceptions\DBException;
use core\base\controller\BaseRoute;

try{
    BaseRoute::routeDirection();
}
catch (RouteException | DBException $e){
    exit($e->getMessage());
}
