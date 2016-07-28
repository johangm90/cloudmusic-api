<?php

/**
* @Author: Johan Guerreros <johangm90@gmail.com>
* @License: Copyright (c) 2016. All Rights Reserved.
*/

require_once("vendor/autoload.php");

// Retrieve instance of the framework
$app = Base::instance();

// Initialize app
$app->config('app/config.ini');

// Define routes
$app->config('app/routes.ini');

// Execute application
$app->run();

?>
