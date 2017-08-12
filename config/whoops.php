<?php

/* Make sure that the Whoops directory is set in your PHP includes path */
$path = __DIR__ . '/../vendor/filp/whoops/src/';

require_once($path . 'Whoops/Run.php');
require_once($path . 'Whoops/Handler/HandlerInterface.php');
require_once($path . 'Whoops/Handler/Handler.php');
require_once($path . 'Whoops/Handler/PrettyPageHandler.php');
require_once($path . 'Whoops/Handler/JsonResponseHandler.php');
require_once($path . 'Whoops/Exception/ErrorException.php');
require_once($path . 'Whoops/Exception/Inspector.php');
require_once($path . 'Whoops/Exception/Frame.php');
require_once($path . 'Whoops/Exception/FrameCollection.php');

$run = new \Whoops\Run;
$handler = new \Whoops\Handler\PrettyPageHandler;
$JsonHandler = new \Whoops\Handler\JsonResponseHandler;

$run->pushHandler($JsonHandler);
$run->pushHandler($handler);
$run->register();
