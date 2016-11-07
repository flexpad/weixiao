<?php

namespace Addons\WeiSite\Controller;

use Addons\WeiSite\Controller\BaseController;

class TemplateController extends BaseController {
	function _initialize() {
		parent::_initialize ();
	}
	
	// 首页模板
	function index() {
		$this->_getTemplateByDir ();
		
		$this->assign ( 'next_url', addons_url ( 'WeiSite://Slideshow/lists' ) );
		$this->display ();
	}
	// 二级分类
	function subcate() {
		// 使用提示
		$this->_getTemplateByDir ( 'TemplateSubcate' );
		$this->display ( 'index' );
	}
	function list_subcate() {
	    $isAjax = I ( 'isAjax' );
	    $isRadio = I ( 'isRadio' );
	    // 使用提示
	    $this->_getTemplateByDir ( 'TemplateSubcate' );
	    $this->assign('isRadio',$isRadio);
	    $this->display ( 'ajax_index' );
	    
	}
	// 分类列表模板
	function lists() {
		$this->_getTemplateByDir ( 'TemplateLists' );
		
		$this->assign ( 'next_url', addons_url ( 'WeiSite://Template/detail' ) );
		
		$this->display ();
	}
	// 详情模板
	function detail() {
		$this->_getTemplateByDir ( 'TemplateDetail' );
		
		$this->assign ( 'next_url', addons_url ( 'WeiSite://Cms/lists' ) );
		
		$this->display ();
	}
	// 底部菜单模板
	function footer() {
		$this->_getTemplateByDir ( 'TemplateFooter' );
		
		$this->assign ( 'next_url', addons_url ( 'WeiSite://Footer/lists' ) );
		
		$this->display ();
	}
	
	// 保存切换的模板
	function save() {
		$act = I ( 'post.type' );
		$config ['template_' . $act] = I ( 'post.template' );
		D ( 'Common/AddonConfig' )->set ( _ADDONS, $config );
		echo 1;
	}
	
	// 获取目录下的所有模板
	function _getTemplateByDir($type = 'TemplateIndex') {
		$action = strtolower ( _ACTION );
		$default = $this->config ['template_' . $action];
		// dump($default);
		$dir = ONETHINK_ADDON_PATH . _ADDONS . '/View/default/' . $type;
		$url = SITE_URL . '/Addons/' . _ADDONS . '/View/default/' . $type;
		
		$dirObj = opendir ( $dir );
		while ( $file = readdir ( $dirObj ) ) {
			if ($file === '.' || $file == '..' || $file == '.svn' || is_file ( $dir . '/' . $file ))
				continue;
			
			$res ['dirName'] = $res ['title'] = $file;
			
			// 获取配置文件
			if (file_exists ( $dir . '/' . $file . '/info.php' )) {
				$info = require_once $dir . '/' . $file . '/info.php';
				$res = array_merge ( $res, $info );
			}
			
			// 获取效果图
			if (file_exists ( $dir . '/' . $file . '/info.php' )) {
				$res ['icon'] = __ROOT__ . '/Addons/WeiSite/View/default/' . $type . '/' . $file . '/icon.png';
			} else {
				$res ['icon'] = ADDON_PUBLIC_PATH . '/default.png';
			}
			
			// 默认选中
			if ($default == $file) {
				$res ['class'] = 'selected';
				$res ['checked'] = 'checked="checked"';
			}
			
			$tempList [] = $res;
			unset ( $res );
		}
		
		closedir ( $dir );
		// 兼容pigcms
		if ($type != 'TemplateFooter' && $type != 'TemplateLists' && $type != 'TemplateSubcate' && file_exists ( ONETHINK_ADDON_PATH . _ADDONS . '/View/default/pigcms/index.Tpl.php' )) {
			if ($type == 'TemplateDetail') {
// 				$pigcms_temps = require_once ONETHINK_ADDON_PATH . _ADDONS . '/View/default/pigcms/cont.Tpl.php';
			} else {
				$pigcms_temps = require_once ONETHINK_ADDON_PATH . _ADDONS . '/View/default/pigcms/index.Tpl.php';
			}
			foreach ( $pigcms_temps as $p ) {
				$res ['dirName'] = $p ['tpltypename'];
				$res ['title'] = '模板' . $p ['tpltypeid'];
				
				$res ['desc'] = $p ['tpldesinfo'];
				
				// 获取效果图
				$res ['icon'] = __ROOT__ . '/Addons/WeiSite/View/default/pigcms/images/' . $p ['tplview'];
				
				// 默认选中
				if ($default == $p ['tpltypename']) {
					$res ['class'] = 'selected';
					$res ['checked'] = 'checked="checked"';
				}
				
				$tempList [] = $res;
				unset ( $res );
			}
		}
		// dump ( $pigcms_temps );
		// exit ();
		
		// dump ( $tempList );
		
		$this->assign ( 'tempList', $tempList );
	}
}
