<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2016/11/23
 * Time: 20:45
 */
namespace Addons\Course\Controller;
use Home\Controller\AddonsController;


class WapController extends AddonsController
{
    var $config;
    function _initialize() {
        parent::_initialize ();
        $this->assign('nav',null);
        $config = getAddonConfig ( 'WeiSite' );
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
        define ( 'CUSTOM_TEMPLATE_PATH', ONETHINK_ADDON_PATH . 'WeiSite/View/default/Template' );
    }
    
    protected $model;
    protected $token;
    protected $school;
    public function __construct() {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct ();
        $this->model = $this->getModel('WxyCourse'); //getModelByName ( $_REQUEST ['_controller'] );
        $this->token = get_token();
        $this->school = D('Common/Public')->getInfoByToken($this->token, 'public_name');

        /*var_dump($this->model);
        var_dump($_REQUEST ['_controller']);

        exit();
        $this->model || $this->error ( '模型不存在！' );
        */

        $this->assign ( 'model', $this->model );


    }
    
    public function index()
    {
        $this->_footer();
        $this->display(ONETHINK_ADDON_PATH. 'WeiSite/View/default/TemplateLists/V2/lists.html');
    }
    // 分类列表
    function lists()
    {
        $public_id = I('public_id', 0, 'intval');
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
            $row = isset ($_REQUEST ['list_row']) ? intval($_REQUEST ['list_row']) : 15;

            $data = M('WxyCourse')->where($map)->order('id DESC')->page($page, $row)->select();
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
            $count = M('WxyCourse')->where($map)->count();
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
                    $li['coverurl'] = get_square_url($li['cover'], 200);
                    //if (IS_AJAX) $li['coverurl'] = urlencode($li['coverurl']);
                    $li['fcTime'] = $li['sdate'];
                    $lists [] = $li;
                //}
            }
            //var_dump($lists);
            //$this->assign('slide_data', $slideData);
            $this->assign('lists', $lists);
            //$this->assign ( $list_data );
            $this->_footer();

            if (IS_AJAX)
                $this->ajaxReturn($lists);
            else {
                //var_dump($lists);
                $this->display(ONETHINK_ADDON_PATH . 'Course/View/default/Wap/TemplateLists/' . /*$this->config ['template_lists']*/ 'V4' . '/lists.html');
            }
        }

    }

    // 3G页面底部导航
    function _footer($temp_type = 'weiphp') {
        if ($temp_type == 'pigcms') {
            $param ['token'] = $token = get_token ();
            $param ['temp'] = $this->config ['template_footer'];
            $url = U ( 'Home/Index/getFooterHtml', $param );
            $html = wp_file_get_contents ( $url );
            // dump ( $url );
            // dump ( $html );
            $file = RUNTIME_PATH . $token . '_' . $this->config ['template_footer'] . '.html';
            if (! file_exists ( $file ) || true) {
                file_put_contents ( $file, $html );
            }

            $this->assign ( 'cateMenuFileName', $file );
        } else {
            $list = D ( 'Addons://WeiSite/Footer' )->get_list ();
            //var_dump($list);

            foreach ( $list as $k => $vo ) {
                if ($vo ['pid'] != 0)
                    continue;

                $one_arr [$vo ['id']] = $vo;
                unset ( $list [$k] );
            }

            foreach ( $one_arr as &$p ) {
                $two_arr = array ();
                foreach ( $list as $key => $l ) {
                    if ($l ['pid'] != $p ['id'])
                        continue;

                    $two_arr [] = $l;
                    unset ( $list [$key] );
                }

                $p ['child'] = $two_arr;
            }
            $this->assign ( 'footer', $one_arr );
            if (empty ( $this->config ['template_footer'] )) {
                $this->config ['template_footer'] = 'V2';
            }
            //define ('CUSTOM_TEMPLATE_PATH',  './Addons/Weisite/View/default/Template');
            $html = $this->fetch ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateFooter/' . $this->config ['template_footer'] . '/footer.html' );
            $this->assign ( 'footer_html', $html );
        }
    }

    // 详情
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
            $data = M('WxyCourse')->where($map)->find();
            //var_dump($data);
            $this->assign('info', $data);
            $this->_footer();
            $this->display(ONETHINK_ADDON_PATH . 'Course/View/default/Wap/TemplateDetail/' . /*$this->config ['template_detail']*/'V2'. '/detail.html');
        //}
    }
}