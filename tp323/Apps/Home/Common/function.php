<?php
    /**
	 * add by lijl 生成验证码
     * @param int $width 宽度
     * @param int $height 高度
     * @param int $font_size 字体大小
     * @param int $code_len 验证码长度
     * @param int $line_num 线条长度
     * @param string $font 字体名称
     * @param int $interference 雪花数量
     */
    function verifyCode($width = 100, $height = 32, $font_size = 13, $code_len = 4, $line_num = 5, $font = './Public/ttf/5.ttf', $interference = 40,$verifyName = 'code'){
        $image = imagecreatetruecolor($width, $height);
        $image_color = imagecolorallocate($image,mt_rand(157,255), mt_rand(157,255), mt_rand(157,255));
        imagefilledrectangle($image,0,$height,$width,0,$image_color);
        $x = $width/$code_len;
        $codeStrs = '';
        for($i = 0; $i<$code_len;$i++){
            $str = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $font_color = imagecolorallocate($image,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
            $codeStrs .= $codeStr = utf8_encode($str[mt_rand(0,strlen($str)-1)]);

            imagettftext($image,$font_size,mt_rand(-30,30),$x*$i+mt_rand(1,3),$height / 1.4,$font_color,$font,$codeStr);
        }
        session($verifyName,md5(strtolower($codeStrs)));
        //生成线条
//      for($i = 0;$i<$line_num;$i++) {
//          $line_color = imagecolorallocate($image, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
//          imageline($image, mt_rand(0,$width),mt_rand(0,$height),mt_rand(0,$width),mt_rand(0,$height), $line_color);
//      }
//      for($i=0;$i<$interference;$i++){
//          $color = imagecolorallocate($image, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
//          imagestring($image,mt_rand(1,5),mt_rand(0,$width),mt_rand(0,$height),'*',$color);
//      }
        header("Content-type: image/png");
        imagepng($image);
        imagedestroy($image);
    }

/**
 * add by lijl  检测验证码方法
 * @param string $code 传入的验证码
 * @param string $verifyName session里面的key值
 **/
function checkcode($code,$verifyName='code'){
    $str =strtolower($code);
    return session($verifyName) == MD5($str);
}


/**
 * add by lijl 大小写转换
 * @param string $str 要转换的字符串
 * @param int    $type转换模式 1是首字母转为大写 2是换为小写
 **/
function letterChange($str,$type=1){
    if($type == 1){
        return ucfirst(trim($str));
    }else{
        return strtolower(trim($str));
    }
}

/**
 * add by lijl 创建通用model 
 * @param $module 模块名称
 * @param $model 表名（不带前缀）
 */
function createConmmonModel($module = 'Home',$model){
	$module = ucfirst(strtolower($module));
	$model = strtolower($model);
	$modelN = ucfirst($model);
	
	$tableID = "";
	$fileds = "";
	$filename   = APP_PATH.$module.'/Model/'.$modelN."Model.class.php";
    if(file_exists($filename)){
    	echo("<meta charset='UTF-8'>");		
        die($filename.'文件已经存在');
    }
    $str        = "<?php\r\n";
    $str       .= 'namespace '.$module."\Model;\r\n";
	$str   .= "use Think\Model;\r\n";
	
	
    $str   .= 'class '.$modelN."Model extends Model{\r\n";
	
	$str   .= "\tprotected  $"."_validate=[\r\n";
	$db = \Think\Db::getInstance();
	$fieldsInfo = $db->getFieldsLijl(C('DB_PREFIX').$model);
	$ftr = "";
	for($i=0;$i<count($fieldsInfo);$i++){
		$ftr .="\t\t['".$fieldsInfo[$i]['field']."',";
		if($fieldsInfo[$i]["null"] == "NO"){
			$ftr .="'require','【字段】不能为空！',";
		}
		
		if($fieldsInfo[$i]["extra"] == "auto_increment"){
			$tableID = $fieldsInfo[$i]["field"];
		}
		if($i == count($fieldsInfo)-1){
			$fileds .= $fieldsInfo[$i]['field'];
		}else{
			$fileds .= $fieldsInfo[$i]['field'].",";
		}
		
		
		$ftr .="],\r\n";
	}
	$str   .= $ftr;
	$str   .= "\t];\r\n";
	
	//exit(var_dump($fieldsInfo));
	
	//list方法
	$listStr = "\r\n";
	$listStr .= "\t/**\r\n";
	$listStr .= "\t * 查询表数据\r\n";
	$listStr .= "\t * @type type=1 全部查询，type=2 分页查询\r\n";
	$listStr .= "\t**/\r\n";
	
	$listStr .= "\tpublic function listInfo($"."type = 1, $"."orderInfo = '', $"."offset = null ,$"."rows = null , $"."where=[]){\r\n";
	$listStr .="\t\tif($"."type == 2){\r\n";
	$listStr .="\t\t\t$"."infos = M('".$model."')->where($"."where)\r\n";
	$listStr .="\t\t\t\t\t ->field('".$fileds."')\r\n";
	$listStr .="\t\t\t\t\t ->order($"."orderInfo)\r\n";
	$listStr .="\t\t\t\t\t ->limit($"."offset, $"."rows)\r\n";
	$listStr .="\t\t\t\t\t ->select();\r\n";
	$listStr .="\t\t\t$"."count = M('".$model."')->where($"."where)->count();\r\n";
	$listStr .="\t\t}else{\r\n";
	$listStr .="\t\t\t$"."infos = M('".$model."')->where($"."where)\r\n";
	$listStr .="\t\t\t\t\t ->field('".$fileds."')\r\n";
	$listStr .="\t\t\t\t\t ->order($"."orderInfo)\r\n";
	$listStr .="\t\t\t\t\t ->select();\r\n";
	$listStr .="\t\t\t$"."count = M('".$model."')->where($"."where)->count();\r\n";
	$listStr .="\t\t}\r\n";
	$listStr .="\t\treturn['infos' => $"."infos , 'count' => $"."count];\r\n";
	$listStr .="\t}\r\n";
	$str   .=$listStr;
	
	if($tableID != ""){
		//通过id查询
		$idStr = "\r\n";
		$idStr .= "\t/**\r\n";
		$idStr .= "\t * 通过ID查询信息\r\n";
		$idStr .= "\t * @id 只需传入数字即可\r\n";
		$idStr .= "\t**/\r\n";
		$idStr .= "\tpublic function findByID($"."id, $"."orderInfo = '',  $"."where=['1' => 1]){\r\n";
		
		$idStr .="\t\t$"."infos = M('".$model."')->where(['".$tableID."' => $"."id])\r\n";
		$idStr .="\t\t\t\t\t ->where($"."where)\r\n";
		$idStr .="\t\t\t\t\t ->field('".$fileds."')\r\n";
		$idStr .="\t\t\t\t\t ->order($"."orderInfo)\r\n";
		$idStr .="\t\t\t\t\t ->find();\r\n";
		$idStr .="\t\treturn['infos' => $"."infos];\r\n";
		$idStr .="\t}\r\n";
	}
	$str   .= $idStr;	
	
	//exit(var_dump());
	
	$str   .= "\r\n";
	$str   .= '}';
    if (!$head=fopen($filename, "w+")) {//以读写的方式打开文件，将文件指针指向文件头并将文件大小截为零，如果文件不存在就自动创建
        die("尝试打开文件[".$filename."]失败!请检查是否拥有足够的权限!创建过程终止!");
    }
    if (fwrite($head,$str)==false) {//执行写入文件
        fclose($head);
    }
    fclose($head);
}




/**
 * add by lijl 创建表
 * @param string $tablename 表名
 * @param string $comment 注释
 **/
function createMysqlTable($tablename = 'test',$comment = ''){
	/*
	 * $arr 参数解析 
	 * 参数1（id）	字段名
	 * 参数2（int）	字段类型
	 * 参数3（10）	字段长度
	 * 参数4（1）		是否可以为空 1=NOT NULL 0=NULL
	 * 参数5（1）		是否自增长 1=自增长 0或其他 不做处理（只针对主键）
	 * 参数6（1）		主键标识 1=主键  0 =其他  （全部有主键）
	 * 参数7			字段注释
	 * 说明  只提供一般，如有出入  自行修改
	 */
	
    $arr = [
    			array('id'  , 'int'    ,	'10' ,	1 ,	1	,1,'主键'),
    			array('name', 'varchar',	'255',	0 ,	0	,0,'姓名'),
    		];
	$primary = "";
    $dataStr = "CREATE TABLE `".C('DB_PREFIX').$tablename."` (\r\n";
    foreach ($arr as $key => $value) {
        foreach ($value as $k => $v) {
            switch ($k) {
                case 0:
                    $dataStr .= ' `'.$v.'` ';
                    break;
                case 1:
                    $dataStr .= $v.'('.$value[2].') ';
                    break;
                case 3:
                    if($v == 1){
                        $dataStr .= ' NOT NULL ';
                    }else{
                    	$dataStr .= ' DEFAULT NULL ';
                    }
                    break;
				case 4:
					if($v == 1)
                        $dataStr .= ' AUTO_INCREMENT ';
					break;
				case 5:
					if($v == 1)
						$primary =   "PRIMARY KEY (`".$value[0]."`)";
					break;
                case 6:
                    $dataStr .= " COMMENT '".$v."' ,\r\n";
                    break;
                default:
                    # code...
                    break;
            }
        }
    }
	$dataStr .= $primary."\r\n";
	$dataStr .=")ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='".$comment."';";
	
    echo '<pre><meta charset="UTF-8">';
    print_r($dataStr);
}

function dbexport(){
	exec("D:/wamp/bin/mysql/mysql5.6.17/bin/mysqldump -u$cfg_dbuser -p$cfg_dbpwd --default-character-set=utf8 $cfg_dbname > ".$tmpFile);
	$dbName = C('DB_NAME');   //读取配置文件中的数据库用户名、密码、数据库名
    $dbUser = C('DB_USER');
    $dbPwd  = C('DB_PWD');
    $fileName = time()."_".$dbName.".sql";
    $dumpFileName = "./Backdata/".$fileName;
    exec("D:/wamp/bin/mysql/mysql5.6.17/bin/mysqldump -R -u$dbUser $dbName > $dumpFileName"); 
}

/**
 * 计算规则
 * @param rp_it_id 模板ID
 * @param info Array 计算参数值
 */
function caculate_rule_parser($rp_it_id,$info = []){
	header('Content-Type:text/html;charset=utf-8');
    Vendor("Staging.RuleParserService#class");
    
    $search = [];
    foreach($info as $k=>$v){
		${$k} = $v;
		array_push($search,$k);
	}
    
	$rp_field_list = M('RuleParser')->where(['rp_it_id'=>$rp_it_id])->group('rp_field')->order('rp_sort asc , rp_id asc')->getField('rp_field',true);
    
    $result = [];
    foreach($rp_field_list as $val){
        $rule_list = M('RuleParser')->where(['rp_it_id'=>$rp_it_id,'rp_field'=>$val])->select();
        $rule = "";
        foreach($rule_list as $k => $v){
            $replace = [];
            foreach($search as $m) {
                //替换的值
                $replace[] = ${$m};
            }
            $condition = true;
            if(replace_trans_info($v['rp_condition'])){
                $condition_rule = replace_trans_info($v['rp_condition']);
                //替换
                $condition_rule = str_replace($search,$replace,$condition_rule);
				
                $rule_parser = new  \RuleParserService($condition_rule);
                $condition = $rule_parser->evaluate();
            }
            if($condition == true){
                $rule = replace_trans_info($v['rp_rule']);
                break;
            }
			
        }
        //获取赋值
        $search[] = $val;
		//替换
        $rule = str_replace($search,$replace,$rule);
        $rule_parser = new \RuleParserService($rule);
        $res = $rule_parser->evaluate();
        $result[$val] = $res;
		//exit(var_dump($res));
        $search[] = $val;
        ${$val} = $res; 
    }
//  $result['sum_lixi'] = $result['hetongjia'] - $loancach;
//  $son_lixi = bcdiv($result['sum_lixi'],$loanage,2);
//  $m = [];
//  for($i = 0;$i<$loanage;$i++){
//  	if($i==0){
//  		$m[]  = [
//      		'yuehuan'=>bcsub($result['hetongjia'] ,bcmul(bcdiv($result['yuehuan'],1,2),($loanage-1),2),2),
//      		'son_lixi'=>bcsub($result['sum_lixi'],bcmul($son_lixi,($loanage-1),2),2)
//      	]; 
//  	}else{
//  		$m[]  = [
//      		'yuehuan'=>bcdiv($result['yuehuan'],1,2),
//      		'son_lixi'=>$son_lixi
//      	]; 
//  	}
//  	
//  }
//  
//  
//  
//  $result['detail'] = $m;
	return $result;
}

function replace_trans_info($str){
	$transInfo = [
		'"' => '&quot;',
		'<' => '&lt;',
		'>' => '&gt;',
	];
	
	foreach($transInfo as $key=>$val){
		if(strpos($str,$val)){
			$str = str_replace($val,$key,$str);
		}
	}
	return $str;
}
