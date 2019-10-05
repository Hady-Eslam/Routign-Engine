<?php

namespace Core\Routing;
use Core\Routing\RoutingEngine;
use Core\Exceptions\RoutingExceptionsEngine;

class FactoryEngine{

    public static function Get_Router_Engine( 
		String $Schema_File, 
		String $Page_404, 
		String $Page_403, 
		String $URL ){

		if ( !file_exists( $Schema_File ) )
			throw new RoutingExceptionsEngine('Schema File Not Found');

        return new RoutingEngine(
			include_once $Schema_File,
			$Page_404,
			$Page_403,
			$URL
		);
    }
}
