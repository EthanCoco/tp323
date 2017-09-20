<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\RedisModel;
class LoginController extends Controller {
	//登录页面
	public function login(){
		//判断是否登录，如果已经登录 直接跳到主页
		if(session(C('IS_LOGIN'))) 
			redirect(U('Manage/index'));
		$this -> display();
	}
    
	//调用生产验证码操作
	public function code(){
		verifyCode();
	}
	
	// 请求检测登陆
	public function islogin(){
		if(session(C('IS_LOGIN'))) 
			die(json_encode(['result'=>'1']));
		$loginform = I("post.LoginForm");
		$username = $loginform['username'];
		$password = $loginform['password'];
		$verify = $loginform['verify'];
		//查询加密字符
		$chars = M()->table('__USER__ a')
            		->join('LEFT JOIN __CHARUSER__ c ON a.id=c.id')
            		->where(['a.username'=>trim($username), 'a.status'=>1,'c.type'=>0])
            		->getField('chars');
		
		//没有查到 说明用户不存在
        if(!$chars){
            die(json_encode(['result'=>L('HOME_CTRL_LOGIN_ISLOGIN_MSG1')]));
        }
		
		//查询用户登录
        $res = M('user')->field('id,username,status,name,usertype,idcard,phone,userimageurl,usersex')
        				->where(['username' => $username,'password' => md5Encrypt(trim($password), $chars),'status' => 1])
        				->find();
		
		if($res){
			//验证码验证
			if(!checkcode($verify)){
				die(json_encode(['result'=>L('HOME_CTRL_LOGIN_ISLOGIN_MSG2')]));
			}
        	//更新用户登录信息
            $lastData = [
                'last_time' => time(),
                'last_ip'   => get_client_ip()
            ];
            M('user')->where($where)->save($lastData);
			
			//设置session信息
            session(C('IS_LOGIN'), $res['id']);
			session(C('USERNAME'), $res['username']);
            session(C('USERREALNAME'), $res['name']);
			session(C('USERTYPE'), $res['usertype']);
			session(C('IDCARD'), $res['idcard']);
			session(C('PHONE'), $res['phone']);
			session(C('USERSEX'), $res['usersex']);
			session(C('USERIMAGEURL'), empty($res['userimageurl'])? __ROOT__.'/Public/Home/Images/user-default.jpg' : $res['userimageurl']  );
			writeSysLog(1,'登录','Login');
			die(json_encode(['result'=>'1']));
        }else{
            die(json_encode(['result'=>L('HOME_CTRL_LOGIN_ISLOGIN_MSG3')]));
        }
	}
		
	//退出 销毁session 跳转到登录页面
	public function logout(){
		session_destroy();
        redirect('Login/login');
	}
	
	//锁定屏幕
	public function lockscreen(){
		$this->display();
	}
	
	//解锁
	public function unlock(){
		//用户名唯一
		$username = session(C('USERNAME'));
		
		$loginform = I("post.LoginForm");
		$password = $loginform['password'];
		
		//查询加密字符
		$chars = M()->table('__USER__ a')
            		->join('LEFT JOIN __CHARUSER__ c ON a.id=c.id')
            		->where(['a.username'=>trim($username), 'a.status'=>1,'c.type'=>0])
            		->getField('chars');
		//查询用户登录
        $res = M('user')->where(['username' => $username,'password' => md5Encrypt(trim($password), $chars),'status' => 1])
        				->count();
		if($res == 1){
			die(json_encode(['result'=>'1']));
		}else{
			die(json_encode(['result'=>'2']));
		}
	}
	
	//修改密码界面
	public function modifypwd(){
		$this->display();
	}
	
	//校验密码
	public function validatepwd(){
		$username = session(C('USERNAME'));
		$loginform = I("post.LoginForm");
		$password = $loginform['password'];
		
		$chars = M()->table('__USER__ a')
            		->join('LEFT JOIN __CHARUSER__ c ON a.id=c.id')
            		->where(['a.username'=>trim($username), 'a.status'=>1,'c.type'=>0])
            		->getField('chars');
		//查询用户登录
        $res = M('user')->field('password')
        				->where(['username' => $username,'status' => 1])
        				->find();
		if(md5Encrypt(trim($password), $chars) == $res['password']){
			die(json_encode(['result'=>'1']));
		}else{
			die(json_encode(['result'=>'2']));
		}
	}
	
	//修改密码动作
	public function updatepwd(){
		$username = session(C('USERNAME'));
		$loginform = I("post.LoginForm");
		$password = $loginform['password'];
		$chars = M()->table('__USER__ a')
            		->join('LEFT JOIN __CHARUSER__ c ON a.id=c.id')
            		->where(['a.username'=>trim($username), 'a.status'=>1,'c.type'=>0])
            		->getField('chars');
		
		$user = M('user');
		$user->password = md5Encrypt(trim($password), $chars);
		$flag = $user->where(['username'=>$username])->save();
		if($flag){
			writeSysLog(4,'修改密码','modify Password');
			die(json_encode(['result'=>'1']));
		}else{
			die(json_encode(['result'=>'2']));
		}
	}
	
	//无权限页面
	public function noauth(){
		$this->display('Login/noauth');
	}
	
	//忘记密码页面
	public function forgetpwdpage(){
		$this->assign('waitTime','');
		$this->display('Login/forgetpwdpage');
	}
	
	//发送邮件验证码
	public function sendMailPwd(){
		$email = I('post.email');
		$infos = M('user')->where(['email'=>$email])->find();
		if(empty($infos)){
			die(json_encode(['result'=>0,'msg'=>'邮箱不存在']));
		}else{
			if($infos['status'] == 0){
				die(json_encode(['result'=>0,'msg'=>'该用户账号已被禁用']));
			}
			
			$yzm = mt_rand(100000,999999);
			
			$redis = new RedisModel();
			$redis->set($email, $yzm,0,0,C('MSG_TIME'),0);
			
			$flag = sendMail($email,'忘记密码验证码【GG网络】',"您此次验证码为".$yzm.",请在5分钟内输入！非本人操作，请尽快修改密码！【GG网络】");
			if($flag){
				die(json_encode(['result'=>1,'wtime'=>C('MSG_TIME'),'msg'=>'发送成功，请查收']));
			}else{
				die(json_encode(['result'=>0,'msg'=>'发送失败']));
			}
		}
	}
	
	//校验邮箱发送校验url
	public function forgetpwdval(){
		$email = I('post.email');
		$vcode = I('post.vcode');
		$redis = new RedisModel();
		$yzminfo = $redis->get($email);
		if(empty($yzminfo)){
			die(json_encode(['result'=>0,'msg'=>'验证码已经过期，请重新发送']));
		}elseif($yzminfo != $vcode){
			die(json_encode(['result'=>0,'msg'=>'验证码错误']));
		}
		
		$infos = M('user')->where(['email'=>$email])->find();
		$key = md5($email.$infos['password']);
		$str = base64_encode($email.'+'.$key);
		$time = time();
		$code = md5(C('PWD_DESC').$time);
		
		$returnStr = "id=".$infos['id']."&string=".$str."&code=".$code."&time=".$time;
		
		$redis->set('MOD_PWD_'.$email, $infos['id'],0,0,600,0);
		
		die(json_encode(['result'=>1,'str'=>$returnStr]));
	}
	
	//修改密码界面（忘记密码）
	public function forgetpwdmod(){
		$this->display('Login/forgetpwdmod');
	}	
	
	//修改密码
	public function forgetpwdsure(){
		$id = I('post.id','');
		//$key = I('post.key','');
		$string = I('post.string','');
		$code = I('post.code','');
		$time = I('post.time','');
		$pwd = I('post.pwd','');
		if($id == '' || $string == '' || $code == '' || $time == ''){
			die(json_encode(['result'=>0,'msg'=>'非法请求']));
		}
		$str = base64_decode($string);
		$arr = explode('+',$str);
		
		$email = $arr[0];
		$redis = new RedisModel();
		$emailExists = $redis->get('MOD_PWD_'.$email);
		if(empty($emailExists)){
			die(json_encode(['result'=>0,'msg'=>'请求过期']));
		}
		$infos = M('user')->where(['email'=>$email])->find();
		$key = md5($email.$infos['password']);
		if($key != $arr[1]){
			die(json_encode(['result'=>0,'msg'=>'非法请求']));
		}
		
		$codeR = md5(C('PWD_DESC').$time);
		if($code != $codeR){
			die(json_encode(['result'=>0,'msg'=>'非法请求']));
		}
		
		$chars = M()->table('__USER__ a')
            		->join('LEFT JOIN __CHARUSER__ c ON a.id=c.id')
            		->where(['a.email'=>trim($email), 'a.status'=>1,'c.type'=>0])
            		->getField('chars');
		
		$user = M('user');
		$user->password = md5Encrypt(trim($pwd), $chars);
		$flag = $user->where(['email'=>$email])->save();
		if($flag !== false){
			writeSysLog(4,'修改密码','modify Password');
			die(json_encode(['result'=>'1','msg'=>'操作成功']));
		}else{
			die(json_encode(['result'=>0,'msg'=>'修改密码失败']));
		}
	}
}