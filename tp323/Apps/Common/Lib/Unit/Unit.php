<?php 
require 'Person.php';
class Unit{
	private static $instance;
	private static $clazz;
	function __construct(){
		//建立 Person这个类的反射类  
		self::$clazz = new ReflectionClass('Person');
		//相当于实例化Person 类  
		self::$instance = self::$clazz->newInstanceArgs();
	}
	
	public function getPro(){
		//获取所有属性
		$properies = self::$clazz->getProperties();
		var_dump($properies);
		foreach($properies as $pro){
			echo $pro->getName()."<br/>";
		}
		
		//指定获取 属性类型ReflectionProperty::IS_PRIVATE,ReflectionProperty::IS_PROTECTED
		//ReflectionProperty::IS_PUBLIC,ReflectionProperty::IS_STATIC
		//如果要同时获取public 和private 属性，就这样写：ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED。
		//通过$property->getName()可以得到属性名。
		$private_pro = self::$clazz->getProperties(ReflectionProperty::IS_PRIVATE);
		echo $private_pro[0]->getName()."<br/>";
		print_r("#####################");
	}
	
	public function getDocCom(){
		//获取所有属性
		$properies = self::$clazz->getProperties();
		var_dump($properies);
		foreach($properies as $property) {   
			if($property->isProtected()) {   
			 	$docblock = $property->getDocComment();   
			   	preg_match('/ type\=([a-z_]*) /', $property->getDocComment(), $matches);   
			 	echo $matches[1]."\n";   
			}   
 		} 
	}
	
	public function getMethod(){
		//获取所有方法
		$methods = self::$clazz->getMethods();
		var_dump($methods);
		//执行getName()方法
		$name = self::$instance->getName();
		echo $name."<br>";
		//或者
		$m = self::$clazz->getmethod('getName');//获取Person类中的getName方法
		$result = $m->invoke(self::$instance);//执行getName方法
		echo $result;
	}
	
	
}


