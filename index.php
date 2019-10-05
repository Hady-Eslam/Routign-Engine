<?php

use Core\Routing\FactoryEngine as RoutingFactory;

define('_APP_ROOT_', __DIR__);

require_once _APP_ROOT_.'/vendor/autoload.php';

$Router = RoutingFactory::Get_Router_Engine(
	_APP_ROOT_.'/Schema.php',
	'404.html',
	'403.html',
	explode('/',    explode('?', $_SERVER['REQUEST_URI'], 2)[0],    2)[1]
);

$Router->BeginRouting();
var_dump( $Router->GetResult() );
