<?php
namespace Home\Controller;

class CustomertabController extends PrivateController {
	public function _initialize(){
		$infos = parent::_initialize(1);
	}
	/*客户列表*/
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
		$where['usertype'] = 2;
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
		export_common_excel($type,$jsonData,2,0,$keys,'customertab');
	}
	
}
