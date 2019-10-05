<?php

namespace Core\Exceptions;

class RoutingExceptionsEngine{
	
	function __construct($Message){
		echo 'Routing Exception : <br>'.$Message;
		var_dump( debug_backtrace() );
		exit();
	}
}
