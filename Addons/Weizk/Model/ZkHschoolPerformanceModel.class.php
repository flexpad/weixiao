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
class ZkHschoolPerformanceModel extends Model
{
    public function addPerformance($data)
    {
        $map['hschool_name'] = $data['hschool_name'];
        $map['year'] = $data['year'];
        $map['score_seg_low'] = $data['score_seg_low'];
        $map['score_seg_high'] = $data['score_seg_high'];

        $items = $this->where($map)->select();
        if ($items != NULL) {
            //var_dump($items);
            if(count($items) == 1) {
                $items[0]['ratio_1'] = $data['ratio_1'];
                $items[0]['ratio_2'] = $data['ratio_2'];
                $this->save($items[0]);
            }
            else{
                echo("return false");
                return false;
            }
        } else {
            var_dump($data);
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