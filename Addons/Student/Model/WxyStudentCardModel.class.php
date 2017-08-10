<?php

namespace Addons\Student\Model;
use Think\Model;

/**
 * Student_Card模型
 */
class WxyStudentCardModel extends Model{
    protected $studentManMode;
    public function __construct() {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }
        parent::__construct ();
        $this->studentManMode = M('WxyStudentCardManage');
    }

    public function addStudent($data){
        $map ['token'] = $data['token'];
        $map ['studentno'] = $data['studentno'];
        $map['name'] = $data['name'];
        //var_dump($map);
        //var_dump($this->where[$map]);

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
