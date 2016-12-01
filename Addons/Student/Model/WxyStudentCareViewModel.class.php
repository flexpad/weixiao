<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/11
 * Time: 21:20
 */

namespace Addons\Student\Model;
//use Think\Model;
use Think\Model\ViewModel;

class WxyStudentCareViewModel extends ViewModel
{
    public $viewFields = array(
        'WxyStudentCare'=>array('token', 'sid', 'studentno', 'uid', 'openid', 'is_audit'),
        'WxyStudentCard'=>array('name'=>'name',
                                'gender' => 'gender',
                                'school' => 'school',
                                'grade'  => 'grade',
                                'phone'  => 'phone',
                                '_on'=>'WxyStudentCare.sid = WxyStudentCard.id'), // To see changed to WxyStudentCard.id
    );
}