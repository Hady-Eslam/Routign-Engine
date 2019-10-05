<?php

namespace Core\Routing;

class RoutingResultEngine{

    private $Result = [];

    function __construct( Array $Result ){
        $this->Result = $Result;
    }

    function Page_404(){
        return $this->Result['404'];
    }

    function Page_403(){
        return $this->Result['403'];
    }

    function is_Resource_Found(){
        return ( $this->Result['Result'] == 'Found' ) ? True : False;
    }

    function Path(){
        return $this->Result['Data']['Path'];
    }

    function Values(){
        return $this->Result['Data']['Values'];
    }

    function Data(){
        return $this->Result['Data'];
    }
}
