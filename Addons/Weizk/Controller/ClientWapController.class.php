<?php
/*
 * Created by PhpStorm.
 * User: zhuke
 * Date: 2017/6/2
 * Time: 20:13
 */


namespace Addons\Weizk\Controller;

use Home\Controller\AddonsController;

class ClientWapController extends AddonsController{
    var $config;
    var $token;
    var $publicid;
    function _initialize() {
        parent::_initialize ();
        $this->assign('nav',null);
        $config = getAddonConfig ( 'Weizk' );
        $config ['cover_url'] = get_cover_url ( $config ['cover'] );
        $config['background_arr']=explode(',', $config['background']);
        $config ['background_id'] = $config ['background_arr'][0];
        $config ['background'] = get_cover_url ( $config ['background_id'] );
        $this->config = $config;
        $this->assign ( 'config', $config );
        //dump ( $config );
        // dump(get_token());

        // 定义模板常量
        $act = strtolower ( _ACTION );
        $temp = $config ['template_' . $act];
        $act = ucfirst ( $act );
        $this->assign ( 'page_title', $config ['title'] );
        define ( 'CUSTOM_TEMPLATE_PATH', ONETHINK_ADDON_PATH . 'Weizk/View/default/Template' );
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

    public function updateInfor() {
        $openid = get_openid();
        $client_model = D('ZkClient');
        $this->assign("page_title","微中考：用户信息录入");
        if (IS_POST) {
            $follow_data = array();
            $name = trim(I('post.name'));
            $phone = trim(I('post.mobile'));
            $school_name = trim(I('post.school'));
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
                $this->error("请关注我们的微信公号后再更新用户信息！！！", U('updateInfor', 'publicid=' . $this->publicid));
            else if ($data != NULL) {
                $data['has_subscribe'] = $userdata['subscribe'];
                if ($userdata['subscribe'] == 0) {
                    $follow_model->where($map)->save($data);
                    $this->error("请关注我们的微信公号后更新用户信息！！！", U('updateInfor', 'publicid=' . $this->publicid));
                }
                else {
                    $follow_model->where($map)->save($data);
                    $client['name'] = $name;
                    $client['phone'] = $phone;
                    $client['token'] = $this->token;
                    $client['c_school'] = $school_name;
                    $client['class_type'] = $class_type;
                    $client['grand_year'] = $grand_year;

                    $res = $client_model->approve($client, $user, $this->token);
                    if($res == true)
                    {
                        $this->success("用户信息更新成功！", U('updateInfor', 'publicid=' . $this->publici));
                    }
                }
            }
            $this->error("用户信息更新有误，请返回重新输入！");
        }
        else {
            $public_id = intval(I('publicid'));
            $public_id = ($public_id > 0) ? $public_id:1;

            $map['id'] = $public_id;
            $data = M('public')->where($map)->find();
            if ($this->token == NULL)
                $this->token = $data['token'];

            $map2['openid'] = $openid;
            $this->assign('care_count', $client_model->where($map2)->count());
            $this->assign('oid', $data['public_name']);
            $this->assign('public_id', $public_id);

            //$this->_footer();
            $this->display('updateInfor');
        }
    }
}