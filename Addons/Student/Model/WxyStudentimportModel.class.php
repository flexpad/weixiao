<?php

namespace Addons\Student\Model;
use Think\Model;

/**
 * Student模型
 */
class WxyStudentimportModel extends Model{
    public function addImport($data){
        $res = $this->add ( $data );
        return $res;
    }

}
