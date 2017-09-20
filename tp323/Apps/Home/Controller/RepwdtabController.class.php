<?php
namespace Home\Controller;

class RepwdtabController extends PrivateController {
	public function _initialize(){
		$infos = parent::_initialize(1);
	}
	
	public function listinfo(){
		$page = I('post.page',1);
		$rows = I('post.rows',20);
		//排序
		$sort  = I('post.sort');
		$order  = I('post.order');
		
		//查询条件数据
		$data = I('post.data','');
		$usertype = I('post.usertype','');
		//排序信息
		if($sort){
			$orderInfo = $sort." ".$order;
		}else{
			$orderInfo = "id ASC";
		}
		
		//计算分页信息
		$offset = ($page-1) * $rows;
		
		//查询条件组装
		$where['usertype'] = $usertype;
		if(!empty($data)){
			foreach($data as $key=>$vue){
				$where[$key] = ['like','%'.trim($vue).'%'];
			}
		}
		
		$infos = D('user')->listInfo(2,$orderInfo,$offset,$rows,$where);
		
		echo json_encode(['rows'=>$infos['infos'],'total'=>$infos['count']]);
	}
	
	//重置密码
	public function resetpwd(){
		$id = I('post.id');
		$chars = M('charuser')->where(['id'=>$id])->find();
		
		$user = M('user');
		$user->password = md5Encrypt(C('DEFAULT_PWD'), $chars['chars']);
		$user->usermodifytime = time();
		$flag = $user->where(['id'=>$id])->save();
		if($flag){
			writeSysLog(4,'重置用户id='.$id.'密码','Reset User password ID='.$id);
			die(json_encode(['result'=>'1']));
		}else{
			die(json_encode(['result'=>'2']));
		}
	}
	
}
