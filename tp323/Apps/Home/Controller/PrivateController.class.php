<?php
namespace Home\Controller;
/**
 * 提示：所有的其他控制器操作，必须继承PrivateController
 */
class PrivateController extends \Think\Controller{
    public function _initialize($type = 0){
    	if($type == 0){
    		$sessionLogin = session(C('IS_LOGIN'));
			if(empty($sessionLogin)){
				session_destroy();
	        	redirect(U('Login/login'));
	        }
	        //当前操作
	        $currentAction = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
	        //默认不需要权限认证操作，以及超级管理员拥有所有权限
	        $defaultAction = ['Home/Manage/index','Home/Manage/profile'];
	        if(session(C('USERTYPE')) == 0 || in_array($currentAction,$defaultAction)){
	        	return true;
	        }
	        //权限认证
	        $checkInfo = $this->_authCheck([$currentAction],'and');
	        //exit(var_dump($checkInfo));
			if(!$checkInfo['result']){
				//跳转到无权限页面
				$this->redirect(C('DEFAULTS_MODULE').'/Login/noauth');
			}
			return ['btnInfo'=>$checkInfo['info']];
    	}else{
    		$sessionLogin = session(C('IS_LOGIN'));
			if(empty($sessionLogin)){
				session_destroy();
	        	redirect(U('Login/login'));
	        }
    	}
    	
	}
	
	/*权限认证方法*/
	private function _authCheck($ruleName = [],$relation = 'or',$uid){
		$auth = new \Think\Auth();
		if(empty($uid)){
			$uid = session(C('IS_LOGIN'));
		}
		return $auth->check($ruleName,$uid,1,'url',$relation);
	}
}