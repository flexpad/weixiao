<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2016/11/10
 * Time: 14:47
 */
/**
 * Student_Care模型
 */
namespace Addons\Student\Model;
use Think\Model;

class WxyStudentCareModel extends Model{
    public function approve($student, $user, $token){
        $studentcard_model = D('WxyStudentCard');
        $student = $studentcard_model->verify($student);
        if ($student == NULL) return false;

        $data['sid'] = $student['sid'];
        $data['studentno'] = $student['studentno'];
        $data['uid'] = $user['uid'];
        $data['openid'] = $user['openid'];
        $data['token'] = $token;
        
        $map ['token'] = $data['token'];
        $map ['studentno'] = $data['studentno'];
        $map ['uid'] = $data['uid'];
        $map ['openid'] = $data['openid'];
        $vl = $this->where($map)->select();
        if ($vl != NULL) {
            foreach ($vl as $vo) {
                $vo['is_audit'] = 1;
            }
            $this->where($map)->save($data);
            return 1;
        } else {
            $data['is_audit'] = 1;
            $data['is_init'] = 1;
            $this->add ($data);
            return 2;
        }

    }
}
