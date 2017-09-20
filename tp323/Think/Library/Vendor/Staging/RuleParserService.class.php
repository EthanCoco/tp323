<?php
class RuleParserService {
    private $rule;
    //类成员变量，只能直接赋值但不能直接进行逻辑运算！
    public static $fns;
    public static $fns_instance;

    function __construct($rule) {
        //instanceof 用于确定一个 PHP 变量是否属于某一类 class 的实例
        self::$fns = new ReflectionClass('Functions');
        self::$fns_instance = self::$fns->newInstanceArgs();
        $is_json = $this->is_json($rule);
        if($is_json == true){
            //规则json转成数组
            $this->rule = json_decode($rule,true);
        }else{
            echo 'rule不是合法的json数据'; 
            return false;
        }
        $this->validate();
    }

    //判断数据是合法的json数据
    function is_json($string){
     json_decode($string);
     return (json_last_error() == JSON_ERROR_NONE);
    }

    //验证规则
    public function validate() {
        if(!is_array($this->rule)){
            //产生异常，如果rule非list类型
            echo 'rule不是合法的json数据'; 
            return false;
        }
        if (sizeof($this->rule) < 2) {
            //产生异常，rule至少需要一个操作数
            echo 'rule至少需要一个操作数'; 
            return false;
        }
    }

    public $index = 0;

    public function _evaluate($rule,$fns,$instance) {
        $res =  $this->array_recurse($rule,$fns,$instance);
        //$r = array('+',5,10);
        //实例化类
       $translate_array = $instance::$ALIAS; 
       $translate_keys = array_keys($translate_array);
//		exit(var_dump($translate_keys));
        //是否在key值
        if(in_array($res[0],$translate_keys)){
           $res[0] = $translate_array[$res[0]];
        }
        $operate_type = array_shift($res);
        //参数
        $args = $res;
        $method = $fns->getMethod($operate_type);
        $result = $method->invoke($instance,$args);
        return $result;
    }

    function array_recurse($args,$fns,$instance){
        foreach($args as $k => $v){
            if(is_array($v)){
                $args[$k] = $this->_evaluate($v,$fns,$instance);
            }
        }
        return $args;
    }

   



    //计算
    public function evaluate() {
        $result = $this->_evaluate($this->rule,self::$fns,self::$fns_instance);
        return $result;
    }



}

class Functions {

    //需要转义的函数列表
    static $ALIAS = array(
        '='     => 'eq',
        '!='    => 'neq',
        '>'     => 'gt',
        '>='    => 'gte',
        '<'     => 'lt',
        '<='    => 'lte',
        'and'   => 'and_',
        'in'    => 'in_',
        'or'    => 'or_',
        'not'   => 'not_',
        'str'   => 'str_',
        'int'   => 'int_',
        '+'     => 'plus',
        '-'     => 'minus',
        '*'     => 'multiply',
        '/'     => 'divide',
    );

    function eq($args) {
        return $args[0] == $args[1];
    }

    function neq($args) {
        return $args[0] != $args[1];
    }

    function gt($args) {
        return $args[0] > $args[1];
    }

    function gte($args) {
        return $args[0] >= $args[1];
    }

    function lt($args) {
        return $args[0] < $args[1];
    }

    function lte($args) {
        return $args[0] <= $args[1];
    }

    function and_($args) {
        $result = true;
        foreach($elem as $args) {
            $result = $return and $elem;
            if (!$result) {
                return false;
            }
        }
        return true;
    }

    function in_($args) {
        return in_array($args[0], $args[1]);
    }

    function or_($args) {
        $result = false;
        foreach ($elem as $args) {
            $result = $return or $elem;
            if ($result) {
                return true;
            }
        }
        return false;
    }

    function not_($args) {
        return !$args[0];
    }

    function str_($args) {
        unicode_decode($args[0], $unicodestr);
        return $unicodestr;
    }

    function int_($args) {
        return (int)$args[0];
    }

    function plus($args) {
        return array_sum($args);

    }

    function minus($args) {
        return $args[0] - $args[1];

    }

    function multiply($args) {
        return $args[0] * $args[1];

    }

    function divide($args) {
        return (float)$args[0] / (float)$args[1];

    }

    function abs($args) {
        return abs($args[0]);
    }

    function upper($args) {
        return strtoupper($args[0]);
    }

    function lower($args) {
        return strtolower($args[0]);
    }


    //bcmath 任意精度数学计算函数
    function bcadd($args) {
        //此可选参数用于设置结果中小数点后的小数位数
        return bcadd($args[0], $args[1], (int)$args[2]);
    }

    function bcsub($args) {
        return bcsub($args[0], $args[1], (int)$args[2]);
    }

    function bcmul($args) {
        return bcmul($args[0], $args[1], (int)$args[2]);
    }

    function bcdiv($args) {
        return bcdiv($args[0], $args[1], (int)$args[2]);
    }

    function bccomp($args) {
        return bccomp($args[0], $args[1], (int)$args[2]);
    }

    function bcmod($args) {
        return bcmod($args[0], $args[1]);
    }

    function bcpow($args) {
        return bcpow($args[0], $args[1], (int)$args[2]);
    }

    function bcsqrt($args) {
        return bcsqrt($args[0], (int)$args[1]);
    }

}