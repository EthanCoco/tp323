<?php
namespace Home\Controller;

class BtntabController extends PrivateController {
	public function _initialize(){
		$infos = parent::_initialize(1);
	}
	/*按钮规则列表*/
	public function listinfo(){
		$page = I('post.page',1);
		$rows = I('post.rows',20);
		
		//计算分页信息
		$offset = ($page-1) * $rows;
		
		$infos = M('function')->where(['functionstatus'=>1])->field('id,functionname title')
					 ->order('functionsort asc')
					 ->limit($offset, $rows)
					 ->select();		 
		$count = M('function')->where(['functionstatus'=>1])->count();
		echo json_encode(['rows'=>$infos,'total'=>$count]);
	}
	
	public function listinfosub(){
		$funid = I('post.funid');
		$infos = M('authRule')->where(['funid'=>$funid,'status'=>1])->field('id,title,name,status')
					 ->select();		 
		$count = M('authRule')->where(['funid'=>$funid,'status'=>1])->count();
		echo json_encode(['rows'=>$infos,'total'=>$count]);
	}
	
	public function listinfobtn(){
		$rid = I('post.rid');
		$infos = M('authBtn')->where(['rid'=>$rid])->field('id,title,name,status')
					 ->select();		 
		$count = M('authBtn')->where(['rid'=>$rid])->count();
		echo json_encode(['rows'=>$infos,'total'=>$count]);
	}
	
	public function repairInfo(){
		//表单字段数据
		$data = I('post.data',"");
		$id = I('post.id');
		if($id == ""){
			//插入数据
			$flag = M('authBtn')->data($data)->add();
			if($flag){
				writeSysLog(2,'添加按钮规则','Add Button Rule');
				die(json_encode(['result'=>'1']));
			}
		}else{
			//修改
			$res = M('authBtn')->where(['id'=>$id])->save($data);
			if($res !== false){
				writeSysLog(4,'修改按钮规则ID='.$id,'Modift Button Rule For ID='.$id);
				die(json_encode(['result'=>'1']));
			}
		}
			
		die(json_encode(['result'=>'2']));
	}
	
	public function changeStatus(){
		$id = I('post.id');
		$status = I('post.status');
		
		$authBtn = M('authBtn');
		$authBtn->status = $status;
		$flag = $authBtn->where(['id'=>$id])->save();
		if($flag){
			writeSysLog(4,'修改按钮ID='.$id.'状态','Modify Button Status ID='.$id);
			die(json_encode(['result'=>'1']));
		}else{
			die(json_encode(['result'=>'2']));
		}
	}
	
	public function getBtnInfo(){
		$id = I('post.id');
		$infos = M('authBtn')->where(['id'=>$id])->find();
		die(json_encode(['info'=>$infos]));		
	}
	
}
