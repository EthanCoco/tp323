<?php
namespace Home\Controller;

class RuletabController extends PrivateController {
	public function _initialize(){
		$infos = parent::_initialize(1);
	}
	/*路由规则列表*/
	public function listinfo(){
		$page = I('post.page',1);
		$rows = I('post.rows',20);
		
		//计算分页信息
		$offset = ($page-1) * $rows;
		
		$infos = M('function')->where(['functionstatus'=>1])->field('id,functionname title')
					 ->order('functionsort asc')
					 ->limit($offset, $rows)
					 ->select();		 
		$count = M('function')->count();
		echo json_encode(['rows'=>$infos,'total'=>$count]);
	}
	
	public function listinfosub(){
		$funid = I('post.funid');
		$infos = M('authRule')->where(['funid'=>$funid])->field('id,title,name,status')->select();		 
		$count = M('authRule')->where(['funid'=>$funid])->count();
		echo json_encode(['rows'=>$infos,'total'=>$count]);
	}
	
	public function repairInfo(){
		//表单字段数据
		$data = I('post.data',"");
		$id = I('post.id');
		if($id == ""){
			$data['type'] = 1;
			//插入数据
			$flag = M('authRule')->data($data)->add();
			if($flag){
				writeSysLog(2,'添加路由规则','Add Route Rule');
				die(json_encode(['result'=>'1']));
			}
		}else{
			//修改
			$res = M('authRule')->where(['id'=>$id])->save($data);
			if($res !== false){
				writeSysLog(4,'修改路由规则ID='.$id,'Modift Route Rule For ID='.$id);
				die(json_encode(['result'=>'1']));
			}
		}
		die(json_encode(['result'=>'2']));
	}
	
	public function changeStatus(){
		$id = I('post.id');
		$status = I('post.status');
		
		$authRule = M('authRule');
		$authRule->status = $status;
		$flag = $authRule->where(['id'=>$id])->save();
		if($flag){
			writeSysLog(4,'修改路由ID='.$id.'状态','Modify Route Status ID='.$id);
			die(json_encode(['result'=>'1']));
		}else{
			die(json_encode(['result'=>'2']));
		}
	}
	
	public function getRuleInfo(){
		$id = I('post.id');
		$infos = M('authRule')->where(['id'=>$id])->find();
		die(json_encode(['info'=>$infos]));		
	}
}
