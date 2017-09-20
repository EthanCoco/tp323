<?php
return array(
	'WEB_DOMAIN'        	=>  'http://'.$_SERVER['HTTP_HOST'],
	//'ERROR_PAGE' 			=>  '/Public/error.html'
	'DEFAULT_PWD'			=>	'1234567',							// 创建用户默认密码
	'LOG_DB_TABLE'			=>	'yy_log',
	//权限字段
	'__YWCLMU__'			=>	'userid',
	'__YWQXJM__'			=>	'auth',
	
//	'SESSION_OPTIONS' 		=> array('use_trans_sid'=>1,'use_only_cookies'=>0),
	
	// 模板字符串配置
	'TMPL_PARSE_STRING' 	=> [
		'__ADMINSTYLE__' 	=> __ROOT__.'/Public/Admin',
		'__HOMESTYLE__' 	=> __ROOT__.'/Public/Home'
	],
	
	'URL_MODE' 				=>  2,									// URL访问模式
	'URL_ROUTER_ON' 		=>  true,
	'DEFAULT_MODULE'        =>  'Home',  							// 默认模块
    'MODULE_ALLOW_LIST' 	=> 	array('Home'),
	'DEFAULT_CONTROLLER'    =>  'Manage', 							// 默认控制器名称
    'DEFAULT_ACTION'        =>  'index', 							// 默认操作名称
    
	// 数据库设置配置
	'DB_TYPE'               => 'mysql',     						// 数据库类型
    'DB_HOST'               => 'localhost', 						// 服务器地址
    'DB_NAME'               => 'tp323',     						// 数据库名
    'DB_USER'               => 'root',      						// 用户名
    'DB_PWD'                => 'root',          						// 密码
    'DB_PORT'               => '3306',      						// 端口
    'DB_PREFIX'             => 'yy_',    							// 数据库表前缀
    'DB_PARAMS'             => array(), 							// 数据库连接参数
    'DB_DEBUG'              => true, 								// 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'       => false,       						// 启用字段缓存
    'DB_CHARSET'            => 'utf8',      						// 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'        => 0, 									// 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'        => false,       						// 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'         => 1, 									// 读写分离后 主服务器数量
    'DB_SLAVE_NO'           => '', 									// 指定从服务器序号
	
	//redis 缓存配置
//	'DATA_CACHE_PREFIX' => 'rs_',//缓存前缀
//	'DATA_CACHE_TYPE'=>'Redis',//默认动态缓存为Redis
//	'REDIS_RW_SEPARATE' => false, //Redis读写分离 true 开启
//	'REDIS_HOST'=>'127.0.0.1', //redis服务器ip，多台用逗号隔开；读写分离开启时，第一台负责写，其它[随机]负责读；
//	'REDIS_PORT'=>'6379',//端口号
//	'REDIS_TIMEOUT'=>'300',//超时时间
//	'REDIS_PERSISTENT'=>false,//是否长连接 false=短连接
	
	'REDIS_HOST'            => 'localhost',                     	//主机  
    'REDIS_PORT'            => 6379,                                //端口  
    'REDIS_CTYPE'           => 1,                                   //连接类型 1:普通连接 2:长连接  
    'REDIS_TIMEOUT'         => 0,                                   //连接超时时间(S) 0:永不超时  
    'REDIS_AUTH'            => "",                     				//密码    
	
	//邮件配置
    'MAIL_HOST' 			=> 'smtp.163.com',						//smtp服务器的名称
    'MAIL_SMTPAUTH' 		=> TRUE, 								//启用smtp认证
    'MAIL_USERNAME' 		=> 'lijianlin0204@163.com',				//你的邮箱名
    'MAIL_FROM' 			=> 'lijianlin0204@163.com',				//发件人地址
    'MAIL_FROMNAME'			=> 'lijianlin',							//发件人姓名
    'MAIL_PASSWORD' 		=> 'zxfwan',							//邮箱密码
    'MAIL_CHARSET' 			=> 'utf-8',								//设置邮件编码
    'MAIL_ISHTML' 			=> TRUE, 								// 是否HTML格式邮件
	'CACHE_TIME'          	=> '600', 								//缓存时间

	// 此处配置为多语言配置	
	'LANG_SWITCH_ON' 		=> true,   								// 开启语言包功能
	'LANG_AUTO_DETECT' 		=> true, 								// 自动侦测语言 开启多语言功能后有效
	'LANG_LIST'        		=> 'en-us,zh-cn', 						// 允许切换的语言列表 用逗号分隔
	'VAR_LANGUAGE'     		=> 'l', 								// 默认语言切换变量
	//表单令牌验证
	//'TOKEN_ON' 				=> true,								// 是否开启令牌验证 默认关闭
	//'TOKEN_NAME'			=> '__hash__',							// 令牌验证的表单隐藏字段名称，默认为__hash__
	//'TOKEN_TYPE'			=> 'md5',								// 令牌哈希验证规则 默认为MD5
	//'TOKEN_RESET'			=> true,								// 令牌验证出错后是否重置令牌 默认为true
	
	//权限配置
    'AUTH_ON' 				=> true, 								//认证开关
    'AUTH_TYPE' 			=> 1, 									// 认证方式，1为时时认证；2为登录认证。
    'AUTH_GROUP' 			=> 'auth_group', 						//用户组数据表名
    'AUTH_GROUP_ACCESS' 	=> 'auth_group_access', 				//用户组明细表
    'AUTH_RULE' 			=> 'auth_rule', 						//权限规则表
    'AUTH_USER'	 			=> 'user',								//用户信息表
	'AUTH_BTN'				=> 'auth_btn',							//权限规则表下的按钮权限表
	'AUTH_BTN_ACCESS'		=> 'auth_btn_access',					//权限规则表下的用户按钮明细表
	'AUTH_BTN_ON'			=>	1,									//是否开启按钮权限，1开启；0不开启
	
	//自定义变量合集
	'IS_LOGIN' 				=>	'isLogin',							// 标识是否登录（用户ID）
	'USERNAME' 				=> 	'username',							// 用户账号
	'USERREALNAME'			=>	'name',								// 用户姓名
	'IDCARD'   				=>	'idcard',							// 身份证号码
	'USERTYPE'				=>	'usertype',							// 用户类别
	'PHONE'	   				=>	'phone',							// 用户手机号码
	'USERSEX'				=>	'usersex',							// 用户性别
	'USERIMAGEURL' 			=>	'userimageurl',						// 用户图片地址
	'DEFAULTS_MODULE'       =>  'Home',								// 默认模块
	
	'MSG_TIME'				=>	300,								// 邮件验证码时间
	'PWD_RESET'				=>	'gigiwangluoAa123',					//重置密码
	'PWD_DESC'				=>	'gigiwangluoAa123',					//加密字符secret
);