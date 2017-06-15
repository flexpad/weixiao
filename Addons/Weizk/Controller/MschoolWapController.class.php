<?php
/*
 * Created by PhpStorm.
 * User: zhuke
 * Date: 2017/6/2
 * Time: 20:13
 */


namespace Addons\Weizk\Controller;

use Addons\Weizk\Controller\BaseController;

class MschoolWapController extends BaseController
{
    var $config;
    var $token;
    var $publicid;

    function _initialize()
    {
        parent::_initialize();
    }

    public function __construct()
    {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct();
        $this->model = $this->getModel('ZkMschool'); //getModelByName ( $_REQUEST ['_controller'] );
        $this->token = get_token();
        $this->publicid = D('Common/Public')->getInfoByToken($this->token, 'id');
    }

    public function picker_list(){
        if (!IS_POST) $this->error("请在表单中提交！");

        $data = M('ZkMschool')->order('id')->select();
        $ret_data = array();
        foreach($data as $index=>$item){
            $ret_data[$index] = array("value"=>$item['id'],"text"=>$item['name']);
        }

        $this->ajaxReturn(json_encode($ret_data),'JSON');

    }
}