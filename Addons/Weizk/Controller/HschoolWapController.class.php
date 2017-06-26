<?php
/*
 * Created by PhpStorm.
 * User: zhuke
 * Date: 2017/6/2
 * Time: 20:13
 */


namespace Addons\Weizk\Controller;

use Addons\Weizk\Controller\BaseController;

class HschoolWapController extends BaseController
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
        $this->model = $this->getModel('ZkHschool'); //getModelByName ( $_REQUEST ['_controller'] );
        $this->token = get_token();
        $this->publicid = D('Common/Public')->getInfoByToken($this->token, 'id');
    }

    public function picker_list(){
        if (!IS_POST) $this->error("请在表单中提交！");

        $data = M('ZkHschool')->order('id')->select();
        $ret_data = array();
        foreach($data as $index=>$item){
            $ret_data[$index] = array("value"=>$item['id'],"text"=>$item['name']);
        }

        $this->ajaxReturn(json_encode($ret_data),'JSON');

    }

    function lists()
    {
        $public_id = I('publicid', 0, 'intval');
        empty ($public_id) && $public_id = I('public', 0, 'intval');
        $map ['token'] = D('Common/Public')->getinfo($public_id, 'token');
        // TBD if public_id is not found in Public table, how to detail?
        //var_dump($public_id);
        //var_dump($map);
        //exit();

        //$map ['token'] = $this->token;
        if ($public_id || IS_AJAX) {
            $map ['token'] = D('Common/Public')->getinfo($public_id, 'token');
            empty ($map['token']) && $map['token'] = $this->token;
            /*
            $cate = D('WxyCourse')->where('id = ' . $map ['cate_id'])->find();
            $this->assign('cate', $cate);
            // 二级分类

            $category = M('weisite_category')->where('pid = ' . $map ['cate_id'])->order('sort asc, id desc')->select();
        }
        if (!empty ($category)) {
            foreach ($category as &$vo) {
                $vo ['icon'] = get_cover_url($vo ['icon']);
                empty ($vo ['url']) && $vo ['url'] = addons_url('WeiSite://WeiSite/lists', array(
                    'cate_id' => $vo ['id']
                ));
            }
            $this->assign('category', $category);
            // 幻灯片

            $slideshow = M('weisite_slideshow')->where($map)->order('sort asc, id desc')->select();
            foreach ($slideshow as &$vo) {
                $vo ['img'] = get_cover_url($vo ['img']);
            }

            foreach ($slideshow as &$data) {
                foreach ($category as $c) {
                    if ($data ['cate_id'] == $c ['id']) {
                        $data ['url'] = $c ['url'];
                    }
                }
            }
            $this->assign('slideshow', $slideshow);

            $this->_footer();
            if ($this->config ['template_subcate'] == 'default') {
                // code...
                $htmlstr = 'cate.html';
            } else {
                $htmlstr = 'index.html';
            }
            if (!$cate ['template']) {
                $cate ['template'] = $this->config ['template_subcate'];
            }
            $this->display(ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateSubcate/' . $cate ['template'] . '/' . $htmlstr);
        } else {*/

            $page = I('p', 1, 'intval');
            //var_dump($page);

            if (IS_AJAX) $page = intval(I('post.page'));
            $row = isset ($_REQUEST ['list_row']) ? intval($_REQUEST ['list_row']) : 6;

            $data = M('zk_hschool')->where($map)->order('id ASC')->page($page, $row)->select();
            //var_dump($data);

            if (empty ($data)) {
                /*
                $cmap ['id'] = $map ['cate_id'] = intval($cate_id);
                $cate = M('weisite_category')->where($cmap)->find();
                if (!empty ($cate ['url'])) {
                    redirect($cate ['url']);
                    die ();
                }
                */
                //redirect();
            }
            /* 查询记录总数 */
            $count = M('zk_hschool')->where($map)->count();
            $list_data ['list_data'] = $data;

            // 分页
            if ($count > $row) {
                $page = new \Think\Page ($count, $row);
                $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
                $list_data ['_page'] = $page->show();
            }

            foreach ($list_data ['list_data'] as $k => $li) {
                if ($li ['jump_url'] && empty ($li ['content'])) {
                    $li ['url'] = $li ['jump_url'];
                } else {
                    $li ['url'] = U('detail', array(
                        'id' => $li ['id']
                    ));
                }
                //if (IS_AJAX) $li['url'] = urlencode($li['url']);
                /*
                $showType = explode(',', $li ['show_type']);
                if (in_array(1, $showType)) {
                    $slideData [] = $li;
                }
                if (in_array(0, $showType)) {
                */
                // unset($list_data['list_data'][$k]);
                unset($li['intro']);
                $li['coverurl'] = get_square_url($li['cover'], 600);
                //if (IS_AJAX) $li['coverurl'] = urlencode($li['coverurl']);
                $li['fcTime'] = $li['sdate'];
                $lists [] = $li;
                //}
            }
            //var_dump($lists);
            //$this->assign('slide_data', $slideData);
            $this->assign('lists', $lists);
            //$this->assign ( $list_data );
            //$this->_footer();
            //var_dump($lists);
            if (IS_AJAX)
                $this->ajaxReturn($lists);
            else {
                //var_dump($lists);
                $this->display("lists");
            }
        }

    }

    function detail()
    {
        /*
        if (file_exists(ONETHINK_ADDON_PATH . 'WeiSite/View/default/pigcms/Index_' . $this->config ['template_detail'] . '.html')) {
            $this->pigcms_detail();
            $this->display(ONETHINK_ADDON_PATH . 'WeiSite/View/default/pigcms/Index_' . $this->config ['template_detail'] . '.html');
        } else {
        */
        $map ['id'] = I('get.id', 0, 'intval');
        //$info = M('WxyCourse')->where($map)->find();
        // dump($info);exit;
        /*
        if ($info ['is_show'] == '0') {
            unset ($info ['cover']);
        }
        */
        // dump($info);exit;
        //$this->assign('info', $info);

        // dump($info);exit;
        $data = M('zk_hschool')->where($map)->find();
        //var_dump($data);
        $this->assign('info', $data);
        //$this->_footer();
        $this->display("detail");
        //}
    }

}