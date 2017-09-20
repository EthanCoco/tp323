<?php
namespace Home\Controller;
class DictController extends \Think\Controller{
//	public function _initialize(){
//		$infos = parent::_initialize(1);
//	}
    

    public function test(){
        header('Content-Type:text/html;charset=utf-8');
        Vendor("Staging.RuleParserService#class");
		//require APP_PATH . 'Common/Lib/Staging/RuleParserService.class.php';
       	// loancach 贷款额
        // loanage 贷款期数
        // bankrate 银行利率
    
        $sm_redit = 501;
        $loancach = 100000;
        $loanage = 36;
        $search = ['loancach', 'loanage','sm_redit'];

        $rp_field_list = M('RuleParser')->where(['rp_it_id'=>27])->group('rp_field')->order('rp_id')->getField('rp_field',true);
        
        $result = [];
        foreach($rp_field_list as $val){
            $rule_list = M('RuleParser')->where(['rp_it_id'=>27,'rp_field'=>$val])->select();
            $rule = "";
            foreach($rule_list as $k => $v){
                $replace = [];
                foreach($search as $m) {
                    //替换的值
                    $replace[] = ${$m};
                }
                $condition = true;
                if($v['rp_condition']){
                    $condition_rule = $v['rp_condition'];
                    //替换
                    $condition_rule = str_replace($search,$replace,$condition_rule);
					
                    $rule_parser = new  \RuleParserService($condition_rule);
                    $condition = $rule_parser->evaluate();
                }
                if($condition == true){
                    $rule = $v['rp_rule'];
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
        $result['sum_lixi'] = $result['hetongjia'] - $loancach;
        $son_lixi = bcdiv($result['sum_lixi'],$loanage,2);
        $m = [];
        for($i = 0;$i<$loanage;$i++){
        	if($i==0){
        		$m[]  = [
	        		'yuehuan'=>bcsub($result['hetongjia'] ,bcmul(bcdiv($result['yuehuan'],1,2),($loanage-1),2),2),
	        		'son_lixi'=>bcsub($result['sum_lixi'],bcmul($son_lixi,($loanage-1),2),2)
	        	]; 
        	}else{
        		$m[]  = [
	        		'yuehuan'=>bcdiv($result['yuehuan'],1,2),
	        		'son_lixi'=>$son_lixi
	        	]; 
        	}
        	
        }
        
        
        
        $result['detail'] = $m;
        dump($result);
    }

  
}