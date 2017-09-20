<?php 
	namespace Home\Behaviors;
	class AuthCheckBehavior {
		public function run(&$param){
			$info = M('auth')->where(['userid'=>$param])->select();
			$funcode = [];
			$index = 0;
			foreach($info as $f){
				$funcode[$index] = $f['funcode'];
			}
			if(in_array("909210010013", $funcode)){
			    return 1;
			} else {
			   	return 2;
			}
			return $funcode;
		}
	}
?>