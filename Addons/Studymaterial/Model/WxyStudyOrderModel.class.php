<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2016/11/16
 * Time: 15:05
 */
namespace Addons\Studymaterial\Model;
use Think\Model;

/**
 * Studymaterial模型
 */
class WxyStudyOrderModel extends Model{
    public function order_list($stage =NULL){

        if ($stage != NULL) {
            $map['stage'] = $stage;
            $map['email'] = array('exp','is not null');
        }
        else
            $map['email'] = array('exp','is not null');
        return $this->where($map)->select();        
    }

}
