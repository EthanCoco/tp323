<?php
namespace Home\Controller;

class AuthtabController extends PrivateController {
	public function _initialize(){
		$infos = parent::_initialize(1);
	}
	/*按钮规则列表*/
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
			$orderInfo = 'u.'.$sort." ".$order;
		}else{
			$orderInfo = "u.id ASC";
		}
		
		//计算分页信息
		$offset = ($page-1) * $rows;
		
		//查询条件组装
		$where['u.usertype'] = 1;
		$where['u.status'] = 1;
		if(!empty($data)){
			foreach($data as $key=>$vue){
				$where[$key] = ['like','%'.trim($vue).'%'];
			}
		}
		
		$userInfo = M()->table('__USER__ u')
				   ->field('u.id uid,username,name,idcard,group_concat( g.title) g_title,group_concat( g.rules) as g_rules ')
				   ->join('left join __AUTH_GROUP_ACCESS__ ga on ga.uid=u.id')
				   ->join('left join __AUTH_GROUP__ g on g.id=ga.group_id')
				   ->where($where)
				   ->order($orderInfo)
				   ->group('u.id')
				   ->limit($offset,$rows)
				   ->select();
		$jsonData = [];
		$index = 0;
		foreach($userInfo as $info){
			$jsonData [$index] = [
				'id'=>$info['uid'],
				'username'=>$info['username'],
				'name'=>$info['name'],
				'group'=>$info['g_title']
			];
			if(!empty($info['g_rules'])){
				$querySql = M()->table('__AUTH_BTN_ACCESS__ ba')
							   ->join('left join __AUTH_BTN__ b on b.id=ba.btn_id')
							   ->join('left join __AUTH_RULE__ r on r.id=b.rid')
							   ->field('b.rid,GROUP_CONCAT(b.title) b_title')
							   ->where(['uid'=>$info['uid'],'b.rid'=>['in',$info['g_rules']]])
							   ->group('b.rid')
							   ->buildSql();
				$ruleData = M()->table('__AUTH_RULE__ r')
							   ->join('left join '.$querySql.' gg on gg.rid=r.id')
							   ->field('r.title,gg.b_title')
							   ->where(['status'=>1,'r.id'=>['in',$info['g_rules']]])
							   ->select();
				if(!empty($ruleData)){
					$str = '';
					foreach($ruleData as $data){
						empty($data['b_title']) ? ($str .= $data['title']."<br/>") : ($str .= $data['title']."【".$data['b_title']."】<br/>");
					}
					$jsonData[$index]['rulename'] = $str;
				}else{
					$jsonData[$index]['rulename'] = null;
				}
			}else{
				$jsonData[$index]['rulename'] = null;
			}
			
			$index++;
		}
		
		$count = M('user u')->where($where)->count();
		echo json_encode(['rows'=>$jsonData,'total'=>$count]);
	}
	
	public function authGroupTree(){
		$uid = I('post.uid');
		//获取用户组树
		$jsonData = $this->authGroupList();
    	
		$userGroupAuthInfo = $this->userGroupAuthInfo($uid);
		echo json_encode(['authGroupTree'=>$jsonData,'userAuthGroup'=>$userGroupAuthInfo]);
	}
	
	private function authGroupList(){
		$groupInfo = M()->table('__AUTH_GROUP__')->field('id,title,rules')->where(['status'=>1])->select();
		foreach($groupInfo as $group){
			if(!empty($group['rules'])){
				$jsonData[] = [
					'id'		=>	$group['id'],
					'text'		=>	$group['title'],
					'parent'	=>	0,
					'state' 	=> 	['opened' => true],
					'type'		=>	"file",
					'flag'		=>	$group['id'],
				];
		 	}
		}
		$jsonData[] = ['id'=>0,'flag'=>0,'state' => ['opened' => true],'text'=>'权限组列表','parent'=>"#"];
		return $jsonData;
	}
	
	//获取用户权限信息
	private function userGroupAuthInfo($uid = ""){
		$result = [];
		if(empty($uid)){
			return $result;
		}
		$groupInfos = M()->table('__AUTH_GROUP_ACCESS__')->field('group_id')->where(['uid'=>$uid])->select();
		foreach($groupInfos as $info){
			array_push($result,$info['group_id']);
		}
		return $result;
	}
	
	public function authBtnTree(){
		$uid = I('post.uid');
		$groups =  I('post.groupIDs');
		//exit(var_dump($_POST));
		//获取用户组树
		$jsonData = $this->authBtnList($groups);
    	
		$userBtnAuthInfo = $this->userBtnAuthInfo($uid);
		echo json_encode(['authBtnTree'=>$jsonData,'userAuthBtn'=>$userBtnAuthInfo]);
	}
	
	private function userBtnAuthInfo($uid = ""){
		$result = [];
		if(empty($uid)){
			return $result;
		}
		$groupInfos = M()->table('__AUTH_BTN_ACCESS__')->field('btn_id')->where(['uid'=>$uid])->select();
		foreach($groupInfos as $info){
			array_push($result,'btn_'.$info['btn_id']);
		}
		return $result;
	}
	
	//获取功能树
	private function authBtnList($groups){
		$groupInfo = M()->table('__AUTH_GROUP__')->field('id,title,rules')->where(['status'=>1,'id'=>['in',$groups]])->select();
		foreach($groupInfo as $group){
			if(!empty($group['rules'])){
				$jsonData[] = [
					'id'		=>	'group_'.$group['id'],
					'text'		=>	$group['title'],
					'parent'	=>	0,
					'state' 	=> 	['opened' => true],
					'flag'		=>	$group['id'],
				];
				$authInfos = M()->table('__AUTH_RULE__ r')
				   				->join('left join __FUNCTION__ f on f.id=r.funid and f.functionstatus=1')
				   				->join('left join __AUTH_BTN__ b on b.rid=r.id and b.status=1')
				   				->field('r.id rid,r.title rtitle,f.id fid,f.functionname ftitle,b.id bid,b.title btitle')
				   				->where(['r.status'=>1,'r.id'=>['in',$group['rules']],'f.functionstatus'=>1])
								//->order('r.id asc')
								//->fetchSql(true)
								->select();
								//echo $authInfos;exit;
				if(!empty($authInfos)){
					$tempFtitle = [];
					$tempRtitle = [];
					$tempBtitle = [];
					foreach($authInfos as $auth){
						$tempFtitle [$auth['fid']] = ['fid'=>$auth['fid'],'ftitle'=>$auth['ftitle']];
						$tempRtitle [$auth['rid']]= ['fid'=>$auth['fid'],'rid'=>$auth['rid'],'rtitle'=>$auth['rtitle']];
						$tempBtitle [$auth['bid']] = ['rid'=>$auth['rid'],'bid'=>$auth['bid'],'btitle'=>$auth['btitle']];
					}
					if(!empty($tempFtitle)){
						foreach($tempFtitle as $f){
							$jsonData[] = [
								'id'		=>	'fun_'.$f['fid'],
								'text'		=>	$f['ftitle'],
								'parent'	=>	'group_'.$group['id'],
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
								'flag'		=>	$r['rid'],
							];
						}
						//exit(dump($tempBtitle));
						foreach($tempBtitle as $b){
							if($b['bid'] != ""){
								$jsonData[] = [
									'id'		=>	'btn_'.$b['bid'],
									'text'		=>	$b['btitle'],
									'parent'	=>	'rule_'.$b['rid'],
									'state' 	=> 	['opened' => true],
									'type'		=>	'file',
									'flag'		=>	$b['bid'],
								];
							}
						}
					}
				}				
			}
		}
		$jsonData[] = ['id'=>0,'flag'=>0,'state' => ['opened' => true],'text'=>'权限树','parent'=>"#"];
		return $jsonData;
	}
	
	/*保存权限*/
	public function saveAuth(){
		$uid = I('post.uid');
		$data = I('post.data','');
		$type = I('post.type');
		if($type == 0){
			if(!empty($data)){
				//删除原先权限
				M('authGroupAccess')->where(['uid'=>$uid])->delete();
				$dataList = [];
				$len = count($data);
				for($i = 0;$i<$len;$i++){
					$dataList[] = [
						'uid'  => $uid,
						'group_id' => $data[$i],
					];
				}
				//批量写入
				M('authGroupAccess')->addAll($dataList);
				writeSysLog(8,'给用户ID='.$uid.'赋权','Empowerment for userid='.$uid);
				die(json_encode(['result'=>1]));
			}else{
				M('authGroupAccess')->where(['uid'=>$uid])->delete();
				M('authBtnAccess')->where(['uid'=>$uid])->delete();
				writeSysLog(8,'给用户ID='.$uid.'赋权','Empowerment for userid='.$uid);
				die(json_encode(['result'=>1]));
			}
		}elseif($type == 1){
			if(!empty($data)){
				//删除原先权限
				M('authBtnAccess')->where(['uid'=>$uid])->delete();
				$dataList = [];
				$len = count($data);
				for($i = 0;$i<$len;$i++){
					$dataList[] = [
						'uid'  => $uid,
						'btn_id' => $data[$i],
					];
				}
				//批量写入
				M('authBtnAccess')->addAll($dataList);
				writeSysLog(8,'给用户ID='.$uid.'赋权','Empowerment for userid='.$uid);
				die(json_encode(['result'=>1]));
			}else{
				M('authBtnAccess')->where(['uid'=>$uid])->delete();
				writeSysLog(8,'给用户ID='.$uid.'赋权','Empowerment for userid='.$uid);
				die(json_encode(['result'=>1]));
			}
		}
	}
}
