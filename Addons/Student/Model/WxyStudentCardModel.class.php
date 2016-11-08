<?php

namespace Addons\Student\Model;
use Think\Model;

/**
 * Student模型
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
}
