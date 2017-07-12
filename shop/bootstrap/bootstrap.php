<?php
use lingyin\base\Ling;
use lingyin\helpers\ArrayHelper;

/**
 * Created by PhpStorm.
 * User: huanjin
 * Date: 2017/7/11
 * Time: 22:09
 */

require(__DIR__ . '/../../vendor/autoload.php');
$config = require(__DIR__ . '/../../vendor/lingsoft/lingyin/bootstrap/bootstrap.php');

$config = ArrayHelper::merge($config, Ling::loadConfig(__DIR__ . '/../../common/config/web.php'));
$config = ArrayHelper::merge($config, Ling::loadConfig(__DIR__ . '/../config/web.php'));

return $config;