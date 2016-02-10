<?php
	class anotationDocument{
	    var $name;
	    var $type;
	    var $links;
	   
	   	function __construct($name,$type){
		  	$this->name = $name;
			$this->type = $type;
	   	}

		function anadirLink($valor, $indice){
      		if (!isset($this->links)){
         		$this->links = array();
      		}
      		$this->links[$indice] = $valor;
   		}
	}
?>