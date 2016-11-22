<?php

namespace Addons\DailyTime\Model;
use Think\Model;

/**
 * DailyTime模型
 */
class WxyDailyTimeModel extends Model{
    public function add_attendance($data){
        $res = $this->add ( $data );
        return $res;
    }
}
