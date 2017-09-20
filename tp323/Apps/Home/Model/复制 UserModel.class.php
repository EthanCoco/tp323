<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model
{
    /**
     * $_validate 自动验证
     **/
    protected $_validate = array(
        array('username', 'require', '{%HOME_MODEL_USER_USERNAME_REQUIRE}'),
        array('password', 'require', '{%HOME_MODEL_USER_PASSWORD_REQUIRE}'),
        array('name', 'require', '{%HOME_MODEL_USER_NAME_REQUIRE}'),
        array('email', 'require', '{%HOME_MODEL_USER_EMAIL_REQUIRE}'),
        array('email', 'email', '{%HOME_MODEL_USER_EMAIL_EMIAL}'),
        array('phone', 'require', '{%HOME_MODEL_USER_PHONE_REQUIRE}'),
        array('sort', 'require', '{%HOME_MODEL_USER_SORT_REQUIRE}'),
        array('sort', 'number', '{%HOME_MODEL_USER_SORT_NUMBER}'),
        array('verify', 'checkcode', '{%HOME_MODEL_USER_VERIFY_CODE}', 0, 'function'),
    );
	
    protected $_auto = array(
        array('addtime', 'time', self::MODEL_INSERT, 'function'),
        array('add_ip', 'get_client_ip', self::MODEL_INSERT, 'function'),
    );

    /**
     * alogin　登录操作
     * @reutrn string
     **/
    public function login(){
    	//验证过滤
        $data = $this->create($_POST, 2);
        if(empty($data)){
            return false;
        }
		
		//查询加密字符
		$chars = M()->table('__USER__ a')
            		->join('LEFT JOIN __CHARUSER__ c ON a.id=c.id')
            		->where(['a.username'=>trim($data['username']), 'a.status'=>1,'c.type'=>0])
            		->getField('chars');
        if(!$chars){
            $this->error = L('HOME_MODEL_USER_LOGIN_MSG1');
            return false;
        }
		
		//查询用户登录
        $where = array(
            'username' => $data['username'],
            'password' => md5Encrypt(trim($data['password']), $chars),
            'status'   => 1
        );
        $res = $this->field('id,username,status,name,cid')->where($where)->find();
        if($res){
        	//更新用户登录信息
            $lastData = array(
                'last_time' => time(),
                'last_ip'   => get_client_ip()
            );
            $this->where($where)->save($lastData);
            session(C('IS_LOGIN'), $res['id']);
            session(C('USERNAME'), $res['name']);
			$comInfo = M('cominfo')->field('cname,cmpLevel,cmpcode,name,gljgdm')->where(['cid'=>$res['cid']])->find();
            //将单位信息放入session中
            session(C('CID'), $res['cid']);
            session(C('CSIMPLENAME'), $comInfo['cname']);//单位简称
			session(C('CFULLNAME'), $comInfo['name']);//单位全称
			session(C('CCODE'), $comInfo['cmpcode']);//单位代码
            return $res;
        }else{
            $this->error = L('HOME_MODEL_USER_LOGIN_MSG2');
            return false;
        }
    }

}