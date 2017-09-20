<?php
namespace Home\Controller;

class EnginetabController extends PrivateController {
	public function _initialize(){
		$infos = parent::_initialize(1);
	}
	
	public function listinfo(){
		$page = I('post.page',1);
		$rows = I('post.rows',20);
		//计算分页信息
		$offset = ($page-1) * $rows;
		
		$infos = M('ruleModel')->field('id,rulename ,ruledescription,status')
				 ->order('id asc,id asc')
				 ->limit($offset, $rows)
				 ->select();		 
		$count = M('ruleModel')->count();
		echo json_encode(['rows'=>$infos,'total'=>$count]);
	}
	
	public function listinfosub(){
		$id = I('post.id');
		$infos = M('ruleParser')->where(['rp_it_id'=>$id])->field('rp_id,rp_it_id ,rp_condition,rp_field,rp_rule,rp_sort')
				 ->order('rp_sort asc')
				 ->select();		 
		$count = M('ruleParser')->where(['rp_it_id'=>$id])->count();
		
		$fieldInfo = M('ruleField')->field('name,descraption')->where(['rp_model_id'=>$id])->select();
		$fieldStr = "";
		if(!empty($fieldInfo)){
			foreach($fieldInfo as $fd){
				$fieldStr .='【'.$fd['name'].':'.$fd['descraption'].'】；';
			}
		}
		echo json_encode(['rows'=>$infos,'total'=>$count,'fieldStr'=>$fieldStr]);
	}
	
	public function repairInfo(){
		//表单字段数据
		$data = I('post.data',"");
		$id = I('post.id');
		if($id == ""){
			//插入数据
			$flag = M('ruleModel')->data($data)->add();
			if($flag){
				writeSysLog(2,'添加规则模板','Add Rule Model');
				die(json_encode(['result'=>'1']));
			}
		}else{
			//修改
			$res = M('ruleModel')->where(['id'=>$id])->save($data);
			if($res !== false){
				writeSysLog(4,'修改规则模板ID='.$id,'Modift Rule Model For ID='.$id);
				die(json_encode(['result'=>'1']));
			}
		}
			
		die(json_encode(['result'=>'2']));
	}
	
	public function changeStatus(){
		$id = I('post.id');
		$status = I('post.status');
		
		$ruleModel = M('ruleModel');
		$ruleModel->status = $status;
		$flag = $ruleModel->where(['id'=>$id])->save();
		if($flag){
			writeSysLog(4,'修改规则模板ID='.$id.'状态','Modify Rule Model Status ID='.$id);
			die(json_encode(['result'=>'1']));
		}else{
			die(json_encode(['result'=>'2']));
		}
	}
	
	public function getEngineInfo(){
		$id = I('post.id');
		$infos = M('ruleModel')->where(['id'=>$id])->find();
		die(json_encode(['info'=>$infos]));		
	}
	
	public function delRuleParser(){
		$id = I('post.id');
		$flag  = M('ruleParser')->where(['rp_id'=>$id])->delete();
		if($flag){
			die(json_encode(['result'=>1]));
		}
		die(json_encode(['result'=>0]));
	}
	
	public function getRuleParser(){
		$id = I('post.id');
		$info  = M('ruleParser')->where(['rp_id'=>$id])->find();
		$jsonData = [
			'rp_field'=>$info['rp_field'],
			'rp_rule'=>replace_trans_info($info['rp_rule']),
			'rp_condition'=>replace_trans_info($info['rp_condition']),
			'rp_sort'=>$info['rp_sort'],
			'rp_id'=>$info['rp_id'],
			'rp_it_id'=>$info['rp_it_id']
		];
		die(json_encode(['info'=>$jsonData]));
	}
	
	public function saveRuleParser(){
		$rp_field = I('post.rp_field');
		$rp_rule = I('post.rp_rule');
		$rp_sort = I('post.rp_sort');
		$rp_condition = I('post.rp_condition');
		$rp_id = I('post.rp_id');
		$rp_it_id = I('post.rp_it_id');
		
		$data['rp_field'] = $rp_field;
		$data['rp_rule'] = addslashes($rp_rule);
		$data['rp_sort'] = $rp_sort; 
		$data['rp_condition'] = addslashes($rp_condition); 
		$data['rp_it_id'] = $rp_it_id; 
		if($rp_id == ""){
			//插入数据
			$flag = M('ruleParser')->data($data)->add();
			if($flag){
				writeSysLog(2,'添加计算规则字段','Add Rule Parser Field');
				die(json_encode(['result'=>1]));
			}
		}else{
			//修改
			$res = M('ruleParser')->where(['rp_id'=>$rp_id])->save($data);
			if($res !== false){
				writeSysLog(4,'修改计算规则字段ID='.$rp_id,'Modify Rule Parser Field for ID='.$rp_id);
				die(json_encode(['result'=>1]));
			}
		}
		die(json_encode(['result'=>0]));
	}
	
	public function listRuleField(){
		$id = I('post.id');
		$fieldInfo = M('ruleField')->where(['rp_model_id'=>$id])->select();
		$count = M('ruleField')->where(['rp_model_id'=>$id])->count();
		die(json_encode(['rows'=>$fieldInfo,'total'=>$count]));	
	}
	
	//删除变量字段
	public function delRuleField(){
		$id = I('post.id');
		$flag  = M('ruleField')->where(['id'=>$id])->delete();
		if($flag){
			die(json_encode(['result'=>1]));
		}
		die(json_encode(['result'=>0]));
	}
	
	public function getRuleField(){
		$id = I('post.id');
		$info  = M('ruleField')->where(['id'=>$id])->find();
		die(json_encode(['info'=>$info]));
	}
	
	//修改添加变量字段
	public function saveRuleField(){
		$name = I('post.name');
		$descraption = I('post.descraption');
		$id = I('post.id');
		$rp_model_id = I('post.rp_model_id');
		if($id == ""){
			//插入数据
			$data['rp_model_id'] = $rp_model_id;
			$data['name'] = $name;
			$data['descraption'] = $descraption; 
			$flag = M('ruleField')->data($data)->add();
			if($flag){
				writeSysLog(2,'添加变量字段','Add variable Field');
				die(json_encode(['result'=>1]));
			}
		}else{
			//修改
			$data['name'] = $name;
			$data['descraption'] = $descraption;
			$res = M('ruleField')->where(['id'=>$id])->save($data);
			if($res !== false){
				writeSysLog(4,'修改变量字段ID='.$id,'Modify variable Field for ID='.$id);
				die(json_encode(['result'=>1]));
			}
		}
		die(json_encode(['result'=>0]));
	}
	
	//试用模板计算
	public function calculation(){
		$info = I('post.info');
		$rp_it_id = I('post.rp_it_id');
		
		$result = caculate_rule_parser($rp_it_id,$info);
		die(json_encode($result));
	}
	
}
