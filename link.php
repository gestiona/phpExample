<?php
	class link{
	    var $rel;
	    var $href;
	   
	   	function __construct($rel,$href){
		  	$this->rel = $rel;
		  	$this->href = $href;
	   	}

		function set($data) {
        	foreach ($data AS $key => $value) $this->{$key} = $value;
    	}
	}
?>