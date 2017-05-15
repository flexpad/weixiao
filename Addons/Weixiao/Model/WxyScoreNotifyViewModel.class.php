<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2016/11/27
 * Time: 15:08
 */
namespace Addons\Weixiao\Model;
//use Think\Model;
use Think\Model\ViewModel;

class WxyScoreNotifyViewModel extends ViewModel {
    public $viewFields = array(

        'WxyScore' => array(
            'token'     => 'token',
            'id'        => 'id', 
            'sid'       => 'sid', 
            'courseid'  => 'courseid',
            'course_name' => 'course_name', //added the course_name in the score table
            'name'      => 'stuname',
            'subject'   => 'subject',
            'term'      => 'exam', 
            'score'     => 'score', 
            'score1'    => 'score1', 
            'score2'    => 'score2', 
            'score3'    => 'score3', 
            'exmscore'  => 'socreStr', 
            'classdate' =>  'classdate', 
            'comment'   =>  'comment',
            'studentno' => 'studentno'
            //'_on' => 'WxyStudentCard.studentno = WxyScore.studentno AND WxyStudentCard.token = WxyScore.token'
        ),

        'WxyStudentCare'=>array(
            'openid' => 'openid',
            '_on'    => 'WxyScore.token = WxyStudentCare.token AND WxyScore.studentno = WxyStudentCare.studentno'),
        /*
        'WxyStudentCard'=>array('name'=>'student_name',
            'name' => 'stuname',
            '_on'=>'WxyStudentCare.sid = WxyStudentCard.id'), // To see changed to WxyStudentCard.id

        'WxyClassCourse' => array (
            'course_name' => 'course',
            'teacher' => 'teacher',
            '_on' => 'WxyScore.courseid = WxyClassCourse.id'
        ),

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