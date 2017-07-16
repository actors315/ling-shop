<?php

$config = require(__DIR__ . '/../shop/bootstrap/bootstrap.php');
(new \lingyin\web\Application($config))->run();