<?php

namespace Addons\DailyTime\Model;
use Think\Model;

/**
 * DailyTime模型
 */
class WxyStudentimportModel extends Model{
    /**
    * Student模型
    */
    public function addImport($data){
        $res = $this->add ( $data );
        return $res;
    }
}
