<?php
/*
 * Created by PhpStorm.
 * User: zhuke
 * Date: 2017/6/2
 * Time: 20:13
 */


namespace Addons\Weizk\Controller;

use Addons\Weizk\Controller\BaseController;

class EvalReportWapController extends BaseController
{
    var $config;
    var $token;
    var $publicid;
    var $model;

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
        $this->model = $this->getModel('ZkEvalReport');
        $this->token = get_token();
        $this->publicid = D('Common/Public')->getInfoByToken($this->token, 'id');
    }

    private function get_lists($page,$client_id=0){
        $map['token'] = $this->token;
        $map['openid'] = get_openid();
        if($client_id != 0)
        {
            $map['client_id'] = $client_id;
        }
        $row = 5;

        $data = M('ZkEvalReport')->where($map)->order('id')->page($page, $row)->select();
        //$data = M('ZkEvalReport')->where($map)->order('id')->select();

        if($data == NULL)
            return NULL;

        $ret_list = array();
        foreach($data as $key => $val) {
            $prj_map['id'] = $val['prj_id'];
            $prj_data = M('ZkEvalPrj')->where($prj_map)->find();
            $ret_list[$key]['title'] = ($prj_data)?$prj_data['title']:'此测评问题已删除';
            $ret_list[$key]['c_time'] = date('Y-m-d H:i:s', $val['timestamp ']);
            $client_data = D('ZkClient')->get_clinet_info($val['client_id']);
            $ret_list[$key]['client_name'] = $client_data['name'];
            $ret_list[$key]['id'] = $val['id'];
            $ret_list[$key]['url'] = U('detail', array('id'=>$val ['id']));
        }

        return $ret_list;
    }
    public function lists()
    {
        $client_id = I('clientid', 0, 'intval'); // 默认显示第一页数据
        $page = I('page', 1, 'intval');
        if (IS_AJAX) $page = intval(I('post.page'));

        $data = $this->get_lists($page, $client_id);

        $this->assign('report_list',$data);
        $this->assign('public_id', $this->publicid);

        if (IS_AJAX)
            $this->ajaxReturn($data);
        else
            $this->display('lists');
    }

    public function ajx_lists(){
        $page = I('page', 1, 'intval'); // 默认显示第一页数据
        $data = get_lists($page);
        $this->ajaxReturn(json_encode($data),'JSON');
    }

    public function detail()
    {
        $map['id'] = I('id', 0, 'intval'); // 默认显示第一页数据
        $data = M('ZkEvalReport')->where($map)->find();
        $ret_detail['text'] = $data['report'];
        $prj_map['id'] = $data['prj_id'];
        $prj_data = M('ZkEvalPrj')->where($prj_map)->find();
        $ret_detail['title'] = $prj_data['title'];
        $this->assign('detail',$ret_detail);
        $this->assign('public_id', $this->publicid);
        $this->display();
    }
}