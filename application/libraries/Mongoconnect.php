<?php
class Mongoconnect {
    
    public function DataBase() {
     	
  	$MongoDB  = new Mongo("mongodb://chikoo:chikoo123@troup.mongohq.com:10042/chikoo");
 	$DataBase = $MongoDB->{"chikoo"};
	
    return $DataBase; 
	
    }
} ?>
