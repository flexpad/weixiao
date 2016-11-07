<?php

namespace Addons\WeiSite\Controller;

use Addons\WeiSite\Controller\BaseController;

class SlideshowController extends BaseController {
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'weisite_slideshow' );
		parent::_initialize ();
	}
	// 通用插件的列表模型
	public function lists() {
		$has_slide = 1;
		$file = ONETHINK_ADDON_PATH . _ADDONS . '/View/default/TemplateIndex/' . $this->config ['template_index'] . '/info.php';
		if (file_exists ( $file )) {
			$info = require_once $file;
			if (isset ( $info ['has_slide'] ) && $info ['has_slide'] == 0) {
				$has_slide = 0;
			}
		}else if(file_exists ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/pigcms/Index_' . $this->config ['template_index'] . '.html' )){
		    $pigcms_temps = require_once ONETHINK_ADDON_PATH . _ADDONS . '/View/default/pigcms/index.Tpl.php';
            foreach ($pigcms_temps as $pig){
                if ($pig['tpltypename']==$this->config ['template_index']){
                    $has_slide=$pig['has_slide'];
                }
            }
		}
		
		$this->assign ( 'has_slide', $has_slide );
		
		$map ['token'] = get_token ();
		session ( 'common_condition', $map );
		
		$list_data = $this->_get_model_list ( $this->model );
		foreach ( $list_data ['list_data'] as &$vo ) {
			$vo ['img'] = '<img src="' . get_cover_url ( $vo ['img'] ) . '" width="50px" >';
		}
		foreach ($list_data['list_data'] as &$data){
			$map['id']=$data['cate_id'];
			$res=D('weisite_category')->where($map)->field('title')->find();
			$data['cate_id']=$res['title'];
		}
		$this->assign ( $list_data );
		//dump ( $list_data );
		
		$this->display ();
	}
	// 通用插件的编辑模型
	public function edit() {
		$model = $this->model;
		is_array ( $model ) || $model = $this->getModel ( $model );
		$id || $id = I ( 'id' );
		
		// 获取数据
		$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
		$data || $this->error ( '数据不存在！' );
		
		$token = get_token ();
		if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
			$this->error ( '非法访问！' );
		}
		
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				$this->_saveKeyword ( $model, $id );
				
				// 清空缓存
				method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
				
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			
			$map ['token'] = get_token ();
			
			$list = M('weisite_category')->where ( $map )->select ();
			//dump($list);
			foreach ( $list as $v ) {
				
				$extra .= $v ['id'] . ':' . $v ['title'] . "\r\n";
			}
				
			$fields = get_model_attribute ( $model ['id'] );
			if (! empty ( $extra )) {
				foreach ( $fields as &$vo ) {
					if ($vo ['name'] == 'pid') {
						$vo ['extra'] .= "\r\n" . $extra;
					}
				}
			}
			//dump($extra);	
			//dump($fields);
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			
			$templateFile || $templateFile = $model ['template_edit'] ? $model ['template_edit'] : '';
			$this->display ( $templateFile );
		}
	}
	
	
	// 通用插件的增加模型
	public function add() {
		parent::common_add ( $this->model );
	}
	
	// 通用插件的删除模型
	public function del() {
		parent::common_del ( $this->model );
	}
	// 首页
	function index() {
		$this->display ();
	}
	// 分类列表
	function category() {
		$this->display ();
	}
	// 相册模式
	function picList() {
		$this->display ();
	}
	// 详情
	function detail() {
		$this->display ();
	}
}
