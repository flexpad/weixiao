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

class WxyStudentPerformViewModel extends ViewModel {
    public $viewFields = array(
        'WxyStudentCare'=>array('token', 'sid', 'studentno', 'uid', 'openid', 'is_audit'),
        'WxyStudentCard'=>array('name'=>'student_name',
            'gender' => 'gender',
            'school' => 'school',
            'grade'  => 'grade',
            'class_id' => 'class_id',
            'phone'  => 'phone',
            '_on'=>'WxyStudentCare.sid = WxyStudentCard.id',
            /*'_type' => 'RIGHT'*/
        ), // To see changed to WxyStudentCard.id

        'WxyScore' => array(
            'subject' => 'subject',
            'course_name' => 'course_name',
            'courseid' => 'courseid',
            'termid' => 'termid',
            'term' => 'term',
            'score' => 'score',
            'score1' => 'score1',
            'score2' => 'score2',
            'score3' => 'score3',
            'exmscore' => 'exmscore',
            'classdate' => 'classdate',
            'comment' => 'comment',
            '_on' => 'WxyStudentCard.studentno = WxyScore.studentno AND WxyStudentCard.token = WxyScore.token',
            /*'_type' => 'RIGHT'*/
        ),

        'WxyClassCourse' => array (
            'teacher' => 'teacher',
            '_on' => 'WxyScore.subject = WxyClassCourse.course_name AND WxyStudentCard.token = WxyClassCourse.token AND WxyStudentCard.grade = WxyClassCourse.grade AND WxyStudentCard.class_id = WxyClassCourse.class_id',
            '_type' => 'RIGHT'
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