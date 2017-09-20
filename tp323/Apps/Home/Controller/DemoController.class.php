<?php
namespace Home\Controller;
use Home\Model\RedisModel;
class DemoController extends \Think\Controller {
	//类反射
	public function index(){
		require APP_PATH . 'Common/Lib/Unit/Unit.php';
		$unit = new \Unit();
		//$unit -> getPro();
		$unit -> getDocCom();
		//$unit -> getMethod();
	}

	//装饰者设计模式
	public function index1(){
		require APP_PATH . 'Common/Lib/Unit/Decorator.php';
		
		//来杯咖啡
		$coffee = new \Coffee();
		printf($coffee->_name."::".$coffee->cost());//输出价格
		echo "<br>";
		//加点奶
		$milk =  \Milk::getInstance($coffee);
		printf($milk->_name."::".$milk->cost());//输出价格
		echo "<br>";
		
		
		//加点奶
		$milk1 =  \Milk::getInstance($coffee);
		printf($milk->_name."::".$milk->cost());//输出价格
		echo "<br>";
		
		if($milk1 == $milk){
			echo "same object<br>";
		}else{
			echo "not same object<br>";
		}
		
		
		//加点糖
		$sugar =  \Sugar::getInstance($milk);
		printf($sugar->_name."::".$sugar->cost());//输出价格
		
	}
	
	public function index2(){
		$a = authcode('lijianlin', 'ENCODE', '1234567',10);
		echo $a;
	}
	
	//调用存储过程
	public function index3(){
		$m = new \Think\Model();
		$info = $m->query('call test_prod(1)');
		var_dump($info);
	}
	
	public function index4(){
		dbexport();
	}
	
	public function index5(){
		$rds = new RedisModel();
		echo("<meta charset='UTF-8'>");	
		$flag = I('get.flag');
		
		if($flag == 0){
			echo "调用短信接口！<br/>";
			$yzm = mt_rand(100000,999999);
			$rds->set("test", $yzm,0,0,60,0);
			echo "生成验证码！<br/>";
			echo "发送短信！<br/>";
		}else{
			if(empty($rds->get("test"))){
				echo "验证码过期，请重新发送<br/>";
			}else{
				echo "验证通过<br/>";
				echo $rds->get("test");
			}
		}
		
		$arList = $rds->keys("t*"); 
		echo "Stored keys in redis:: " ;
		print_r($arList); 
	}

	public function index6(){
		$redis = new \Redis();
		$redis->connect('192.168.20.53',6379);
		$redis->set("test","lijianlin");
		$redis->get("test");
	}
	
	/********模拟短信登录************/
	
	//界面
	public function applogin(){
		$this->assign('waitTime','');
		$this->display('Demo/app/applogin');
	}
	//获取短信验证码
	public function appyzm(){
		$phone = I('post.phone');
		
		$rds = new RedisModel();
		$yzm = mt_rand(100000,999999);
		$rds->set($phone, $yzm,0,0,60,0);
		//$this->assign('waitTime',60);
		echo json_encode(['result'=>1,"wtime"=>60]);
		
	}
	
	//登录动作
	public function applogindone(){
		$phone = I('post.phone');
		$yzm = I('post.yzm');
		$rds = new RedisModel();
		$rds_yzm = $rds->get($phone);
		$result = [];
		if(empty($rds_yzm)  ){
			$result['result'] = 2;
		}else if($rds_yzm != $yzm){
			$result['result'] = 3;
		}else{
			$result['result'] = 1;
		}
		
		echo json_encode($result);
	}
	
	/********测试************/
	public function test(){
		$data['t1'] = 'tttt1';
		$data['t2'] = 'tttt2'; 
		$data['t3'] = 'tttt3'; 
		$data['t5'] = date('Y-m-d H:i:s',time()); 
		$id = M('test')->data($data)->add();
		echo $id;
	}
	
	
	
	
}
	