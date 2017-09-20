<?php
namespace Home\Controller;
/**
 * 公共方法
 */
class SharefunController extends PrivateController {
	public function _initialize(){
		$infos = parent::_initialize(1);
	}
	//获取代码列表
	public function getCodeInfoList(){
		$code = I('post.code','');
		$datas = M('codeinfo_'.LANG_SET)->field("codevalue as id,codeflag,codename as text")->where(['codeflag'=>$code])->select();
		echo json_encode($datas);
	}
	
	//不定参数的获取代码数据
	public function getMulCodeInfoList(){
		$codeflag = I('post.codeflag');
		$codeInfos = M('codeinfo_'.LANG_SET)->field("codeflag,codevalue, codename")->where('codeflag in ('. $codeflag .')')->select();
		echo json_encode($codeInfos);
	}
	
	//调用第三方软件 生成PDF文件
	public function wkhtmltopdf(){
		shell_exec("D:/softall/wkhtmltopdf/bin/wkhtmltopdf.exe http://www.baidu.com/ ./Uploads/Pdf/2.pdf");
		
	}
	
	//获取省市区
	public function getregion(){
		$Region = M("area");
	    $param['pid']=I('get.pid','');
	    $param['level']=I('get.type','');
	    $list = $Region->field("id,pid,level,CASE '" . LANG_SET . "' WHEN 'zh-cn' THEN name ELSE nameen END AS name")->where($param)->select();
	    echo json_encode($list);
	}
	
	/**
	 * 上传图片 并 生成缩略图
	 */
	public function upload(){
		$file = $_FILES['userimageurl'];
		$type = $_FILES['userimageurl']["type"];
		$timeNow = date('Y-m-d H:i:s',time());
		$timeNowMonth = date('Ym',time());
		$type = substr($_FILES['userimageurl']['name'], strpos($_FILES['userimageurl']['name'], '.')+1);
		$tmpfile = time();
		$fileName = $tmpfile.'.'.$type;
		
		//判断图片格式
		if(!in_array($type, ['jpg','gif','png','JPG','GIF','PNG'])){
			die(json_encode(['pic'=>$fileName,'result'=>'-1']));
		}
		//判断图片大小
		if($_FILES['userimageurl']['size'] > 2*1024*1024){
			die(json_encode(['pic'=>$fileName,'result'=>'-2']));
		}
		//存储路径（项目）
		$createDir = './Uploads/Images/'.$timeNowMonth;
		//存储路径（数据库）
		$saveDir = __ROOT__ . '/Uploads/Images/'.$timeNowMonth;
		//创建目录
		mkdirs($createDir);
		move_uploaded_file($_FILES['userimageurl']['tmp_name'], $createDir."/".$fileName);
		
		//生成缩略图
		$image = new \Think\Image();
		$image->open($createDir."/".$fileName);
		
		$thumbName = explode('.', $fileName)[0].'_thumb.jpg';
		$image->thumb(150,150,\Think\Image::IMAGE_THUMB_CENTER)->save($createDir."/".$thumbName);
		die(json_encode(['result'=>$saveDir."/".$thumbName]));
	}
}
