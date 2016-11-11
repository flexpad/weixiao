<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2016/11/9
 * Time: 10:13
 */


namespace Addons\Student\Controller;

use Addons\Student\Model\WxyStudentCareViewModel;
use Home\Controller\AddonsController;

class WapController extends AddonsController {
    var $model;
    var $token;
    //var $openid;
    //var $uid;
    public function __construct() {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct ();
        $this->model = $this->getModel('WxyStudentCare'); //getModelByName ( $_REQUEST ['_controller'] );
        $this->token = get_token();
        /*var_dump($this->model);
        var_dump($_REQUEST ['_controller']);

        exit();
        $this->model || $this->error ( '模型不存在！' );

        $this->assign ( 'model', $this->model );
        */

    }
    public function bind() {
        $openid = get_openid();
        $studentcare_model = D('WxyStudentCare');
        if (IS_POST) {
            $follow_data = array();
            $studentcare = array();
            $studentcard_model = D('WxyStudentCard');
            $studentno = trim(I('studentno'));
            $name = trim(I('post.name'));
            $phone = trim(I('post.mobile'));

            $map['openid'] = $user['openid'] = $openid;
            $user['uid'] = $this->uid;

            $follow_model = M('public_follow');
            $data = $follow_model->where($map)->find();

            if ($this->token == NULL || $user['openid'] == NULL)
                $this->error("请在微信中打开！");

            $access_token = get_access_token ($this->token);
            $suburl = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token . '&openid=' . $user ['openid'] . '&lang=zh_CN';
            $userdata = file_get_contents ( $suburl );
            $userdata = json_decode ( $userdata, true );
            //var_dump($userdata);
            //exit();
            if ($data == NULL && $userdata['subscribe'] == 1) {
                $follow_data['uid'] = 0;
                $follow_data['openid'] = $map['openid'];
                $follow_data['token'] = $this->token;
                $follow_data['has_subscribe'] = 1;
                $follow_data['syc_status'] = 0;
                $follow_data['remark'] = 'added by student bind';
                $follow_model->add($follow_data); //Add a record for new follower.
                //Need add user data here also TBD.
            }
            else if ($data == NULL && $userdata['subscribe'] == 0)
                $this->error("请关注我们的微信公号后再绑定学生！！！", U('bind'));
            else if ($data != NULL) {
                $data['has_subscribe'] = $userdata['subscribe'];
                if ($userdata['subscribe'] == 0) {
                    $follow_model->where($map)->save($data);
                    $this->error("请关注我们的微信公号后再绑定学生！！！", U('bind'));
                }
                else {
                    $follow_model->where($map)->save($data);
                    $student['studentno'] = $studentno;
                    $student['name'] = $name;
                    $student['phone'] = $phone;
                    $student['token'] = $this->token;
                    //$student = $studentcard_model->verify($student);
                    $res = $studentcare_model->approve($student, $user, $this->token);
                    if ($res == 2) {
                        $this->success("已绑定学号为：". $student['studentno']. "的学生！", U('bind'));
                    }
                    else
                        if ($res == 1)
                            $this->error("已绑定过：". $student['studentno']. "号学生！", U('bind'));
                        else
                            $this->error("学生信息有误，请返回重新输入！", U('bind'));

                }
            }
            $this->error("学生信息有误，请返回重新输入！");
        }
        else {
            $public_id = intval(I('publicid'));
            $public_id = ($public_id > 0) ? $public_id:1;

            $map['id'] = $public_id;
            $data = M('public')->where($map)->find();
            if ($this->token == NULL)
                $this->token = $data['token'];

            //var_dump($public_id);
            //var_dump($this->token);
            $map2['openid'] = $openid;
            $this->assign('care_count', $studentcare_model->where($map2)->count());
            $this->assign('oid', $data['public_name']);
            $this->display('bind_student');
        }
    }

    public function index()
    {
        $this->display('bind_student');
    }

    public function infor() {
        $public_id = intval(I('publicid'));
        $public_id = ($public_id > 0) ? $public_id:1;

        $map['id'] = $public_id;
        $data = M('public')->where($map)->find();

        if ($this->token == NULL)
            $this->token = $data['token'];

        //var_dump($public_id);
        //var_dump($data);

        unset($map);
        unset($data);
        $studentcare_view = D('WxyStudentCareView');
        $map['token'] = $this->token;
        //var_dump($studentcare_view);
        if ($this->uid) {
            $map['uid'] = $this->uid;
        }
        else {
            $map['openid'] = get_openid();
        }
        $map['is_audit'] = 1;
        $data = $studentcare_view->where($map)->select();
        //var_dump($data);

        $this->assign('list', $data);
        $this->display('Infor');
    }

}
