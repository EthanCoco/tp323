<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model{
//	protected  $_validate=[
//		['id','require','【字段】不能为空！',],
//		['username','require','【字段】不能为空！',],
//		['password','require','【字段】不能为空！',],
//		['name','require','【字段】不能为空！',],
//		['email','require','【字段】不能为空！',],
//		['phone','require','【字段】不能为空！',],
//		['addtime','require','【字段】不能为空！',],
//		['last_time',],
//		['add_ip',],
//		['last_ip',],
//		['status','require','【字段】不能为空！',],
//		['idcard','require','【字段】不能为空！',],
//		['usertype','require','【字段】不能为空！',],
//		['userimageurl',],
//		['usersex',],
//		['usermodifytime',],
//		['useraddrdetail',],
//		['userdiscrip',],
//		['userpost',],
//		['userprovince',],
//		['usercity',],
//		['usertown',],
//	];

	/**
	 * 查询表数据
	 * @type type=1 全部查询，type=2 分页查询
	**/
	public function listInfo($type = 1, $orderInfo = '', $offset = null ,$rows = null , $where=[]){
		if($type == 2){
			$infos = M('user')->where($where)
					 ->field('id,username,password,name,email,phone,addtime,last_time,add_ip,last_ip,status,idcard,usertype,userimageurl,usersex,usermodifytime,useraddrdetail,userdiscrip,userpost,userprovince,usercity,usertown')
					 ->order($orderInfo)
					 ->limit($offset, $rows)
					 ->select();
			$count = M('user')->where($where)->count();
		}else{
			$infos = M('user')->where($where)
					 ->field('id,username,password,name,email,phone,addtime,last_time,add_ip,last_ip,status,idcard,usertype,userimageurl,usersex,usermodifytime,useraddrdetail,userdiscrip,userpost,userprovince,usercity,usertown')
					 ->order($orderInfo)
					 ->select();
			$count = M('user')->where($where)->count();
		}
		return['infos' => $infos , 'count' => $count];
	}

	/**
	 * 通过ID查询信息
	 * @id 只需传入数字即可
	**/
	public function findByID($id, $orderInfo = '',  $where=['1' => 1]){
		$infos = M('user')->where(['id' => $id])
					 ->where($where)
					 ->field('id,username,password,name,email,phone,addtime,last_time,add_ip,last_ip,status,idcard,usertype,userimageurl,usersex,usermodifytime,useraddrdetail,userdiscrip,userpost,userprovince,usercity,usertown')
					 ->order($orderInfo)
					 ->find();
		return['infos' => $infos];
	}

}