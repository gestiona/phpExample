<?php
	class anotacion{
	    var $links;
	    var $code;
	    var $date;
	    var $state;
		var $originDate;
		var $originCode;
		var $originOrganization;
		var $originRegistryOffice;
		var $shortDescription;
		var $longDescription;
		var $classification;
		var $incomeType;
		var $deliveryType;
		var $type;
		var $annulledDate;
		var $annulledReason;
		var $category;
	   
	   	function __construct($code,$date,$state,$originDate,$originCode,$originOrganization,$originRegistryOffice,$shortDescription,$longDescription,$classification,$incomeType,$deliveryType,$type,$annulledDate,$annulledReason,$category){
		  	$this->code = $code;
		  	$this->date = $date;
			$this->state = $state;
			$this->originDate = $originDate;
			$this->originCode = $originCode;
			$this->originOrganization = $originOrganization;
			$this->originRegistryOffice = $originRegistryOffice;
			$this->shortDescription = $shortDescription;
			$this->longDescription = $longDescription;
			$this->classification = $classification;
			$this->incomeType = $incomeType;
			$this->deliveryType = $deliveryType;
			$this->type = $type;
			$this->annulledDate = $annulledDate;
			$this->annulledReason = $annulledReason;
			$this->category = $category;
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