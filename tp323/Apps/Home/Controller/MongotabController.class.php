<?php
namespace Home\Controller;

class MongotabController extends PrivateController {
	public function _initialize(){
		$infos = parent::_initialize(1);
	}
	
	/*原生php-mongo操作*/
	public function index(){
		$m = new \MongoClient(); // 连接默认主机和端口为：mongodb://localhost:27017
		$db = $m->tp;
		//$colletion = $db->createCollection("tp323");
		//echo "集合创建成功";
		
		//插入
		$collection = $db->tp323;
//		$document = [
//			'name'=>	'lily3',
//			'gender'=>	'women',
//			'age'=> 24,
//			'likes'=>100
//		];
//		$collection->insert($document);
		
		//查询
//		$info = $collection->find();
//		foreach($info as $d){
//			echo $d['name']."\n";
//		}
		
		//更新
		//$collection->update(['name'=>'lily'],['$set'=>['age'=>23]]);
		
		//删除(只删除一条)
		//$collection->remove(['age'=>24],['justOne'=>true]);
		
		
	}
	
}
