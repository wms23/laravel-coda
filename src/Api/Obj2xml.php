<?php

namespace PhpCoda\LaravelCoda\Api;

class Obj2xml {
    var $xmlResult;
    
    function __construct($rootNode){
        $this->xmlResult = new SimpleXMLElement("<$rootNode></$rootNode>");
    }
    
    private function iteratechildren($object,$xml){
        foreach ($object as $name=>$value) {
        	if ($value != null) {
	            if (is_string($value) || is_numeric($value)) {
	                $xml->$name=$value;
	            } else {
	                $xml->$name=null;
	                $this->iteratechildren($value,$xml->$name);
	            }
        	}
        }
    }
    
    function toXml($object) {
        $this->iteratechildren($object,$this->xmlResult);
        return $this->xmlResult->asXML();
    }
}
?>