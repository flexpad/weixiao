<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2016/11/27
 * Time: 15:08
 */
namespace Addons\Student\Model;
//use Think\Model;
use Think\Model\ViewModel;

class WxyStudentPerformViewModel extends ViewModel {
    public $viewFields = array(
        'WxyStudentCare'=>array('token', 'sid', 'studentno', 'uid', 'openid', 'is_audit'),
        'WxyStudentCard'=>array('name'=>'student_name',
            'gender' => 'gender',
            'school' => 'school',
            'grade'  => 'grade',
            'phone'  => 'phone',
            '_on'=>'WxyStudentCare.sid = WxyStudentCard.id'), // To see changed to WxyStudentCard.id

        'WxyScore' => array(
            'courseid' => 'courseid',
            'score' => 'score',
            'score1' => 'score1',
            'score2' => 'score2',
            'score3' => 'score3',
            'exmscore' => 'exmscore',
            'classdate' => 'classdate',
            'comment' => 'comment',
            '_on' => 'WxyStudentCard.studentno = WxyScore.studentno AND WxyStudentCard.token = WxyScore.token'
        ),

        'WxyCourse' => array (
            'name' => 'course_name',
            'teacher' => 'teacher',
            '_on' => 'WxyScore.courseid = WxyCourse.id'
        ),
        /*
        'WxyDailyTime' => array(
            'arriveTime' => 'arriveTime',
            'leaveTime' => 'leaveTime',
            'state' => 'state',
            'description' => 'description',
            '_on' => 'WxyStudentCard.studentno = WxyDailyTime.studentID'
        )
        */
        
    );
}