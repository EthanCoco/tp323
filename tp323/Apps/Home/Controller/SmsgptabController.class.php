<?php
namespace Home\Controller;

class SmsgptabController extends PrivateController {
	public function _initialize(){
		$infos = parent::_initialize(1);
	}
	
	public function sendApi(){
		$key = '4dhswfjdfjdh92343bhjerjewhfd';
		$phones = '111;222;333;444;555;';
		$mid = 34512;
		$comCode = '201705251521';
		$deptCode = '09888273743';
		$content = urlencode('#code#=207678');
		
		foreach(){
			
		}
		
		
	}
	
	public function sendSms($params){
		$url = 'http://push.yuuwei.com/index.php/Sms/sendSms';

        $param = [
        	'key'=>$params['key'],
            'mobile'=>$params['phone'],
            'tpl_id'=>	$params['mid'],
            'tpl_value'=> $params['content'],
            'com_code' => $params['comCode'],
            'dept_code'	=>	$params['deptCode'],
        ];
        $httph =curl_init($url);
        curl_setopt($httph, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($httph, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($httph, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($httph, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
        curl_setopt($httph, CURLOPT_POST, 1);//设置为POST方式
        curl_setopt($httph, CURLOPT_POSTFIELDS, $param);
        curl_setopt($httph, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($httph, CURLOPT_HEADER,1);
        $rst=curl_exec($httph);
        curl_close($httph);
        return $rst;
	}
}
