<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2016/11/23
 * Time: 15:45
 */
namespace Addons\Course\Model;
use Think\Model;
/*
 * Scoreimport 模型
 */
class WxyScoreimportModel extends Model{
    public function addImport($data){
        $res = $this->add ( $data );
        return $res;
    }
}
