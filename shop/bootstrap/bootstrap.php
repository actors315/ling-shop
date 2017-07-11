<?php
use lingyin\base\Ling;

/**
 * Created by PhpStorm.
 * User: huanjin
 * Date: 2017/7/11
 * Time: 22:09
 */
defined('ENVIRONMENT') || define('ENVIRONMENT', empty($_SERVER['ENV']) ? 'product' : $_SERVER['ENV']);

switch (ENVIRONMENT) {
    case 'dev' :
        error_reporting(-1);
        ini_set('display_errors', 1);
        break;

    case 'test' :
    case 'product' :
        ini_set('display_errors', 0);
        if (version_compare(PHP_VERSION, '5.3', '>=')) {
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        } else {
            error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
        }
        break;
    default :
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'The application environment is not set correctly.';
        exit(1);
}

require(__DIR__ . '/../../vendor/autoload.php');
$config = ling\base\Ling::loadConfig(__DIR__.'/../../common/config/web.php');
$config = ling\base\Ling::loadConfig(__DIR__ . '/../config/web.php');

return $config;