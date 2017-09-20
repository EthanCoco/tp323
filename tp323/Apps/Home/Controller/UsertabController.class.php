<?php
namespace Home\Controller;

class UsertabController extends PrivateController {
	public function _initialize(){
		$infos = parent::_initialize(1);
	}
	/*用户列表*/
	public function listinfo(){
		$page = I('post.page',1);
		$rows = I('post.rows',20);
		//排序
		$sort  = I('post.sort');
		$order  = I('post.order');
		
		//查询条件数据
		$data = I('post.data','');
		//排序信息
		if($sort){
			$orderInfo = $sort." ".$order;
		}else{
			$orderInfo = "id ASC";
		}
		
		//计算分页信息
		$offset = ($page-1) * $rows;
		
		//查询条件组装
		$where['usertype'] = 1;
		//$where['status'] = 1;
		if(!empty($data)){
			foreach($data as $key=>$vue){
				$where[$key] = ['like','%'.trim($vue).'%'];
			}
		}
		
		$infos = D('user')->listInfo(2,$orderInfo,$offset,$rows,$where);
		
		//分页条件信息（导出做准备）
		$pageInfo = ['page'=>$page,'rows'=>$rows,'orderInfo'=>$orderInfo,'where'=>$where];
		
		echo json_encode(['rows'=>$infos['infos'],'total'=>$infos['count'],'pageInfo'=>$pageInfo]);
	}
	
	/*添加用户*/
	public function addinfo(){
		//表单字段数据
		$data = I('post.data',"");
		
		//获取密码加密字符
		$charspwd = $data['charspwd'];
		
		//补充或删除表数据
		unset($data['charspwd']); 
		$data['addtime'] = time();
		$data['add_ip'] = get_client_ip();
		$data['status'] = 1;
		$data['usertype'] = 1;
		$data['password'] = md5Encrypt(trim(md5(C('DEFAULT_PWD'))), $charspwd);
		//插入数据
		$userid = M('user')->data($data)->add();
		if($userid){
			//插入密码验证字符
			$charuser = M('charuser')->data(['id'=>$userid,'chars'=>$charspwd,'type'=>0])->add();
			writeSysLog(2,'添加用户','Add User');
			die(json_encode(['result'=>'1']));
		}
		die(json_encode(['result'=>'2']));
	}
	
	/*重名判断*/
	public function rename(){
		$data = I('post.data',"");
		$flag = M('user')->where(['username'=>$data])->count();
		if($flag > 0){
			die(json_encode(['result'=>'1']));
		}else{
			die(json_encode(['result'=>'2']));
		}
	}
	
	/*导出Excel*/
	public function export(){
		//1、导出当前页		2、导出所有
		$type = I('post.type','2');
		$pageInfo = I('post.pageInfo','');
		$where = json_decode(htmlspecialchars_decode($pageInfo),true);
		$offset = ($where['page']-1) * $where['rows'];
		$dataInfos = [];
		//获取数据
		if($type == 1){
			$dataInfos = M('user')->where($where['where'])->select();
		}else{
			$dataInfos = M('user')->where($where['where'])
								  ->order($where['orderInfo'])
								  ->limit($offset,$where['rows'])
								  ->select();
		}
		$jsonData = [];
		$keys = ['id','username','name','idcard','email','phone','status','add_ip','addtime'];
		foreach($dataInfos as $info){
			$jsonData[] = [
				'id'		=>	$info['id'],
				'username' 	=> 	$info['username'],
				'name'		=>	$info['name'],
				'idcard'	=>	$info['idcard'],
				'email'		=>	$info['email'],
				'phone'		=>	$info['phone'],
				'status'	=>	($info['status'] == '1' ? L('HOME_COMMON_ENABLE') : L('HOME_COMMON_UNENABLE') ),
				'add_ip'	=>	$info['add_ip'],
				'addtime'	=>	date('Y-m-d',$info['addtime'])	 
			];
		}
		
		//exit(var_dump($jsonData));
		//导出excel
		export_common_excel($type,$jsonData,2,0,$keys,'usertab');
	}
	
	/*修改用户状态*/
	public function changestatus(){
		$status = I('post.status');
		$id = I('post.id');
		$user = M('user');
		$user->status = $status;
		$flag = $user->where(['id'=>$id])->save();
		if($flag){
			writeSysLog(4,'修改用户id='.$id.'状态','Modify User Status ID='.$id);
			die(json_encode(['result'=>'1']));
		}else{
			die(json_encode(['result'=>'2']));
		}
	}

	/*获取当前用户个人信息*/
	public function profileinfo(){
		//exit(var_dump(session(C('IS_LOGIN'))));
		$model = new \Think\Model();
		$profileInfo =$model->table('__USER__')
							->field("id,username,name,email,phone,addtime,userimageurl,idcard,usersex,useraddrdetail,userdiscrip,usercity,usertown,userprovince,userprovince as userprovince1,usercity as usercity1,usertown as usertown1")
							->where(['id'=>session(C('IS_LOGIN'))])
							->find();
		//userprovince 和usercity 和usertown为必填项 所以判断一个即可
		if(!empty($profileInfo['userprovince'])){
			$regionInfo = M('area')->field("CASE '" . LANG_SET . "' WHEN 'zh-cn' THEN name ELSE nameen END AS name")
								   ->where(['id'=>['in',[$profileInfo['userprovince'],$profileInfo['usercity'],$profileInfo['usertown']]]])
								   ->order('level asc')
								   ->select();
			$profileInfo['userprovince'] = $regionInfo[0]['name'];
			$profileInfo['usercity'] = $regionInfo[1]['name'];
			$profileInfo['usertown'] = $regionInfo[2]['name'];
		}
		
		//var_dump($profileInfo);
		//writeSysLog(5,'获取个人信息','Get Personal Information');
		die(json_encode($profileInfo));
	}
	
	/*修改保存个人信息*/
	public function saveprofile(){
		//表单字段数据
		$data = I('post.data',"");
		$data['usermodifytime'] = time();
		//插入数据
		$flag = M('user')->where(['id'=>session(C('IS_LOGIN'))])->save($data);
		if($flag){
			session(C('USERSEX'), $data['usersex']);
			session(C('USERIMAGEURL'), $data['userimageurl']);
			session(C('USERREALNAME'), $data['name']);
			writeSysLog(4,'修改个人信息','Modify Personal Infomation');
			die(json_encode(['result'=>'1']));
		}
		die(json_encode(['result'=>'2']));
	}
	
	/*获取个人操作信息(最新10条)*/
	public function operateinfo(){
		$personLogInfos = M('log')->field("logid,type,userid,username,time,ipaddr,CASE '" . LANG_SET . "' WHEN 'zh-cn' THEN content ELSE contenten END AS content")
								  ->where(['userid'=>session(C('IS_LOGIN'))])
								  ->order('time desc')
								  ->limit(0,10)
								  ->select();
		$jsonData = [];
		foreach($personLogInfos as $info){
			$codeArr = [['type','RZLX']];
			$codeValueInfo = getCodeInfos($codeArr,$info);
			$jsonData[] = [
				'logid'		=> 	$info['logid'],
				'type'		=> 	$info['type'],
				'typename'	=> 	$codeValueInfo['type'],
				'username'	=> 	$info['username'],
				'content' 	=> 	$info['content'],
				'time' 		=> 	$info['time']
			];
		}
		
		$data = [];
		$totalInfo = M('log')->field('*,count(1) as num')
							 ->where(['userid'=>session(C('IS_LOGIN'))])
							 ->group('type')
							 ->order('type asc')
							 ->select();
							 
		foreach($totalInfo as $info){
			$codeArr = [['type','RZLX']];
			$codeValueInfo = getCodeInfos($codeArr,$info);
			$data[] = [
				'type'		=> 	$info['type'],
				'typename'	=> 	$codeValueInfo['type'],
				'username'	=> 	$info['username'],
				'num'		=> 	$info['num']
			];
		}
		//exit(var_dump($jsonData));
		echo json_encode(['timelist'=>$jsonData,'ullist'=>$data]);
	}
}
