<?php

namespace Addons\Student\Model;
use Think\Model;

/**
 * Student_Card模型
 */
class WxyRfidDataModel extends Model{
    public function __construct() {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }
        parent::__construct ();
    }

    //增量添加记录
    public function addRfidCard($data){
        $map ['token'] = $data['token'];
        $map ['serial_no'] = $data['serial_no'];

        $dbData = $this->where($map)->select();
/*        var_dump('data = ', $data);
        var_dump('---- dbData = ', $dbData);*/
        if ($dbData != NULL)
            return $this->where($map)->save($data);
        else
        {
            $res = $this->add ( $data );
            return $res;   
        }           
    }
    
    public function verify($map) {
        $data = $this->where($map)->find();
        if ($data != NULL) {
            return $data;
        }
        else
            return false;
    }
}
