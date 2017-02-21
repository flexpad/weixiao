<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2017/2/20
 * Time: 18:47
 */
namespace Addons\Course\Model;
use Think\Model;
/**
 * Score模型
 */
class WxyCourseCommentsModel extends Model{
    public function addComments($data){
        $map['studentno'] = $data['studentno'];
        $map ['courseid'] = $data['courseid'];
        $map ['token'] = $data['token'];
        $result = $this->where($map)->select();
        $student = M('wxy_student_card')->where($map)->find();
        $map ['token'] = $data['token'];
        $map ['courseid'] = $data['courseid'];
        $map ['studentno'] = $data['studentno'];
        $map ['sid'] = $student['id'];
        $map ['name'] = $student['name'];

        //$map ['classdate'] = $data['classdate'];
        //var_dump($data);
        //var_dump($this->where[$map]);
        //$result = M('wxy_course_comments')->where($map)->select();
        if ($result != NULL) {
            foreach ($result as $value) {
                if (!strcmp($value['comments_txt'], $data['comments_txt'])) 
                    return false;                
            }
        }
        $data['timestamp'] = date("Y-m-d");
        $data['name'] = $student['name'];
        $data['sid'] = $student['id'];
        $res = $this->add ( $data );
        return $res;

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
