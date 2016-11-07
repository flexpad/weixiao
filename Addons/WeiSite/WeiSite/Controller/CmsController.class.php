<?php

namespace Addons\WeiSite\Controller;

use Addons\WeiSite\Controller\BaseController;

class CmsController extends BaseController {
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'custom_reply_news' );
		parent::_initialize ();
	}
	// 通用插件的列表模型
	public function lists() {
		$map ['token'] = get_token ();
		session ( 'common_condition', $map );
		
		$list_data = $this->_get_model_list ( $this->model );
		
		// 分类数据
		$map ['is_show'] = 1;
		$list = M ( 'weisite_category' )->where ( $map )->field ( 'id,title' )->select ();
		$cate [0] = '';
		foreach ( $list as $vo ) {
			$cate [$vo ['id']] = $vo ['title'];
		}
		
		foreach ( $list_data ['list_data'] as &$vo ) {
			$vo ['cate_id'] = intval ( $vo ['cate_id'] );
			$vo ['cate_id'] = $cate [$vo ['cate_id']];
		}
		$this->assign ( $list_data );
		// dump ( $list_data );
		
		$this->display ();
	}
	// 通用插件的编辑模型
	public function edit() {
		$model = $this->model;
		$id = I ( 'id' );
		
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			
			//dump($Model);exit;
			if ($Model->create () && $Model->save ()) {
				D ( 'Common/Keyword' )->set ( $_POST ['keyword'], _ADDONS, $id, $_POST ['keyword_type'], 'custom_reply_news' );
				
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			
			$extra = $this->getCateData ();
			if (! empty ( $extra )) {
				foreach ( $fields as &$vo ) {
					if ($vo ['name'] == 'cate_id') {
						$vo ['extra'] .= "\r\n" . $extra;
					}
				}
			}
			
			// 获取数据
			$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
			$data || $this->error ( '数据不存在！' );
			
			$token = get_token ();
			if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
				$this->error ( '非法访问！' );
			}
			$has_slide = $this->has_slideshow();
			if ($has_slide){
			    $fields['show_type']['extra'].=chr(10).'1:幻灯片';
			}
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '编辑' . $model ['title'];
			
			$this->display ();
		}
	}
	
	// 通用插件的增加模型
	public function add() {
		$model = $this->model;
		$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
		
		if (IS_POST) {
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				D ( 'Common/Keyword' )->set ( $_POST ['keyword'], _ADDONS, $id, $_POST ['keyword_type'], 'custom_reply_news' );
				
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			
			$extra = $this->getCateData ();
			if (! empty ( $extra )) {
				foreach ( $fields as &$vo ) {
					if ($vo ['name'] == 'cate_id') {
						$vo ['extra'] .= "\r\n" . $extra;
					}
				}
			}
			$has_slide = $this->has_slideshow();
            if ($has_slide) {
               $fields['show_type']['extra'].=chr(10).'1:幻灯片';
            }
			$this->assign ( 'fields', $fields );
			$this->meta_title = '新增' . $model ['title'];
			
			$this->display ();
		}
	}
	
	// 通用插件的删除模型
	public function del() {
		parent::common_del ( $this->model );
	}
	
	// 获取所属分类
	function getCateData() {
		$map ['is_show'] = 1;
		$map ['token'] = get_token ();
		$list = M ( 'weisite_category' )->where ( $map )->select ();
		$list=$this->get_data($list);
		foreach ( $list as $v ) {
			$extra .= $v ['id'] . ':' . $v ['title'] . "\r\n";
		}
		return $extra;
	}
	
	function has_slideshow(){
	    $has_slide = 1;
	    $config=get_addon_config('WeiSite');
	    $file = ONETHINK_ADDON_PATH . _ADDONS . '/View/default/TemplateLists/' . $config ['template_lists'] . '/info.php';
	    if (file_exists ( $file )) {
	        $info = require_once $file;
	        if (isset ( $info ['has_slide'] ) && $info ['has_slide'] == 0) {
	            $has_slide = 0;
	        }
	    }
	    return $has_slide;
	}
	function get_data($list) {
	
		// 取一级菜单
		foreach ( $list as $k => $vo ) {
			// dump($vo);
			if ($vo ['pid'] != 0)
				continue;
				
			$one_arr [$vo ['id']] = $vo;
			unset ( $list [$k] );
		}
		foreach ( $one_arr as $p ) {
			$data [] = $p;
				
			$two_arr = array ();
			foreach ( $list as $key => $l ) {
				if ($l ['pid'] != $p ['id'])
					continue;
	
				//$l ['title'] = '├──' . $l ['title'];
				$two_arr [] = $l;
				unset ( $list [$key] );
			}
				
			$data = array_merge ( $data, $two_arr );
		}
		// dump($data);exit;
		return $data;
	}
}