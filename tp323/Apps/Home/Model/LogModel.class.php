<?php
namespace Home\Model;
use Think\Model;
class LogModel extends Model{
	protected  $_validate=[
		['logid','require','【字段】不能为空！',],
		['type','require','【字段】不能为空！',],
		['content',],
		['userid','require','【字段】不能为空！',],
		['username',],
		['time',],
		['ipaddr',],
		['contenten',],
	];

	/**
	 * 查询表数据
	 * @type type=1 全部查询，type=2 分页查询
	**/
	public function list($type = 1, $orderInfo = '', $offset = null ,$rows = null , $where=[]){
		if($type == 2){
			$infos = M('log')->where($where)
					 ->field('logid,type,content,userid,username,time,ipaddr,contenten')
					 ->order($orderInfo)
					 ->limit($offset, $rows)
					 ->select();
			$count = M('log')->where($where)->count();
		}else{
			$infos = M('log')->where($where)
					 ->field('logid,type,content,userid,username,time,ipaddr,contenten')
					 ->order($orderInfo)
					 ->select();
			$count = M('log')->where($where)->count();
		}
		return['infos' => $infos , 'count' => $count];
	}

	/**
	 * 通过ID查询信息
	 * @id 只需传入数字即可
	**/
	public function findByID($id, $orderInfo = '',  $where=['1' => 1]){
		$infos = M('log')->where('logid' => $id)
					 ->where($where)
					 ->field('logid,type,content,userid,username,time,ipaddr,contenten')
					 ->order($orderInfo)
					 ->find();
		return['infos' => $infos];
	}

}