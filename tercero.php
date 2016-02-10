<?php
	class tercero{
	    var $links;
	    var $nif;
	    var $name;
	    var $relation;
		var $address;
		var $zone;
		var $country;
		var $province;
		var $zipCode;
		var $notificationChannel;
		var $personType;
		var $department;
		var $email;
		var $phone;
		var $fax;
		var $mobile;
		var $notes;
	   
	   	function __construct($nif,$name,$relation,$address,$zone,$country,$province,$zipCode,$notificationChannel,$personType,$department,$email,$phone,$fax,$mobile,$notes){
		  	$this->nif = $nif;
		  	$this->name = $name;
			$this->relation = $relation;
			$this->address = $address;
			$this->zone = $zone;
			$this->country = $country;
			$this->province = $province;
			$this->zipCode = $zipCode;
			$this->notificationChannel = $notificationChannel;
			$this->personType = $personType;
			$this->department = $department;
			$this->email = $email;
			$this->phone = $phone;
			$this->fax = $fax;
			$this->mobile = $mobile;
			$this->notes = $notes;
	   	}

		function anadirLink($valor, $indice){
      		if (!isset($this->links)){
         		$this->links = array();
      		}
      		$this->links[$indice] = $valor;
   		}
	}
?>