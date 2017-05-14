<?php

namespace Addons\Weixiao\Model;
use Think\Model;

/**
 * ClassCourse模型
 */
class WxyClassCourseModel extends Model{
    /*public function addImport($data){
        $res = $this->add ( $data );
        return $res;
    }*/

    public function addCourse($data){
        $map ['token'] = $data['token'];
        $map ['valid_date'] = $data['valid_date'];
        $map ['grade'] = $data['grade'];
        $map ['class_id'] = $data['class_id'];
        $map ['course_type'] = $data['course_type'];
        $map ['course_name'] = $data['course_name'];
        //$map ['comment'] = $data['comment'];
        //$map ['classdate'] = $data['classdate'];
        //var_dump($data);
        //var_dump($this->where[$map]);
        if ($this->where($map)->select() != NULL) {
            return false;
        } else {
            $res = $this->add ( $data );
            return $res;
        }
    }
}
