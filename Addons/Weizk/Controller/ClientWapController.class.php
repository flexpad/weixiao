<?php
/*
 * Created by PhpStorm.
 * User: zhuke
 * Date: 2017/6/2
 * Time: 20:13
 */


namespace Addons\Weizk\Controller;

use Addons\Weizk\Controller\BaseController;

class ClientWapController extends BaseController{
    var $config;
    var $token;
    var $publicid;
    function _initialize() {
        parent::_initialize ();
    }
    public function __construct() {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct ();
        $this->model = $this->getModel('ZkClient'); //getModelByName ( $_REQUEST ['_controller'] );
        $this->token = get_token();
        $this->publicid = D('Common/Public')->getInfoByToken($this->token, 'id');
    }

    public function index() {
        $this->display("index");
    }

    public function test() {
        $this->display("test");
    }

    public function user_center()
    {
        $this->assign("page_title","微中考：用户中心");
        $public_id = intval(I('publicid'));
        $this->assign('public_id', $public_id);

        $map['openid'] = get_openid();
        $map['uid'] = $this->uid;
        $map['token'] = $this->token;

        $data = M('ZkClient')->where($map)->order('id')->select();
        $ret_data = array();
        foreach($data as $index=>$item){
            $ret_data[$index] = array("value"=>$item['id'],"text"=>$item['name'],"state"=>"valid");
        }
        for($index = count($ret_data); $index<3;++$index)
        {
            $ret_data[$index] = array("value"=>0,"text"=>"绑定新学生","state"=>"none");
        }
        $this->assign("bind_students",$ret_data);
        //var_dump($ret_data);
        $this->display("user_center");
    }

    public function updateInfor() {
        $public_id = intval(I('publicid'));
        $public_id = ($public_id > 0) ? $public_id:1;

        $client_id = intval(I('clientid'));
        $client_id = ($client_id > 0) ? $client_id:0;
        $openid = get_openid();
        $client_model = D('ZkClient');
        $this->assign("page_title","微中考：用户信息录入");
        if (IS_POST) {
            $follow_data = array();
            $name = trim(I('post.name'));
            $phone = trim(I('post.mobile'));
            $school_name = trim(I('post.mschool'));
            $school_id = intval(I('post.mschool_id'));
            $class_type = trim(I('post.classtype'));
            $grand_year = trim(I('post.grand_year'));

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

            if ($data == NULL && $userdata['subscribe'] == 1) {
                $follow_data['uid'] = 0;
                $follow_data['openid'] = $map['openid'];
                $follow_data['token'] = $this->token;
                $follow_data['has_subscribe'] = 1;
                $follow_data['syc_status'] = 0;
                $follow_data['remark'] = 'added by client updateInfor';
                $follow_model->add($follow_data); //Add a record for new follower.
                //Need add user data here also TBD.
            }
            else if ($data == NULL && $userdata['subscribe'] == 0)
                $this->error("请关注我们的微信公号后再更新用户信息！！！", U('updateInfor', 'publicid=' . $public_id));
            else if ($data != NULL) {
                $data['has_subscribe'] = $userdata['subscribe'];
                if ($userdata['subscribe'] == 0) {
                    $follow_model->where($map)->save($data);
                    $this->error("请关注我们的微信公号后更新用户信息！！！", U('updateInfor', 'publicid=' .$public_id));
                }
                else {
                    $follow_model->where($map)->save($data);

                    $client['name'] = $name;
                    $client['phone'] = $phone;
                    $client['token'] = $this->token;
                    $client['c_school'] = $school_id;
                    $client['class_type'] = $class_type;
                    $client['grand_year'] = $grand_year;

                    $res = $client_model->approve($client, $user, $this->token);
                    if($res == true)
                    {
                        $this->success("用户信息更新成功！", U('user_center', 'publicid='.$this->publicid));
                    }
                }
            }
            $this->error("用户信息更新有误，请返回重新输入！");
        }
        else {
            $map['id'] = $public_id;
            $data = M('public')->where($map)->find();
            if ($this->token == NULL)
                $this->token = $data['token'];

            if($client_id != 0 ){
                $map2['id'] = $client_id;
                $clientData = $client_model->where($map2)->find();
                $this->assign("client_data",$clientData);
            }

            $this->assign('user_id', $user['uid']);
            $this->assign('public_id', $public_id);
            $this->display('updateInfor');
        }
    }

    public function get_bind_students()
    {
        if (!IS_POST) $this->error("请在表单中提交！");

        $map['openid'] = get_openid();
        $map['uid'] = $this->uid;
        $map['token'] = $this->token;

        $data = M('ZkClient')->where($map)->order('id')->select();
        $ret_data = array();
        foreach($data as $index=>$item){
            $ret_data[$index] = array("value"=>$item['id'],"text"=>$item['name']);
        }
        $this->ajaxReturn(json_encode($ret_data),'JSON');
    }

    public function show_student(){
        $this->assign("page_title","微中考：学生信息");
        $public_id = intval(I('publicid'));
        $map['id'] = I('clientid');
        $data = M('ZkClient')->where($map)->select();
        if($data == NULL) $this->error("用户ID有误，请返回重新选择！");
        $school_map['id'] = $data[0]['c_school'];
        $mschool = M('ZkMschool')->where($school_map)->select();

        $std_info = $data[0];
        $std_info['c_school_name'] = $mschool[0]['name'];
        $this->assign('public_id', $public_id);
        $this->assign('std_info',$std_info);
        $this->assign("client_id",$map['id']);
        $this->display('student');
    }

    public function set_candiSchool(){
        $this->assign("page_title","微中考：设定学生目标学校");

        if (IS_POST) {
            $public_id = intval(I('post.publicId'));
            $client_id = I('post.clientId');
            $map['id'] = $client_id;


            $client_data = M('ZkClient')->where($map)->find();
            if($client_data == NULL)  $this->error("请选择正确的学校！");

            for($index=0;$index<5;$index++){
                $client_data['candi_school'.$index] = intval(I(('post.candi_school'.$index.'Id')));
            }
            //var_dump($client_data);
            $res = M('ZkClient')->save($client_data);

            if($res == true)
            {
                $this->success("用户信息更新成功！", U('show_student', 'publicid=' . $public_id.'&clientid='.$client_id));
            }
            else{
                $this->error("用户信息更新失败/无改变！");
            }

        }
        else{
            $public_id = intval(I('publicid'));
            $client_id = intval(I('clientid'));
            $map['id'] = $client_id;
            $data = M('ZkClient')->where($map)->find();
            $titles = array('最心仪的学校','候选学校一','候选学校二','候选学校三','候选学校四');
            $schools = array();

            for($index=0; $index < 5; $index++){
                $school_map['id'] = $data['candi_school'.$index];
                $hschool = M('ZkHschool')->where($school_map)->find();
                $schools[$index] = array('title'=>$titles[$index],'name'=>'candi_school'.$index,'text'=>$hschool['name'],'school_id'=>$hschool['id']);
            }

            $this->assign("client_id",$client_id);
            $this->assign('public_id', $public_id);
            $this->assign('candi_schools',$schools);
            $this->display('set_candiSchool');
        }

    }

}