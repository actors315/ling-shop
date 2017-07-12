<?php

$config = require(__DIR__ . '/../shop/bootstrap/bootstrap.php');
(new \lingyin\web\Application($config))->run();
print_r(\lingyin\base\Ling::$app->id);