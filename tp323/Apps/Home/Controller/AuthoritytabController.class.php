<?php
namespace Home\Controller;

class AuthoritytabController extends PrivateController {
	public function _initialize(){
		$infos = parent::_initialize(1);
		//废弃代码
	}
	/*用户树*/	
	public function usertree(){
		$data[] = ["id" => 0, "parent" => "#", "text" => L('HOME_VIEW_MANAGE_AUTHORITYTAB_CRTL_MSG3'),'state' => ['opened' => true]];
		$infos = M('user')->field("id,name")->where(['status'=>1,'usertype'=>1])->select();
		foreach($infos as $info){
			$data [] = [
	       	"id"		=>	$info['id'],
				"text"		=>	$info['name'],
				"parent"	=>	0,
				"type"		=>	'file'
			];
		}
		echo json_encode($data);
	}
	
	/*权限树*/
	public function funtree(){
        //用户ID
		$userid = I('post.userid',"");
		//获取功能树
		$jsonData = $this->funAuthList();
    	
		//获取用户权限信息
		$userFun = $this->userFunInfo($userid);
		echo json_encode(['funtree'=>$jsonData,'userfun'=>$userFun]);
	}
	
	//获取用户权限信息
	private function userFunInfo($userid = ""){
		$result = [];
		if(empty($userid)){
			return $result;
		}
		$infos = M('auth')->field('funcode')->where(['userid'=>$userid])->select();
		foreach($infos as $info){
			array_push($result,$info['funcode']);
		}
		return $result;
	}
	
	//获取功能树
	private function funAuthList(){
		$funInfo = M('function')->field("id,functionid, CASE '" . LANG_SET . "' WHEN 'zh-cn' THEN functionname ELSE functionnameen END AS functionname,functiontype,functionurl,functionimagecode ")
									 ->where(['functionstatus'=>'1'])
									 ->order("functiontype desc,functionsort asc")
									 ->select();
		$jssonData = [];
		$index = 0;						 
		foreach($funInfo as $info){
			if($info['functiontype'] == 0){
				$jsonData[$index] = [
					'id'		=>	$info['functionid'],
					'text'		=>	$info['functionname'],
					'parent'	=>	0,
					'type'		=>	"file",
					'flag'		=>	1,
				];
			}else{
				$jsonData[$index] = [
						'id'		=>	$info['functionid'],
						'text'		=>	$info['functionname'],
						'parent'	=>	0,
						'state' 	=> 	['opened' => true],
						'flag'		=>	0,
						
					];
				$funchildInfo = M('funchild')->field("id,childrencode,childrenstatus,childrenurl, CASE '" . LANG_SET . "' WHEN 'zh-cn' THEN childrenname ELSE childrennameen END AS childrenname")
									 ->where(['pid'=>$info['id'],'childrenstatus'=>1])
									 ->order('childrensort asc')
									 ->select();
				//二级目录
				if(!empty($funchildInfo)){
					foreach($funchildInfo as $cinfo){
						$jsonData[] = [
							'id'		=>	$cinfo['childrencode'],
							'text'		=>	$cinfo['childrenname'],
							'parent'	=>	$info['functionid'],
							'type'		=>	"file",
							'flag'		=>	1,
						];
						$index++;
					}
				}
			}
			$index++;
		}
		$jsonData[] = ['id'=>0,'flag'=>0,'state' => ['opened' => true],'text'=> L('HOME_VIEW_MANAGE_AUTHORITYTAB_CRTL_MSG4'),'parent'=>"#"];
		//exit(var_dump($jsonData));
		return $jsonData;
	}
	
	/*保存权限*/
	public function saveauth(){
		$userid = I('post.userid');
		$data = I('post.data');
		//删除原先权限
		M('auth')->where(['userid'=>$userid])->delete();
		$dataList = [];
		for($i = 0;$i<count($data);$i++){
			$dataList[] = [
				'userid'  => $userid,
				'funcode' => $data[$i],
			];
		}
		//批量写入
		M('auth')->addAll($dataList);
		writeSysLog(8,'给userid='.$userid.'赋权','Empowerment for userid='.$userid);
		die(json_encode(['result'=>1]));
	}

}
