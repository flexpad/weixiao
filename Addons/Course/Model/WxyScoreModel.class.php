<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2016/11/23
 * Time: 16:02
 */
namespace Addons\Course\Model;
use Think\Model;
/**
 * Score模型
 */
class WxyScoreModel extends Model{
    public function addScore($data){
        $map ['token'] = $data['token'];
        $map ['courseid'] = $data['courseid'];
        $map ['studentno'] = $data['studentno'];
        $map ['classdate'] = $data['classdate'];
        //var_dump($data);
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
        else
            return false;
    }
}
