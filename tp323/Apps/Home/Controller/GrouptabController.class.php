<?php
namespace Home\Controller;

class GrouptabController extends PrivateController {
	public function _initialize(){
		$infos = parent::_initialize(1);
	}
	/*用户组列表*/
	public function listinfo(){
		$page = I('post.page',1);
		$rows = I('post.rows',20);
		//排序
		$sort  = I('post.sort');
		$order  = I('post.order');
		
		//排序信息
		if($sort){
			$orderInfo = $sort." ".$order;
		}else{
			$orderInfo = "id ASC";
		}
		
		//计算分页信息
		$offset = ($page-1) * $rows;
		
		$infos = M('authGroup')->field('id,title,status,rules')
					 ->order($orderInfo)
					 ->limit($offset, $rows)
					 ->select();
		$jsonData = [];
		$index = 0;
		if(!empty($infos)){
			foreach($infos as $info){
				$jsonData[$index] =[
					'id'=>$info['id'],
					'title'=>$info['title'],
					'status'=>$info['status'],
					'rules' => $info['rules'],
				];
				
				$rInfos = M('authRule')->field('title')->where(['id'=>['in',$info['rules']]])->select();
				
				if(!empty($rInfos)){
					$num = 1;
					$str = '';
					foreach($rInfos as $rinfo){
						if($num%4 == 0){
							$str.=$rinfo['title']."、<br/>";
						}else{
							$str.=$rinfo['title']."、";
						}
						$num++;
					}
					$str = rtrim(rtrim($str, "、"),'、<br/>');
					$jsonData[$index]['rules'] = $str; 
				}else{
					$jsonData[$index]['rules'] = ''; 
				}
				$index++;
			}
		}			 
					 
		$count = M('authGroup')->count();
		echo json_encode(['rows'=>$jsonData,'total'=>$count]);
	}
	
	public function changeStatus(){
		$id = I('post.id');
		$status = I('post.status');
		
		$authGroup = M('authGroup');
		$authGroup->status = $status;
		$flag = $authGroup->where(['id'=>$id])->save();
		if($flag){
			writeSysLog(4,'修改用户组ID='.$id.'状态','Modify Group Status ID='.$id);
			die(json_encode(['result'=>'1']));
		}else{
			die(json_encode(['result'=>'2']));
		}
	}
	
	public function getRuleTree(){
		$type = I('post.type');
		$authInfos = M()->table('__AUTH_RULE__ r')
		   				->join('left join __FUNCTION__ f on f.id=r.funid and f.functionstatus=1')
		   				->field('r.id rid,r.title rtitle,f.id fid,f.functionname ftitle')
		   				->where(['r.status'=>1,'f.functionstatus'=>1])
		   				//->order('f.functionsort desc')
						->select();
		if(!empty($authInfos)){
			$tempFtitle = [];
			$tempRtitle = [];
			foreach($authInfos as $auth){
				$tempFtitle [$auth['fid']] = ['fid'=>$auth['fid'],'ftitle'=>$auth['ftitle']];
				$tempRtitle [$auth['rid']]= ['fid'=>$auth['fid'],'rid'=>$auth['rid'],'rtitle'=>$auth['rtitle']];
			}
			if(!empty($tempFtitle)){
				foreach($tempFtitle as $f){
					$jsonData[] = [
						'id'		=>	'fun_'.$f['fid'],
						'text'		=>	$f['ftitle'],
						'parent'	=>	0,
						'state' 	=> 	['opened' => true],
						'flag'		=>	$f['fid'],
					];
				}
				foreach($tempRtitle as $r){
					$jsonData[] = [
						'id'		=>	'rule_'.$r['rid'],
						'text'		=>	$r['rtitle'],
						'parent'	=>	'fun_'.$r['fid'],
						'state' 	=> 	['opened' => true],
						'type'		=>	'file',
						'flag'		=>	$r['rid'],
					];
				}
			}
		}
		$jsonData[] = ['id'=>0,'flag'=>0,'state' => ['opened' => true],'text'=>'选择用户组拥有的路由规则','parent'=>"#"];
		$groupInfos = [];
		$ruleCheckedInfo = [];
		//需要查询用户组已有的规则
		if($type == 1){
			$id = I('post.id');
			$groupInfos = M('authGroup')->where(['id'=>$id])->find();
			$rules = explode(',',$groupInfos['rules']);
			$len = count($rules);
			$ruleCheckedInfo = [];
			for($i=0;$i<$len;$i++){
				array_push($ruleCheckedInfo,'rule_'.$rules[$i]);
			}
		}
		die(json_encode(['ruleInfos'=>$jsonData,'groupInfos'=>$groupInfos,'ruleCheckedInfo'=>$ruleCheckedInfo]));
	}
	
	public function repairInfo(){
		//表单字段数据
		$data = I('post.data',"");
		$id = I('post.id');
		if($id == ""){
			//插入数据
			$flag = M('authGroup')->data($data)->add();
			if($flag){
				writeSysLog(2,'添加用户组','Add User Group');
				die(json_encode(['result'=>'1']));
			}
		}else{
			//修改
			$res = M('authGroup')->where(['id'=>$id])->save($data);
			if($res !== false){
				writeSysLog(4,'修改用户组ID='.$id,'Modift User Group For ID='.$id);
				die(json_encode(['result'=>'1']));
			}
		}
		die(json_encode(['result'=>'2']));
	}
	
	
	
}
