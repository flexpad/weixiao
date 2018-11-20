<?php

namespace Addons\WeiSite\Controller;

use Addons\WeiSite\Controller\BaseController;

class WeiSiteController extends BaseController {
	function config() {
		$public_info = get_token_appinfo ();
		$normal_tips = '在微信里回复“微官网”即可以查看效果,也可以点击：<a href="' . addons_url ( 'WeiSite://WeiSite/index', array (
				'publicid' => $public_info ['id']
		) ) . '">预览</a>ﺿ<a id="copyLink" data-clipboard-text="' . addons_url ( 'WeiSite://WeiSite/index', array (
				'publicid' => $public_info ['id']
		) ) . '">复制链接</a><script type="application/javascript">$.WeiPHP.initCopyBtn("copyLink");</script>';
		$this->assign ( 'normal_tips', $normal_tips );

		$config = D ( 'Common/AddonConfig' )->get ( _ADDONS );
		// dump(_ADDONS);
		if (IS_POST) {
			$_POST ['config'] ['background'] = implode ( ',', $_POST ['background'] );
			// $config = array_merge ( ( array ) $config, ( array ) $_POST ['config'] );
			$flag = D ( 'Common/AddonConfig' )->set ( _ADDONS, $_POST ['config'] );
			if ($flag !== false) {
				if ($_GET ['from'] == 'preview') {
					$url = U ( 'preview' );
				} else {
					$url = Cookie ( '__forward__' );
				}
				$this->success ( '保存成功', $url );
			} else {
				$this->error ( '保存失败' );
			}
			exit ();
		}
		$config ['background_arr'] = explode ( ',', $config ['background'] );
		$config ['background'] = $config ['background_arr'] [0];
		$this->assign ( 'data', $config );
		$this->display ();
	}
	// 首页
	function index() {
		// add_credit ( 'weisite', 86400 );
		if (file_exists ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/pigcms/Index_' . $this->config ['template_index'] . '.html' )) {
			$this->pigcms_index ();
			$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/pigcms/Index_' . $this->config ['template_index'] . '.html' );
		} else {
			$map1 ['token'] = $map ['token'] = get_token ();
			$map1 ['is_show'] = $map ['is_show'] = 1;
			$map ['pid'] = 0; // 获取一级分篿

			// 分类
			$category = M ( 'weisite_category' )->where ( $map )->order ( 'sort asc, id desc' )->select ();
			foreach ( $category as &$vo ) {
				$vo ['icon'] = get_cover_url ( $vo ['icon'] );
				empty ( $vo ['url'] ) && $vo ['url'] = addons_url ( 'WeiSite://WeiSite/lists', array (
						'cate_id' => $vo ['id']
				) );
			}
			$this->assign ( 'category', $category );
			// dump($category);
			// 幻灯燿
			$slideshow = M ( 'weisite_slideshow' )->where ( $map1 )->order ( 'sort asc, id desc' )->select ();
			foreach ( $slideshow as &$vo ) {
				$vo ['img'] = get_cover_url ( $vo ['img'] );
			}

			foreach ( $slideshow as &$data ) {
				foreach ( $category as $cate ) {
					if ($data ['cate_id'] == $cate ['id'] && empty ( $data ['url'] )) {
						$data ['url'] = $cate ['url'];
					}
				}
			}
			$this->assign ( 'slideshow', $slideshow );
			// dump($slideshow);

			// dump($category);
			$map2 ['token'] = $map ['token'];
			$public_info = get_token_appinfo ( $map2 ['token'] );
			$this->assign ( 'publicid', $public_info ['id'] );

			$this->assign ( 'manager_id', $this->mid );

			$this->_footer ();
			// $backgroundimg=ONETHINK_ADDON_PATH.'WeiSite/View/default/TemplateIndex/'.$this->config['template_index'].'/icon.png';
			if ($this->config ['show_background'] == 0) {
				$this->config ['background'] = '';
				$this->assign ( 'config', $this->config );
			}
			$html = empty ( $this->config ['template_index'] ) ? 'ColorV1' : $this->config ['template_index'];
			$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/' . $html . '/index.html' );
		}
	}
	// 分类列表
	function lists() {
		$cate_id = I ( 'get.cate_id', 0, 'intval' );
		empty ( $cate_id ) && $cate_id = I ( 'classid', 0, 'intval' );
        //var_dump($cate_id);
		if (file_exists ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/pigcms/Index_' . $this->config ['template_lists'] . '.html' )) {

			$this->pigcms_lists ( $cate_id );
			$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/pigcms/Index_' . $this->config ['template_lists'] . '.html' );
		} else {
			$map ['token'] = get_token ();
			if ($cate_id) {
				$map ['cate_id'] = $cate_id;
				$cate = M ( 'weisite_category' )->where ( 'id = ' . $map ['cate_id'] )->find ();
				$this->assign ( 'cate', $cate );
				// 二级分类
				$category = M ( 'weisite_category' )->where ( 'pid = ' . $map ['cate_id'] )->order ( 'sort asc, id desc' )->select ();
			}
			if (! empty ( $category )) {
				foreach ( $category as &$vo ) {
					$vo ['icon'] = get_cover_url ( $vo ['icon'] );
					empty ( $vo ['url'] ) && $vo ['url'] = addons_url ( 'WeiSite://WeiSite/lists', array (
							'cate_id' => $vo ['id']
					) );
				}
				$this->assign ( 'category', $category );
				// 幻灯燿

				$slideshow = M ( 'weisite_slideshow' )->where ( $map )->order ( 'sort asc, id desc' )->select ();
				foreach ( $slideshow as &$vo ) {
					$vo ['img'] = get_cover_url ( $vo ['img'] );
				}

				foreach ( $slideshow as &$data ) {
					foreach ( $category as $c ) {
						if ($data ['cate_id'] == $c ['id']) {
							$data ['url'] = $c ['url'];
						}
					}
				}
				$this->assign ( 'slideshow', $slideshow );

				$this->_footer ();
				if ($this->config ['template_subcate'] == 'default') {
					// code...
					$htmlstr = 'cate.html';
				} else {
					$htmlstr = 'index.html';
				}
				if (! $cate ['template']) {
					$cate ['template'] = $this->config ['template_subcate'];
				}
				$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateSubcate/' . $cate ['template'] . '/' . $htmlstr );
			} else {

				$page = I ( 'p', 1, 'intval' );
                if (IS_AJAX) $page = intval(I('post.page'));
				//else var_dump($page);
				$row = isset ( $_REQUEST ['list_row'] ) ? intval ( $_REQUEST ['list_row'] ) : 15;

				$data = M ( 'custom_reply_news' )->where ( $map )->order ( 'sort asc, id DESC' )->page ( $page, $row )->select ();
				if (empty ( $data )) {
					$cmap ['id'] = $map ['cate_id'] = intval ( $cate_id );
					$cate = M ( 'weisite_category' )->where ( $cmap )->find ();
					if (! empty ( $cate ['url'] )) {
						redirect ( $cate ['url'] );
						die ();
					}
				}
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
                        $li['coverurl'] = get_square_url($li['cover'] ,200);
						//if (IS_AJAX) $li['coverurl'] = urlencode($li['coverurl']);
                        $li['fcTime'] = time_format($li['cTime']);
                        $lists [] = $li;
					}
				}
				$this->assign ( 'slide_data', $slideData );
				$this->assign ( 'lists', $lists );
				//$this->assign ( $list_data );
				$this->_footer ();

                if (IS_AJAX)
                    $this->ajaxReturn($lists);
                else {
                    //var_dump($lists);
                    $this->display(ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateLists/' . $this->config ['template_lists'] . '/lists.html');
                }
			}
		}
	}
    private function trimall($str)  //删除空格
    {
        $oldchar=array(" ","　","\t","\n","\r","　");
        $newchar=array("","","","","","");
        return str_replace($oldchar,$newchar,$str);
    }
	// 详情
    function detail() {
		if (file_exists ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/pigcms/Index_' . $this->config ['template_detail'] . '.html' )) {
			$this->pigcms_detail ();
			$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/pigcms/Index_' . $this->config ['template_detail'] . '.html' );
		} else {
			$map ['id'] = I ( 'get.id', 0, 'intval' );
			$info = M ( 'custom_reply_news' )->where ( $map )->find ();
			// dump($info);exit;
			if ($info ['is_show'] == '0') {
				unset ( $info ['cover'] );
			}
			// dump($info);exit;
			$this->assign ( 'info', $info );
			$this->assign ('page_title',$info['title']);
			$content = strip_tags($info['content']);
            $pattern = '/\s/';
            $content = preg_replace($pattern, '', $content);
            $content = $this->trimall($content);
            $content = mb_substr($content, 0, 80);

			$this->assign ('brief_intro',$content.'...');
			$this->assign ('cover',$info['cover']);
			//var_dump($this->config ['template_detail']);
			//dump($info['title']);
			M ( 'custom_reply_news' )->where ( $map )->setInc ( 'view_count' );

			$this->_footer ();
			$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateDetail/' . $this->config ['template_detail'] . '/detail.html' );
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
				$this->config ['template_footer'] = 'V1';
			}
			$html = $this->fetch ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateFooter/' . $this->config ['template_footer'] . '/footer.html' );
			$this->assign ( 'footer_html', $html );
		}
	}
	function _deal_footer_data($vo, $k) {
		$arr = array (
				'id' => $vo ['id'],
				'fid' => $vo ['pid'],
				'token' => $vo ['token'],
				'name' => $vo ['title'],
				'orderss' => 0,
				'picurl' => get_cover_url ( $vo ['icon'] ),
				'url' => $vo ['url'],
				'status' => "1",
				'RadioGroup1' => "0",
				'vo' => array (),
				'k' => $k
		);
		return $arr;
	}
	function coming_soom() {
		$this->display ();
	}
	function tvs1_video() {
		$this->display ();
	}

	/*
	 * 兼容小猪CMS模板
	 *
	 * 移植方法ﺿ
	 * 1、把 tpl\static\tpl 目录下的所有文档复制到 weiphp瘿Addons\WeiSite\View\default\pigcms 目录䶿
	 * 2、把 tpl\Wap\default 目录下的所有文档也复制冿weiphp瘿Addons\WeiSite\View\default\pigcms 目录䶿
	 * 3、把 tpl\User\default\common\ 目录下的所有图片文件复制到 weiphp瘿Addons\WeiSite\View\default\pigcms 目录䶿
	 * 4、把 PigCms\Lib\ORG\index.Tpl.php 文件复制冿weiphp瘿Addons\WeiSite\View\default\pigcms 目录䶿
	 * 5、把pigcms 目录下所有文档代码里瘿Wap/Index/lists 替换憿Home/Addons/execute?_addons=WeiSite&_controller=WeiSite&_action=lists
	 * 6、把pigcms 目录下所有文档代码里瘿Wap/Index/index 替换憿Home/Addons/execute?_addons=WeiSite&_controller=WeiSite&_action=index
	 */
	function pigcms_init() {
		// dump ( 'pigcms_init' );
		C ( 'TMPL_L_DELIM', '{pigcms:' );
		// C ( 'TMPL_FILE_DEPR', '_' );

		define ( 'RES', ONETHINK_ADDON_PATH . 'WeiSite/View/default/pigcms/common' );

		$public_info = get_token_appinfo ();
		$manager = get_userinfo ( $public_info ['uid'] );

		// 站点配置
		$data ['f_logo'] = get_cover_url ( C ( 'SYSTEM_LOGO' ) );
		$data ['f_siteName'] = C ( 'WEB_SITE_TITLE' );
		$data ['f_siteTitle'] = C ( 'WEB_SITE_TITLE' );
		$data ['f_metaKeyword'] = C ( 'WEB_SITE_KEYWORD' );
		$data ['f_metaDes'] = C ( 'WEB_SITE_DESCRIPTION' );
		$data ['f_siteUrl'] = SITE_URL;
		$data ['f_qq'] = '';
		$data ['f_qrcode'] = '';
		$data ['f_ipc'] = C ( 'WEB_SITE_ICP' );
		$data ['reg_validDays'] = 30;

		// 用户信息
		$data ['user'] = array (
				'id' => $GLOBALS ['myinfo'] ['uid'],
				'openid' => get_openid (),
				'username' => $GLOBALS ['myinfo'] ['nickname'],
				'mp' => $public_info ['token'],
				'password' => $GLOBALS ['myinfo'] ['password'],
				'email' => $GLOBALS ['myinfo'] ['email'],
				'createtime' => $GLOBALS ['myinfo'] ['reg_time'],
				'lasttime' => $GLOBALS ['myinfo'] ['last_login_time'],
				'status' => 1,
				'createip' => $GLOBALS ['myinfo'] ['reg_ip'],
				'lastip' => $GLOBALS ['myinfo'] ['last_login_ip'],
				'smscount' => 0,
				'inviter' => 1,
				'gid' => 5,
				'diynum' => 0,
				'activitynum' => 0,
				'card_num' => 0,
				'card_create_status' => 0,
				'money' => 0,
				'moneybalance' => 0,
				'spend' => 0,
				'viptime' => $GLOBALS ['myinfo'] ['last_login_time'] + 86400,
				'connectnum' => 0,
				'lastloginmonth' => 0,
				'attachmentsize' => 0,
				'wechat_card_num' => 0,
				'serviceUserNum' => 0,
				'invitecode' => '',
				'remark' => ''
		);

		// 微网站配置信忿
		$data ['homeInfo'] = array (
				'id' => $manager ['uid'],
				'token' => $public_info ['token'],
				'title' => $this->config ['title'],
				'picurl' => get_cover_url ( $this->config ['cover'] ),
				// 'apiurl' => "",
				// 'homeurl' => "",
				'info' => $this->config ['info'],
				// 'musicurl' => "",
				// 'plugmenucolor' => "#5CFF8D",
				'copyright' => $manager ['copy_right'],
				// 'radiogroup' => "12",
				// 'advancetpl' => "0"
				'logo' => get_cover_url ( $this->config ['cover'] )
		);

		// 背景噿
		$bgarr = $this->config ['background_arr'];
		$data ['flashbgcount'] = count ( $bgarr );
		foreach ( $bgarr as $bg ) {
			$data ['flashbg'] [] = array (
					'id' => $bg,
					'token' => $public_info ['token'],
					'img' => get_cover_url ( $bg ),
					'url' => "javascript:void(0)",
					'info' => "背景图片",
					'tip' => '2'
			);
		}
		// $data ['flashbg'] [0] = array (
		// 'id' => $this->config ['background_id'],
		// 'token' => $public_info ['token'],
		// 'img' => $this->config ['background'],
		// 'url' => "javascript:void(0)",
		// 'info' => "背景图片",
		// 'tip' => '2'
		// );
		$data ['flashbgcount'] = count ( $data ['flashbg'] );
		$map ['token'] = get_token ();
		$map ['is_show'] = 1;
		// 幻灯燿
		$slideshow = M ( 'weisite_slideshow' )->where ( $map )->order ( 'sort asc, id desc' )->select ();
		foreach ( $slideshow as $vo ) {
			$data ['flash'] [] = array (
					'id' => $vo ['id'],
					'token' => $vo ['token'],
					'img' => get_cover_url ( $vo ['img'] ),
					'url' => $vo ['url'],
					'info' => $vo ['title'],
					'tip' => '1'
			);
		}
		$data ['num'] = count ( $data ['flash'] );

		// 底部枿
		$this->_footer ( 'pigcms' );

		// 设置版权信息
		$data ["iscopyright"] = 0;
		$data ["copyright"] = $data ["siteCopyright"] = empty ( $manager ['copy_right'] ) ? C ( 'COPYRIGHT' ) : $manager ['copy_right'];
		// 分享
		$data ['shareScript'] = '';

		$data ['token'] = $public_info ['token'];
		$data ['wecha_id'] = $public_info ['wechat'];

		$this->assign ( $data );

		// 模板信息
		if (file_exists ( ONETHINK_ADDON_PATH . _ADDONS . '/View/default/pigcms/index.Tpl.php' )) {
			$pigcms_temps = require_once ONETHINK_ADDON_PATH . _ADDONS . '/View/default/pigcms/index.Tpl.php';
			foreach ( $pigcms_temps as $k => $vo ) {
				$temps [$vo ['tpltypename']] = $vo;
			}
		}

		if (file_exists ( ONETHINK_ADDON_PATH . _ADDONS . '/View/default/pigcms/cont.Tpl.php' )) {
			$pigcms_temps = require_once ONETHINK_ADDON_PATH . _ADDONS . '/View/default/pigcms/cont.Tpl.php';
			foreach ( $pigcms_temps as $k => $vo ) {
				$temps [$vo ['tpltypename']] = $vo;
			}
		}
		$tpl = array (
				'id' => $public_info ['id'],
				'routerid' => "",
				'uid' => $public_info ['uid'],
				'wxname' => $public_info ['public_name'],
				'winxintype' => $public_info ['type'],
				'appid' => $public_info ['appid'],
				'appsecret' => $public_info ['secret'],
				'wxid' => $public_info ['id'],
				'weixin' => $public_info ['wechat'],
				'headerpic' => get_cover_url ( $GLOBALS ['myinfo'] ['headface_url'] ),
				'token' => $public_info ['token'],
				'pigsecret' => $public_info ['token'],
				'province' => $GLOBALS ['myinfo'] ['province'],
				'city' => $GLOBALS ['myinfo'] ['city'],
				'qq' => $GLOBALS ['myinfo'] ['qq'],
				// 'wxfans' => "0",
				// 'typeid' => "8",
				// 'typename' => "服务",
				// 'tongji' => "",
				// 'allcardnum' => "0",
				// 'cardisok' => "0",
				// 'yetcardnum' => "0",
				// 'totalcardnum' => "0",
				// 'createtime' => "1440150418",
				// 'updatetime' => "1440150418",
				// 'transfer_customer_service' => "0",
				// 'openphotoprint' => "0",
				// 'freephotocount' => "3",
				// 'oauth' => "0",
				'color_id' => 0,

				'tpltypeid' => $temps [$this->config ['template_index']] ['tpltypeid'],
				'tpltypename' => $this->config ['template_index'],

				'tpllistid' => $temps [$this->config ['template_lists']] ['tpltypeid'],
				'tpllistname' => $this->config ['template_lists'],

				'tplcontentid' => $temps [$this->config ['template_detail']] ['tpltypeid'],
				'tplcontentname' => $this->config ['template_detail']
		);
		$this->assign ( 'tpl', $tpl );
		$this->assign ( 'wxuser', $tpl );
	}
	function pigcms_index() {
		$this->pigcms_init ();

		$cate = $this->_pigcms_cate ( 0 );
		$this->assign ( 'info', $cate );
	}
	function pigcms_lists($cate_id) {
		$this->pigcms_init ();

		$map ['token'] = get_token ();
		$cateArr = M ( 'weisite_category' )->where ( $map )->getField ( 'id,title' );

		$thisClassInfo = array ();
		if ($cate_id) {
			$map ['cate_id'] = $cate_id;

			$thisClassInfo = $this->_deal_cate ( $cateArr [$cate_id] );
		}

		$data = M ( 'custom_reply_news' )->where ( $map )->order ( 'sort asc, id DESC' )->select ();
		foreach ( $data as $vo ) {
			$info [] = array (
					'id' => $vo ['id'],
					'uid' => 0,
					'uname' => $vo ['author'],
					'keyword' => $vo ['keyword'],
					'type' => 2,
					'text' => $vo ['intro'],
					'classid' => $vo ['cate_id'],
					'classname' => $vo [''],
					'pic' => get_cover_url ( $vo ['cover'] ),
					'showpic' => 1,
					'info' => strip_tags ( htmlspecialchars_decode ( mb_substr ( $vo ['content'], 0, 10, 'utf-8' ) ) ),
					'url' => $this->_getNewsUrl ( $vo ),
					'createtime' => $vo ['cTime'],
					'uptatetime' => $vo ['cTime'],
					'click' => $vo ['view_count'],
					'token' => $vo ['token'],
					'title' => $vo ['title'],
					'usort' => $vo ['sort'],
					'name' => $vo ['title'],
					'img' => get_cover_url ( $vo ['cover'] )
			);
		}

		$this->assign ( 'info', $info );
		$this->assign ( 'thisClassInfo', $thisClassInfo );
	}
	function pigcms_detail() {
		$this->pigcms_init ();

		$cate = $this->_pigcms_cate ( 0 );
		$this->assign ( 'info', $cate );

		$map ['id'] = I ( 'get.id', 0, 'intval' );
		$res = M ( 'custom_reply_news' )->where ( $map )->find ();
		if ($res ['is_show'] == 0) {
			unset ( $res ['cover'] );
		}
		$res = $this->_deal_news ( $res, 1 );
		$this->assign ( 'res', $res );
		var_dump($res);
		M ( 'custom_reply_news' )->where ( $map )->setInc ( 'view_count' );

		$map2 ['cate_id'] = $res ['cate_id'];
		$map2 ['id'] = array (
				'exp',
				'!=' . $map ['id']
		);
		$lists = M ( 'custom_reply_news' )->where ( $map2 )->order ( 'id desc' )->limit ( 5 )->select ();
		foreach ( $lists as &$new ) {
			$new = $this->_deal_news ( $new );
		}

		$this->assign ( 'lists', $lists );
	}
	function _pigcms_cate($pid = null) {
		$map ['token'] = get_token ();
		$map ['is_show'] = 1;
		$pid === null || $map ['pid'] = $pid; // 获取一级分篿

		$category = M ( 'weisite_category' )->where ( $map )->order ( 'sort asc, id desc' )->select ();
		$count = count ( $category );
		foreach ( $category as $k => $vo ) {
			$param ['cate_id'] = $vo ['id'];
			$url = empty ( $vo ['url'] ) ? $vo ['url'] = addons_url ( 'WeiSite://WeiSite/lists', $param ) : $vo ['url'];
			$pid = intval ( $vo ['pid'] );
			$res [$pid] [$vo ['id']] = $this->_deal_cate ( $vo, $count - $k );
		}

		foreach ( $res [0] as $vv ) {
			if (! empty ( $res [$vv ['id']] )) {
				$vv ['sub'] = $res [$vv ['id']];
				unset ( $res [$vv ['id']] );
			}
		}

		return $res [0];
	}
	function _deal_cate($vo, $key = 1) {
		return array (
				'id' => $vo ['id'],
				'fid' => $vo ['pid'],
				'name' => $vo ['title'],
				'info' => $vo ['title'],
				'sorts' => $vo ['sort'],
				'img' => get_cover_url ( $vo ['icon'] ),
				'url' => $url,
				'status' => 1,
				'path' => empty ( $vo ['pid'] ) ? 0 : '0-' . $vo ['pid'],
				'tpid' => 1,
				'conttpid' => 1,
				'sub' => array (),
				'key' => $key,
				'token' => $vo ['token']
		);
	}
	function _deal_news($vo, $type = 0) {
		$map ['id'] = $vo ['cate_id'];
		return array (
				'id' => $vo ['id'],
				'uid' => 0,
				'uname' => $vo ['author'],
				'keyword' => $vo ['keyword'],
				'type' => 2,
				'text' => $vo ['intro'],
				'classid' => $vo ['cate_id'],
				'classname' => empty ( $vo ['cate_id'] ) ? '' : M ( 'weisite_category' )->where ( $map )->getField ( 'title' ),
				'pic' => get_cover_url ( $vo ['cover'] ),
				'showpic' => 1,
				'info' => $type == 0 ? strip_tags ( htmlspecialchars_decode ( mb_substr ( $vo ['content'], 0, 10, 'utf-8' ) ) ) : $vo ['content'],
				'url' => $this->_getNewsUrl ( $vo ),
				'createtime' => $vo ['cTime'],
				'uptatetime' => $vo ['cTime'],
				'click' => $vo ['view_count'],
				'token' => $vo ['token'],
				'title' => $vo ['title'],
				'usort' => $vo ['sort'],
				'name' => $vo ['title'],
				'img' => get_cover_url ( $vo ['cover'] )
		);
	}
	function _getNewsUrl($info) {
		$param ['token'] = get_token ();
		$param ['openid'] = get_openid ();

		if (! empty ( $info ['jump_url'] )) {
			$url = replace_url ( $info ['jump_url'] );
		} else {
			$param ['id'] = $info ['id'];
			$url = U ( 'detail', $param );
		}
		return $url;
	}
	/* 预览 */
	function preview() {
		$publicid = get_token_appinfo ( '', 'id' );
		$url = addons_url ( 'WeiSite://WeiSite/index', array (
				'publicid' => $publicid 
		) );
		$this->assign ( 'url', $url );
		
		$config = get_addon_config ( 'WeiSite' );
		
		$config ['background_arr'] = explode ( ',', $config ['background'] );
		$config ['background'] = $config ['background_arr'] [0];
		$this->assign ( 'data', $config );
		
		$this->display ();
	}
	function preview_cms() {
		$publicid = get_token_appinfo ( '', 'id' );
		$url = addons_url ( 'WeiSite://WeiSite/lists', array (
				'publicid' => $publicid,
				'from' => 'preview' 
		) );
		$this->assign ( 'url', $url );
		
		$this->display ();
	}
	function preview_old() {
		$publicid = get_token_appinfo ( '', 'id' );
		$url = addons_url ( 'WeiSite://WeiSite/index', array (
				'publicid' => $publicid 
		) );
		$this->assign ( 'url', $url );
		$this->display ( SITE_PATH . '/Application/Home/View/default/Addons/preview.html' );
	}
}
