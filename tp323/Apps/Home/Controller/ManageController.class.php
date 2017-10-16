<?php
namespace Home\Controller;
class ManageController extends PrivateController {
	//按钮权限信息
	private $_btnInfo = [];
	
	public function _initialize(){
		$infos = parent::_initialize(0);
        $this->_btnInfo = $infos['btnInfo'];
        $btnIsOn = session(C('USERTYPE')) == 0 ? 0 : C('AUTH_BTN_ON');
        $this->assign('btnon',$btnIsOn);
        $this->assign('btnInfos',$this->_btnInfo);
	}
    //默认首页
    public function index(){
    	//判断用户是否登录
    	$sessionLogin = session(C('IS_LOGIN'));
    	if(empty($sessionLogin)){
        	redirect('Login/login');
        }
		
		//$this->assign('authInfo',$this->_D42514FC0286ABEFF5ED54B9AA96FDE0());						 
		$this->assign('functionInfo',$this->_left());
		if(session(C('USERTYPE')) == 0){
			$this->assign('feedbackInfo',$this->_feedback());
		}
//		echo("<meta charset='UTF-8'>");	
//		exit(dump($this->__feedback()));
		$this->display("index");
    }
	
	//获取权限
	public function _D42514FC0286ABEFF5ED54B9AA96FDE0(){
		$__0286abeff5ed54b9 = M(C('__YWQXJM__'))->where([C('__YWCLMU__')=>session(C('IS_LOGIN'))])->select();
		$__d03fcceef6fe0d5e = [];
		$__556a4b3a2cdda23c = 0;
		foreach($__0286abeff5ed54b9 as $__134AB7A97D42D606){
			$__d03fcceef6fe0d5e[$__556a4b3a2cdda23c] = $__134AB7A97D42D606['funcode'];
			$__556a4b3a2cdda23c++;
		}
		return $__d03fcceef6fe0d5e;
	}
	
	//获取菜单
	public function _left(){
		$jsonData = [];
		//查询菜单信息
    	$functionInfo = M('function')->field("id,functionid, CASE '" . LANG_SET . "' WHEN 'zh-cn' THEN functionname ELSE functionnameen END AS functionname,functiontype,functionurl,functionimagecode ")
									 ->where(['functionstatus'=>'1'])
									 ->order("functionsort asc")
									 ->select();
		$index = 0;
		foreach($functionInfo as $info){
			//判断是否是顶级目录跳转 生成路径
			if($info['functiontype'] == 0){
				$jsonData[$index] = [
							'id'				=>	$info['id'],
							'functionid'		=>	$info['functionid'],
							'functionname'		=>	$info['functionname'],
							'functionurl'		=>	U($info['functionurl']),
							'functiontype'		=>	$info['functiontype'],
							'functionimagecode'	=>	$info['functionimagecode'],
							'pId'				=>	0
						];
			}else{
				$jsonData[$index] = [
							'id'				=>	$info['id'],
							'functionid'		=>	$info['functionid'],
							'functionname'		=>	$info['functionname'],
							'functionurl'		=>	$info['functionurl'],
							'functiontype'		=>	$info['functiontype'],
							'functionimagecode'	=>	$info['functionimagecode'],
							'pid'				=>	0
						];
				$funchildInfo = M('funchild')->field("id,childrencode,childrenstatus,childrenurl, CASE '" . LANG_SET . "' WHEN 'zh-cn' THEN childrenname ELSE childrennameen END AS childrenname")
									 ->where(['pid'=>$info['id'],'childrenstatus'=>1])
									 ->order('childrensort asc')
									 ->select();
				//二级目录
				if(!empty($funchildInfo)){
					foreach($funchildInfo as $cinfo){
						$jsonData[$index]['child'][] = [
										'id'			=>	$cinfo['id'],
										'childrencode'	=>	$cinfo['childrencode'],
										'childrenname'	=>	$cinfo['childrenname'],
										'childrenurl'	=>	U($cinfo['childrenurl']),
										'pid'			=>	1
									];
						
					}
				}else{
					$jsonData[$index]['child'][] = [];
				}
			}
			$index++;
		}	
		return $jsonData;
	}
	
	//获取反馈信息
	public function _feedback(){
		$jsonData = [];
		$infos = M('feedback')->field('fcontent,fstatus,ftime,fdealstatus,name,phone,email,userimageurl')
							  ->join('__USER__ on __FEEDBACK__.userid=__USER__.id')
							  ->where(['fdealstatus'=>0])
							  ->order('ftime DESC')
							  ->limit(0,5)
							  ->select();
		foreach($infos as $info){
			$jsonData [] = [
				'fcontent'  	=> cut_str($info['fcontent'],10),
				'fstatus'   	=> $info['fstatus'],
				'ftime'   		=> formatTime($info['ftime'],1),
				'fdealstatus'   => $info['fstatus'],
				'name'   		=> $info['name'],
				'phone'   		=> $info['phone'],
				'email'   		=> $info['email'],
				'userimageurl'  => $info['userimageurl'],
			];
		}
		$count = M('feedback')->where(['fdealstatus'=>0])->count();
		$flag = M('feedback')->where(['fstatus'=>0])->order('ftime DESC')->limit(0,5)->count();
		return ['info'=>$jsonData,'count'=>$count,'flag'=>$flag];
	}
	//测试
	public function listfb(){
		$jsonData = [];
		$infos = M('feedback')->field('fid,fcontent,fstatus,ftime,fdealstatus,name,phone,email,userimageurl')
							  ->join('__USER__ on __FEEDBACK__.userid=__USER__.id')
							  ->where(['fdealstatus'=>0])
							  ->order('ftime DESC')
							  ->limit(0,20)
							  ->select();
		foreach($infos as $info){
			$jsonData [] = [
				'fid'  			=> $info['fid'],
				'fcontent'  	=> cut_str($info['fcontent'],10),
				'fstatus'   	=> $info['fstatus'],
				'ftime'   		=> formatTime($info['ftime'],1),
				'fdealstatus'   => $info['fstatus'],
				'name'   		=> $info['name'],
				'phone'   		=> $info['phone'],
				'email'   		=> $info['email'],
				'userimageurl'  => $info['userimageurl'],
			];
		}
		$count = M('feedback')->where(['fdealstatus'=>0])->count();
		echo json_encode( ['rows'=>$jsonData,'total'=>$count]);
	}
	
	//生成通用model文件
	public function cm(){
		//createConmmonModel('HOME','user');
		createMysqlTable('dddd');
	}
	
	//个人信息
	public function profile(){
		//分配省市区信息
		$areaInfo = M('area')->field("id,pid,level,CASE '" . LANG_SET . "' WHEN 'zh-cn' THEN name ELSE nameen END AS name")
							 ->where(['level'=>1])
							 ->select();
							 
		$this->assign("provinceInfo",$areaInfo);
		
		$this->display('Manage/sysmanage/profile');
	}
	
	//反馈信息
	public function feedback(){
		$this->display('Manage/sysmanage/feedback');
	}
	
	/*****************系统设置******************/
	
	//用户管理
	public function usertab(){
		$this->display('Manage/sysmanage/usertab');
	}
	
	//重置密码管理
	public function repwdtab(){
		$this->display('Manage/sysmanage/repwdtab');
	}
	
	/*****************客户管理******************/
	public function customertab(){
		$this->display('Manage/customer/customertab');
	}
	
	/*****************权限管理******************/
	//用户组管理
	public function grouptab(){
		$this->display('Manage/authority/grouptab');
	}
	
	//路由规则管理
	public function ruletab(){
		$infos = M('function')->where(['functionstatus'=>1])->field('id,functionname title')
					 ->order('functionsort asc')
					 ->select();	
		$this->assign('funInfo',$infos);
		$this->display('Manage/authority/ruletab');
	}
	
	//按钮规则管理
	public function btntab(){
		$infos = M('authRule')->where(['status'=>1])->field('id,title')
					 ->order('funid asc')
					 ->select();
		$this->assign('ruleInfo',$infos);
		$this->display('Manage/authority/btntab');
	}
	
	//规则授权管理
	public function authtab(){
		$this->display('Manage/authority/authtab');
	}
	
	/*****************技术研究******************/
	//规则引擎
	public function enginetab(){
		$this->display('Manage/technical/enginetab');
	}
	
	//短信群发
	public function smsgptab(){
		$this->display('Manage/technical/smsgptab');
	}
	
}