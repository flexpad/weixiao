<?php

namespace Addons\Weixiao\Model;
use Think\Model;

/**
 * Student_Card模型
 */
class WxyStudentCardModel extends Model{
    public function addStudent($data){
        $map ['token'] = $data['token'];
        $map ['studentno'] = $data['studentno'];
        //var_dump($map);
        //var_dump($this->where[$map]);
        if ($this->where($map)->select() != NULL) {
            return false;
        } else {
            $res = $this->add ( $data );
            return $res;   
        }           
    }
    
    public function verify($map) {
        $data = $this->where($map)->find();
        if ($data != NULL) {
            return $data;
        }
        else {
            $map['phone_bck'] = $map['phone'];
            unset($map['phone']);
            $data = $this->where($map)->find();
            if ($data != NULL ) return $data;
            else
                return false;
        }

    }
}
