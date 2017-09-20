<?php
class Person {
	private $_allowDynamicAttributes = false;
	//用户ID
	protected $id = 1;
	//用户姓名
	protected $name = "lijl";
	//身份证号码
	protected $idcard = "111111111111111";
	public function getId() {
		return $this -> id;
	}

	public function setId($v) {
		$this -> id = $v;
	}

	public function getName() {
		return $this -> name;
	}

	public function setName($v) {
		$this -> name = $v;
	}

	public function getIdcard() {
		return $this -> idcard;
	}

	public function setIdcard($v) {
		$this -> idcard = $v;
	}

}
