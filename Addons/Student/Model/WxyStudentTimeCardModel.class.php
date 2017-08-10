<?php

namespace Addons\Student\Model;
use Think\Model;

/**
 * Student_Card模型
 */
class WxyStudentTimeCardModel extends Model{
    public function __construct() {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }
        parent::__construct ();
    }

    public function addStudentTimeCard($data){
        $map ['token'] = $data['token'];
        $map ['studentno'] = $data['studentno'];
        $map['name'] = $data['name'];

        $dbData = $this->where($map)->select();
/*        var_dump('data = ', $data);
        var_dump('---- dbData = ', $dbData);*/
        if ($this->where($map)->select() != NULL)
            return false;
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
