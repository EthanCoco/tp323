<?php
/**
 * add by lijl 生成随机数
 **/
function randNum(){
    return mt_rand(1000,99999999);
}

/**
 * add by lijl 加密函数
 * @param string $str 要加密的字符串
 * @param string $rand 加密随机字符
 * @return string $chars 加密后的字符串
 **/
function md5Encrypt($str='',$rand=''){
    $hash = $str.$rand;
    $chars =  MD5(hash('sha256', $hash));
    return $chars;
}
/**
 * add by lijl 删除缓存文件
 * @param string $dir 默认temp目录
 **/
function delTemp($dir = TEMP_PATH){
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
       if ($file != "." && $file != "..") {
           $fullpath = $dir . "/" . $file;
           if (!is_dir($fullpath)) {
                unlink($fullpath);
           } else {
                delTemp($fullpath);
           }
       }
    }
    closedir($dh);
    if (rmdir($dir)){
        return true;
    }
    return false;
}

/**
 * add by lijl 将key相同的数组合并为新的数组
 * @param array $arr 接收要组装的二维数组
 **/
function arrAssembly($arr){
    $arr_new = array();
    foreach($arr as $item){
        foreach($item as $key=>$val){
            $arr_new[$key][] = $val;
        }
    }
    return $arr_new;
}

/**
 * add by lijl 格式化时间
 * @param $date 传入要格式化的时间
 * @param $type 格式类型
 * @return 处理好的时间
 **/
function formatTime($date, $type=1){
    switch($type){
        case 1:
            $date = date('Y-m-d H:i:s',$date);
            break;
        case 2:
            $date = date('Y-m-d',$date);
            break;
        case 3:
            $date = date('y-m-d',$date);
            break;
    }
  	return $date;
}


/**
 * add by lijl 切割图片
 **/
function getThumb($url='', $width=null, $height=null){
    if(empty($url)){
        return '';
    }
    if(is_null($width)){
        $width = 100;
    }
    if(is_null($height)){
        $height = $width;
    }
    $tmpArr = explode('/', $url);
    $name = array_pop($tmpArr);
    $allname = implode('/', $tmpArr) ."/thumb/{$width}x{$height}/" . $name;
    return $allname;
}
/**
 * add by lijl  字符串截取
 * @param string $sourcestr 要截取的内容
 * @param string $cutlength 指定长度
 **/
function cut_str($sourcestr,$cutlength){
    $returnstr='';
    $i=0;
    $n=0;
    $str_length=strlen($sourcestr);//字符串的字节数
    while (($n<$cutlength) and ($i<=$str_length)){
        $temp_str=substr($sourcestr,$i,1);
        $ascnum=Ord($temp_str);//得到字符串中第$i位字符的ascii码
        if ($ascnum>=224){ //如果ASCII位高与224，
            $returnstr=$returnstr.substr($sourcestr,$i,3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
            $i=$i+3; //实际Byte计为3
            $n++; //字串长度计1
        }elseif ($ascnum>=192){ //如果ASCII位高与192，

            $returnstr=$returnstr.substr($sourcestr,$i,2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
            $i=$i+2; //实际Byte计为2
            $n++; //字串长度计1
        }elseif ($ascnum>=65 && $ascnum<=90){ //如果是大写字母，
            $returnstr=$returnstr.substr($sourcestr,$i,1);
            $i=$i+1; //实际的Byte数仍计1个
            $n++; //但考虑整体美观，大写字母计成一个高位字符
        }else{ //其他情况下，包括小写字母和半角标点符号，
            $returnstr=$returnstr.substr($sourcestr,$i,1);
            $i=$i+1; //实际的Byte数计1个
            $n=$n+0.5; //小写字母和半角标点等与半个高位字符宽...
        }
    }
    if ($str_length>$cutlength){
        $returnstr = $returnstr."...";//超过长度时在尾处加上省略号
    }
    return $returnstr;
}
/**
 * add by lijl 生成随机字符串
 **/
function getRandStr($length=8) {
    $str = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $randString = ''; 
    $len = strlen($str)-1; 
    for($i = 0;$i < $length;$i ++){ 
        $num = mt_rand(0, $len); 
        $randString .= $str[$num]; 
    } 
    return $randString ; 
}

/**
 * add by lijl 逗号中文转英文
 **/
function bianma($str){
   return str_replace('，',',',$str);
}

/**
 * @param $str 要加密的字符串
 * @return string 加密后的字符串
 */
function homeUserPwd($str){
    $hash = Md5($str);
    $str = MD5(hash('sha256', $hash));
    return $str;
}

/**
 * @param $file 缓存文件名
 * @param int $time 缓存时间
 */
function check_cache($file, $time = 0){
    if($time == 0){
        $time = C('CACHE_TIME');
    }
    if (is_file($file) && (time() - filemtime($file)) < $time) {
        require_once $file;
        exit;
    }
}

/**
 * @param $file 生成静态页面
 */
function create_cache($file){
    file_put_contents($file,ob_get_contents());
}

/**
 * 创建目录 如果目录不存在则创建目录
 */
function mkdirs($dir, $mode = 0777){
    if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
    if (!mkdirs(dirname($dir), $mode)) return FALSE;
    return @mkdir($dir, $mode);
} 

/**
 * add by lijl 导出excel
 * @param $type =2 导出数据类型 1表示当前页 2表示所有
 * @param $dataInfos 二维数组格式数据
 * @param $num	从excel的第几行读取
 * @param $sheetIndex excel工作表
 * @param $keys excel表与数据对应的键 数组
 * @param $loadModelName	需要加载excel模板的名称（注意，不带后缀）
 * @param $titleName='infos'	工作表名称
 */
function export_common_excel($type = 2,$dataInfos,$num,$sheetIndex,$keys,$loadModelName,$titleName = 'infos'){
	require APP_PATH . 'Common/Lib/PHPExcel.class.php';
	ini_set('memory_limit', '2048M');
	ini_set('display_errors',1);
	set_time_limit(0);
	error_reporting(E_ALL);
	
	//文件名
	$prex_name  = $type == 2 ? L('HOME_COMMON_ALLPAGE') : L('HOME_COMMON_CURRENTPAGE');
	$excelName = $prex_name . date('YmdHis');
	
	//读取excel模板
	$objReader = PHPExcel_IOFactory::createReader('Excel5');
	$objPHPExcel = $objReader->load(APP_PATH . C('DEFAULTS_MODULE') . '/Excelmodels/' . LANG_SET . '/' . $loadModelName . '.xls' );
	$objPHPExcel -> getSheet($sheetIndex) -> setTitle($titleName);
	//writeSheetCellValue($objPHPExcel,$num,$dataInfos,$sheetIndex);//写入数据
	
	//写入数据
	foreach($dataInfos as $info){
		$column = count($info);
		$temp = 0;
		for($n = 0; $n <$column; $n++){
			if($temp == $column){
				break;
			}else{
				$pcoordinate = PHPExcel_Cell::stringFromColumnIndex($n).''.$num;
				if($keys[$temp]=='id'){
					$objPHPExcel->setActiveSheetIndex($sheetIndex)->setCellValue($pcoordinate, ($num-1));
				}else{
					$objPHPExcel->setActiveSheetIndex($sheetIndex)->setCellValue($pcoordinate, ' ' . $info[$keys[$temp]] . ' ');
				}
	            $temp++;
			}
		}
		$num++;
	}
	
	//清除缓冲区,避免乱码
	ob_end_clean();
	$filename = iconv("utf-8","gb2312",$excelName);
	header ( 'Content-Type: application/vnd.ms-excel' );
	header ( 'Content-Disposition: attachment;filename="'.$filename.'.xls"'); 
	header ( 'Cache-Control: max-age=0' );
	$objWriter = PHPExcel_IOFactory::createWriter ($objPHPExcel,'Excel5'); //在内存中准备一个excel2003文件
	$objWriter->save ( 'php://output' );
	//写入日志
	writeSysLog(6,'导出excel','Export Excel');
	exit;
}

/**
 * add by lijl 日志写入
 * @param $type	记录类型（1=登录，2=添加，3=删除，4=修改，5=查询）
 * @param $content 记录内容
 */
function writeSysLog($type = 0,$content = "未知操作！",$contenten = "Unknown Operation!"){
	$time = date('Y-m-d H:i:s'); 
	$ipaddr = get_client_ip();
	$userid = session(C('IS_LOGIN'));
	$username = session(C('USERNAME'));
	$Model = M('log');
	//$Model = new \Think\Model() // 实例化一个model对象 没有对应任何数据表
	$sql = "INSERT INTO `" . C('LOG_DB_TABLE') . "` (`type`,`content`,`userid`,`username`,`time`,`ipaddr`,`contenten`)  VALUES ('$type','$content','$userid','$username','$time','$ipaddr','$contenten')"; 
	$Model->execute($sql);
}

/**
 * add by lijl 代码数据
 * @param $codeArr 代码标识
 */
function getCodeInfos($codeArr=[],$data=''){
    $codeNameArr = [];//代码名称数组
    foreach($codeArr as $code){
        $codeVal = $data[$code[0]];
        $codeFind = M('codeinfo_'.LANG_SET)->where(['codevalue'=>$codeVal,'codeflag'=>$code[1]])->find();
        if(!empty($codeFind)){
            $codeNameArr[$code[0]] = $codeFind['codename'];
        }else{
            $codeNameArr[$code[0]] = '';
        }
    }
    return $codeNameArr;
}
 
/**
 * add by lijl 邮件发送函数
 */
function sendMail($to, $title, $content) {
    require APP_PATH . 'Common/Lib/PHPMailer/class.smtp.php';
    require APP_PATH . 'Common/Lib/PHPMailer/class.phpmailer.php';

    $mail = new \PHPMailer(); //实例化
    $mail->IsSMTP(); // 启用SMTP
    $mail->Host=C('MAIL_HOST'); //smtp服务器的名称（这里以163邮箱为例）
    $mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
    $mail->Username = C('MAIL_USERNAME'); //你的邮箱名
    $mail->Password = C('MAIL_PASSWORD') ; //邮箱密码
    $mail->From = C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
    $mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
    $mail->AddAddress($to);	//收件人地址
    $mail->WordWrap = 50; //设置每行字符长度
    $mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
    $mail->CharSet=C('MAIL_CHARSET'); //设置邮件编码
    $mail->Subject =$title; //邮件主题
    $mail->Body = $content; //邮件内容
    $mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
    return($mail->Send());
}

/**
 * add by lijl 生成二维码（简单）
 */
function qrcodeCommon($url="www.baidu.com",$level=3,$size=4){
	require APP_PATH . 'Common/Lib/phpqrcode/phpqrcode.php';
	$errorCorrectionLevel =intval($level) ;//容错级别 
  	$matrixPointSize = intval($size);//生成图片大小 
 	//生成二维码图片 
 	$object = new \QRcode();
  	$object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);  
}



/** 
* @param string $string 原文或者密文 
* @param string $operation 操作(ENCODE | DECODE), 默认为 DECODE 
* @param string $key 密钥 
* @param int $expiry 密文有效期, 加密时候有效， 单位 秒，0 为永久有效 
* @return string 处理后的 原文或者 经过 base64_encode 处理后的密文 
* 
* @example 
* 
*  $a = authcode('abc', 'ENCODE', 'key'); 
*  $b = authcode($a, 'DECODE', 'key');  // $b(abc) 
* 
*  $a = authcode('abc', 'ENCODE', 'key', 3600); 
*  $b = authcode('abc', 'DECODE', 'key'); // 在一个小时内，$b(abc)，否则 $b 为空 
*/  
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 3600){  
    $ckey_length = 4;     
    // 随机密钥长度 取值 0-32;  
    // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。  
    // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方  
    // 当此值为 0 时，则不产生随机密钥  
    $key = md5($key ? $key : EABAX::getAppInf('KEY'));  
    $keya = md5(substr($key, 0, 16));  
    $keyb = md5(substr($key, 16, 16));  
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';  
    $cryptkey = $keya.md5($keya.$keyc);  
    $key_length = strlen($cryptkey);  
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('0d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;  
    $string_length = strlen($string);  
    $result = '';  
    $box = range(0, 255);  
    $rndkey = array();  
    for($i = 0; $i <= 255; $i++)   
    {  
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);  
    }  
    for($j = $i = 0; $i < 256; $i++)   
    {  
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;  
        $tmp = $box[$i];  
        $box[$i] = $box[$j];  
        $box[$j] = $tmp;  
    }  
    for($a = $j = $i = 0; $i < $string_length; $i++)   
    {  
        $a = ($a + 1) % 256;  
        $j = ($j + $box[$a]) % 256;  
        $tmp = $box[$a];  
        $box[$a] = $box[$j];  
        $box[$j] = $tmp;  
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));  
    }  
    if($operation == 'DECODE')   
    {  
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16))   
        {  
            return substr($result, 26);  
        }   
        else   
        {  
            return '';  
        }  
    }   
    else   
    {  
        return $keyc.str_replace('=', '', base64_encode($result));  
    }  
} 

function assoc_unique(&$arr, $key){
	$rAr=array();
	for($i=0;$i<count($arr);$i++){
		if(!isset($rAr[$arr[$i][$key]])){
			$rAr[$arr[$i][$key]]=$arr[$i];
		}
	}
	$arr=array_values($rAr);
}






