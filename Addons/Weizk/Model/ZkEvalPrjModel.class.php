<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2016/11/23
 * Time: 15:45
 */
namespace Addons\Weizk\Model;
use Think\Model;
/*
 * Eval Project 模型
 */
class ZkEvalPrjModel extends Model{
    public function create($data){
        $res = $this->add($data);
        return $res;
    }
}