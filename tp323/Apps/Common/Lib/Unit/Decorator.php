<?php

abstract class Beverage{
	public $_name;
	abstract public function cost();
}

class Coffee extends Beverage{
	public function __construct(){
		$this->_name = 'Coffee';
	}
	public function cost(){
		return 10.00;
	}
}

class Milk extends  Beverage{
	private $_beverage;
	private static $_instance;
	static function getInstance($beverage){
		if(self::$_instance == null){
			self::$_instance = new self($beverage);
		}
		return self::$_instance;
	}
	
	private function __construct($beverage){
		$this->_name = 'Milk';
		if($beverage instanceof Beverage){
			$this->_beverage = $beverage;
		}else{
			exit('Failure');
		}
	}
	
	public function cost(){
		return $this->_beverage->cost() + 5.00;
	}
}

class Sugar extends Beverage{
	private $_beverage;
	private static $_instance;
	function getInstance($beverage){
		if(self::$_instance == null){
			self::$_instance = new self($beverage);
		}
		return self::$_instance;
	}
	private function __construct($beverage){
		$this->_name = 'Sugar';
		if($beverage instanceof Beverage){
			$this->_beverage = $beverage;
		}else{
			exit('Failure');
		}
	}
	
	public function cost(){
		return $this->_beverage->cost() + 3.00;
	}
}
