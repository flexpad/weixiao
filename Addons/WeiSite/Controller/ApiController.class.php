<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2018/1/28
 * Time: 10:08
 */

namespace Addons\WeiSite\Controller;
use Think\Controller;

class ApiController extends Controller
{
    var $model;
    function _initialize() {
        $this->model = $this->getModel ( 'custom_reply_news' );

        //parent::_initialize ();
    }

    public function __construct()
    {
        $GLOBALS ['is_wap'] = false;
        $GLOBALS['is_api'] = true;
    }

    // 通用插件的列表API模型
    public function lists_api() {
        $map ['token'] = I('get.token');
        $cate_id = I ( 'get.cate_id', 0, 'intval' );
        session ( 'common_condition', $map );

        if ($cate_id) {
            $map ['cate_id'] = $cate_id;
            $cate = M ( 'weisite_category' )->where ( 'id = ' . $map ['cate_id'] )->find ();
            $this->assign ( 'cate', $cate );
            // 二级分类
            $category = M ( 'weisite_category' )->where ( 'pid = ' . $map ['cate_id'] )->order ( 'sort asc, id desc' )->select ();
        }
        define (IS_AJAX, true);
        $url_page = I ( 'p', 1, 'intval' );

        //else var_dump($page);
        $row = isset ( $_REQUEST ['list_row'] ) ? intval ( $_REQUEST ['list_row'] ) : 15;

        $data = M ( 'custom_reply_news' )->where ( $map )->order ( 'sort asc, id DESC' )->page ( $url_page, $row )->select ();
        if (empty ( $data )) {
            /*$cmap ['id'] = $map ['cate_id'] = intval ( $cate_id );
            $cate = M ( 'weisite_category' )->where ( $cmap )->find ();
            if (! empty ( $cate ['url'] )) {
                redirect ( $cate ['url'] );
                die ();
            }*/
            $count = 0;
            $lists = null;
        } else {
            /* 查询记录总数 */
            $count = M ( 'custom_reply_news' )->where ( $map )->count ();
            $list_data ['list_data'] = $data;

            // 分页
            if ($count > $row) {
                $page = new \Think\Page ( $count, $row );
                $page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
                $list_data ['_page'] = $page->show ();
            }

            foreach ( $list_data ['list_data'] as $k => $li ) {
                if ($li ['jump_url'] && empty ( $li ['content'] )) {
                    $li ['url'] = $li ['jump_url'];
                } else {
                    $li ['url'] = U ( 'detail', array (
                        'id' => $li ['id']
                    ) );
                }
                //if (IS_AJAX) $li['url'] = urlencode($li['url']);
                $showType = explode ( ',', $li ['show_type'] );
                if (in_array ( 1, $showType )) {
                    $slideData [] = $li;
                }
                if (in_array ( 0, $showType )) {
                    // unset($list_data['list_data'][$k]);
                    unset($li['content']);
                    $li['coverurl'] = str_replace('http:','https:', get_square_url($li['cover']));
                    //if (IS_AJAX) $li['coverurl'] = urlencode($li['coverurl']);
                    $li['fcTime'] = time_format($li['cTime']);
                    $lists [] = $li;
                }
            }
        }

        /*$this->assign ( 'slide_data', $slideData );
        $this->assign ( 'lists', $lists );*/
        //$this->assign ( $list_data );
        /*$this->_footer ();*/
        unset($data);
        $data["total"] = $count;
        $data["ipp"] = $row;
        $data["page"] = $url_page;
        $data["objects"] = $lists;
        if ($data["total"] > 0) {
            $json_data["status_code"] = 0;
            $json_data["msg"] = "SUCCESS";
            $json_data["data"] = $data;
        } else {
            $json_data["status_code"] = 1;
            $json_data["msg"] = "FAIL";
            $json_data["data"] = null;
        }
        $this->ajaxReturn($json_data);
    }

    // 详情
    function detail() {
        $map ['token'] = I('get.token');
        $map ['id'] = I ( 'get.id', 0, 'intval' );
        $info = M( 'custom_reply_news' )->where ( $map )->find ();

        if ($info == null) {
            $data['msg'] = 'FAIL';
            $data['data'] = null;
        } else {
            if ($info ['is_show'] == '0') {
                unset ( $info ['cover'] );
            }
            M( 'custom_reply_news' )->where ( $map )->setInc ( 'view_count' );
            $url='http://'.$_SERVER['SERVER_NAME'];

            $pattern = '/src=\"\//i';
            $replacement = 'src="'.$url.'/';
            $info['content'] = (preg_replace($pattern, $replacement, $info['content']));
            unset($info['token']);
            $data['msg'] = 'SUCCESS';
            $data['data'] = $info;
        }

        $this->ajaxReturn($data);
    }
}