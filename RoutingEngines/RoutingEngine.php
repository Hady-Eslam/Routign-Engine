<?php

namespace Core\Routing;
use Core\Exceptions\RoutingExceptionsEngine;

class RoutingEngine{

	private const BOOL_EXP = '/^<bool(:(.+))?>$/';
	private const INTEGER_EXP = '/^<int(\((\d+)\))?(:(.+))?>$/';
	private const STRING_EXP = '/^<string(\((\d+)\))?(:(.+))?>$/';
	private const DOUBLE_EXP = '/^<double(\((\d+)(\,(\d+))?\))?(:(.+))?>$/';

	private $Schema;
	private $URL;
	private $Page_404;
	private $Page_403;
	private $Result;
	private $Values = [];
	private $Path = '';
	
	function __construct( Array $Schema, String $Page_404, String $Page_403, String $URL ){
		$this->Schema = $Schema;
		$this->URL = $URL;
		$this->Page_404 = $Page_404;
		$this->Page_403 = $Page_403;
	}

	function BeginRouting(){
		$this->Result = $this->GetPath();
	}

	private function GetPath(){

		while (True) {

			$URLPart = explode('/', $this->URL, 2);
			$Matched = False;
			$Matched_Value = NULL;
			
			foreach ($this->Schema as $Key => $Value) {
				$Key = strval($Key);
			
				if ( $Key === '404' )
					$this->Put404Page($Value);

				else if ( $Key === '403' )
					$this->Put403Page($Value);

				// Check BOOLEAN
				else if ( preg_match( self::BOOL_EXP, $Key, $Result) ){
					if ( $this->HANDLE_BOOL($Result, $URLPart[0]) ){
						$Matched = True;
						$Matched_Value = $Value;
						break;
					}
				}

				// Check INT
				else if ( preg_match( self::INTEGER_EXP, $Key, $Result) ){
					if ( $this->HANDLE_INT($Result, $URLPart[0]) ){
						$Matched = True;
						$Matched_Value = $Value;
						break;
					}
				}

				// Check STRING
				else if ( preg_match( self::STRING_EXP, $Key, $Result) ){
					if ( $this->HANDLE_STRING($Result, $URLPart[0]) ){
						$Matched = True;
						$Matched_Value = $Value;
						break;
					}
				}

				// Check DOUBLE
				else if ( preg_match( self::DOUBLE_EXP, $Key, $Result) ){
					if ( $this->HANDLE_DOUBLE($Result, $URLPart[0]) ){
						$Matched = True;
						$Matched_Value = $Value;
						break;
					}
				}
				
				else if ( $Key == $URLPart[0] ){
					$Matched = True;
					$Matched_Value = $Value;
					break;
				}
			}
			
			if ( !$Matched )
				//return $this->ErrorPage;
				return ['NotFound'];
			else{
				if ( sizeof($URLPart) == 1 ){
					if ( is_string($Matched_Value ) )
						//return $Matched_Value;
						return ['Found', $Matched_Value];
					
					else if ( array_key_exists('', $Matched_Value) )
						//return $Matched_Value[''];
						return ['Found', $Matched_Value['']];

					//return $this->ErrorPage;
					return ['NotFound'];
				}
				else
					if ( is_string($Matched_Value) )
						//return $this->ErrorPage;
						return ['NotFound'];
			}
			$this->Schema = $Matched_Value;
			$this->URL = $URLPart[1];
		}
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////

	private function HANDLE_INT($Result, $URL){
		
		if ( preg_match('/^\d+$/', $URL) ){
			
			if ( sizeof($Result) === 1 || sizeof($Result) === 3 && strlen($URL) <= (int)$Result[2] ){
				array_push($this->Values, $URL);
				return True;
			}

			else if ( sizeof($Result) === 5 && ( $Result[2] === '' || strlen($URL) <= (int)$Result[2] ) ){
				$this->Values[$Result[4]] = $URL;
				return True;
			}
		}

		return False;
	}

	private function HANDLE_STRING($Result, $URL){
		
		if ( sizeof($Result) === 1 || sizeof($Result) === 3 && strlen($URL) <= (int)$Result[2]){
			array_push($this->Values, $URL);
			return True;
		}

		else if ( sizeof($Result) === 5 && 
			( $Result[2] === '' || $Result[2] !== '' && strlen($URL) <= (int)$Result[2] ) ){
				
				$this->Values[$Result[4]] = $URL;
				return True;
		}

		return False;
	}

	private function HANDLE_DOUBLE($Result, $URL){

		if ( sizeof($Result) === 1 && preg_match('/^(\d+)(\.(\d+))?$/', $URL) ||
			 sizeof($Result) === 3 && preg_match('/^(\d{0,'.$Result[2].'})(\.(\d+))?$/', $URL) ||
			 sizeof($Result) === 5 &&
			 	preg_match('/^(\d{0,'.$Result[2].'})(\.(\d{0,'.$Result[4].'}))?$/', $URL) ){

			array_push($this->Values, $URL);
			return True;
		}

		else if ( sizeof($Result) === 7 && (
			$Result[2] === '' && $Result[4] === '' ||
			$Result[2] !== '' && $Result[4] === '' && 
				preg_match('/^(\d{0,'.$Result[2].'})(\.(\d+))?$/', $URL) ||
			
			$Result[2] !== '' && $Result[4] !== '' &&
				preg_match('/^(\d{0,'.$Result[2].'})(\.(\d{0,'.$Result[4].'}))?$/', $URL)
		)){
			$this->Values[$Result[6]] = $URL;
			return True;
		}

		return False;
	}

	private function HANDLE_BOOL($Result, $URL){

		if ( !preg_match('/^([01]|[tT]rue|[fF]alse)$/', $URL) )
			return False;

		( sizeof($Result) === 1 ) ? array_push($this->Values, $URL) : $this->Values[$Result[2]] = $URL;
		return True;
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////


	private function Put404Page($Value){
		if ( !is_string($Value) )
			throw new RoutingExceptionsEngine('404 Path Must Be String');
		
		$this->Page_404 = $Value;
	}

	private function Put403Page($Value){
		if ( !is_string($Value) )
			throw new RoutingExceptionsEngine('403 Path Must Be String');
		
		$this->Page_403 = $Value;
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////

	function GetResult(){
		
		return new RoutingResultEngine(
			( $this->Result[0] == 'NotFound' ) ?
				[
					'Result' => 'NotFound',
					'404' => $this->Page_404,
					'403' => $this->Page_403,
					'Data' => [
						'Path' => '',
						'Values' => $this->Values
					]
				] 
				: 
				[
					'Result' => 'Found',
					'404' => $this->Page_404,
					'403' => $this->Page_403,
					'Data' => [
						'Path' => $this->Result[1],
						'Values' => $this->Values
					]
				]
		);
	}
}