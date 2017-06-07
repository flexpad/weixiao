<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2016/11/23
 * Time: 16:02
 */
namespace Addons\Weizk\Model;
use Think\Model;
/**
 * Score模型
 */
class ZkHschoolModel extends Model
{
    public function addHschool($data)
    {
        $map['name'] = $data['name'];
        //$map['year'] = $data['year'];
        $map['factor_bk1'] = $data['factor_bk1'];
        $map['factor_bk2'] = $data['factor_bk2'];

        $items = $this->where($map)->select();
        if ($items != NULL ||
            $data['name'] == NULL ||
            $data['factor_bk1'] == NULL ||
            $data['factor_bk2'] == NULL) {
            //var_dump($items);
            return false;
        } else {
            //var_dump($data);
            $res = $this->add($data);
            return $res;
        }
    }

    public function verify($map)
    {
        $data = $this->where($map)->find();
        if ($data != NULL) {
            return $data;
        } else
            return false;
    }
}