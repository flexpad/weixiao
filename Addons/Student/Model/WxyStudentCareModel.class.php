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
        $studenttimecard_model=D('WxyStudentTimeCard');
        $studentno = $student['studentno'];
        $student = $studentcard_model->verify($student);
        if ($student == NULL)
        {
            $map_t['serial_no'] = $studentno;
            $data = $studenttimecard_model->where($map_t)->find();
            if ($data != NULL)
            {
                $map_s['studentno'] = $data['studentno'];
                $student = $studentcard_model->where($map_s)->find();
                unset($data);
                if ($student == NULL) return false;
            }
            else
                return false;
        }

        $data['sid'] = $student['id']; // To see changed to $student['id']
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
    /*
    public function getInfor($uid, $openid) {
        $studentcard_model = D('WxyStudentCard');
        if ($uid) {
            $map['uid'] = $uid;
            $data = $this->where($map)->select();
        }
        else {
            $map['openid'] = $openid;
            $data = $this->where($map)->select();
        }

        foreach ($data as $index => $vo) {

        }
        return $data;

    }
    */
    public function checksmscode($phonenum,$code,$openid,$update){
        $key = 'Wxy_checksmscode' .$openid;
        if ($update === true){
            $info = array('phonnum'=>$phonenum,'verifycode'=>$code);
            S ( $key, $info, 86400 );
        }
        $info = S ( $key );
        if ($info === false){
            return NULL;
        }
        else{
            return $info;
        }
    }
}
