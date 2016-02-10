<?php
	class oficinaRegistro{
		var $links;
	    var $code;
	    var $name;
	   
	   	function __construct($code,$name){
		  	$this->code = $code;
		  	$this->name = $name;
	   	}

		function anadirLink($valor, $indice){
      		if (!isset($this->links)){
         		$this->links = array();
      		}
      		$this->links[$indice] = $valor;
   		}

		function set($data) {
        	foreach ($data AS $key => $value) $this->{$key} = $value;
    	}
	}
?>