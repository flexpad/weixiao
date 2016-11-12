<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2016/11/9
 * Time: 10:13
 */


namespace Addons\Student\Controller;

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
                //Need adduser here also TBD.
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
            $map['id'] = $public_id;
            $data = M('public')->where($map)->find();
            $this->token = $data['token'];
            //var_dump($public_id);
            //var_dump($this->token);
            $map2['openid'] = $openid;
            $this->assign('care_count', $studentcare_model->where($map2)->count());
            $this->assign('oid', $data['public_name']);
            $this->display('bind_student');
        }
    }

    public function index() {
        $this->display('bind_student');
        /*
        $this->model = $this->getModel ( 'forms_value' );
        $this->forms_id = I ( 'forms_id', 0 );
        $id = I ( 'id' );

        $forms = M ( 'forms' )->find ( $this->forms_id );
        $forms ['cover'] = ! empty ( $forms ['cover'] ) ? get_cover_url ( $forms ['cover'] ) : ADDON_PUBLIC_PATH . '/background.png';
        $forms ['intro'] = str_replace ( chr ( 10 ), '<br/>', $forms ['intro'] );
        $this->assign ( 'forms', $forms );

        if (! empty ( $id )) {
            $act = 'save';

            $data = M ( get_table_name ( $this->model ['id'] ) )->find ( $id );
            $data || $this->error ( '数据不存在！' );

            // dump($data);
            $value = unserialize ( htmlspecialchars_decode ( $data ['value'] ) );
            // dump ( $value );
            unset ( $data ['value'] );
            $data = array_merge ( $data, $value );
            $this->assign ( 'data', $data );
            // dump($data);
        } else {
            $act = 'add';
            if ($this->mid != 0 && $this->mid != '-1') {
                $map ['uid'] = $this->mid;
                $map ['forms_id'] = $this->forms_id;

                $data = M ( get_table_name ( $this->model ['id'] ) )->where ( $map )->find ();
                if ($data ) {
                    $id = $data['id'];
                    redirect (U('index',array('forms_id'=>$this->forms_id,'id'=>$id)));
                }
            }
        }

        // dump ( $forms );
        $map ['forms_id'] = $this->forms_id;
        $map ['token'] = get_token ();
        $fields = M ( 'forms_attribute' )->where ( $map )->order ( 'sort asc, id asc' )->select ();

        if (IS_POST) {
            foreach ( $fields as $vo ) {
                $error_tip = ! empty ( $vo ['error_info'] ) ? $vo ['error_info'] : '请正确输入' . $vo ['title'] . '的值';
                $value = $_POST [$vo ['name']];
                if ($vo['type'] == 'radio' || $vo['type'] == 'checkbox'){
                    if (($vo ['is_must'] &&  is_null ( $value )) || (! empty ( $vo ['validate_rule'] ) && ! M ()->regex ( $value, $vo ['validate_rule'] ))) {
                        $this->error ( $error_tip );
                        exit ();
                    }
                }else {
                    if (($vo ['is_must'] &&  empty ( $value )) || (! empty ( $vo ['validate_rule'] ) && ! M ()->regex ( $value, $vo ['validate_rule'] ))) {
                        $this->error ( $error_tip );
                        exit ();
                    }
                }


                $post [$vo ['name']] = $vo ['type'] == 'datetime' ? strtotime ( $_POST [$vo ['name']] ) : $_POST [$vo ['name']];
                unset ( $_POST [$vo ['name']] );
            }

            $_POST ['value'] = serialize ( $post );
            $act == 'add' && $_POST ['uid'] = $this->mid;
            // dump($_POST);exit;
            $Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );

            // 获取模型的字段信息
            $Model = $this->checkAttr ( $Model, $this->model ['id'], $fields );

            if ($Model->create () && $res = $Model->$act ()) {
                // 增加积分
                add_credit ( 'forms' );

                $param ['forms_id'] = $this->forms_id;
                $param ['id'] = $act == 'add' ? $res : $id;
                $param ['model'] = $this->model ['id'];
                $url = empty ( $forms ['jump_url'] ) ? U ( 'index', $param ) : $forms ['jump_url'];

                $tip = ! empty ( $forms ['finish_tip'] ) ? $forms ['finish_tip'] : '提交成功，谢谢参与';
                $this->success ( $tip, $url, 5 );
            } else {
                $this->error ( $Model->getError () );
            }
            exit ();
        }

        $fields [] = array (
            'is_show' => 4,
            'name' => 'forms_id',
            'value' => $this->forms_id
        );
        $this->assign ( 'fields', $fields );

        $this->display ();
        */
    }

}
