<?php

namespace Addons\SingIn\Controller;

use Home\Controller\AddonsController;

class SingInController extends AddonsController {
	function lists() {
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 关键字搜索
		$map ['token'] = get_token ();
		$key = $this->model ['search_key'] ? $this->model ['search_key'] : 'uid';
		if (isset ( $_REQUEST [$key] )) {
			$map [$key] = array (
					'like',
					'%' . htmlspecialchars ( $_REQUEST [$key] ) . '%' 
			);
			unset ( $_REQUEST [$key] );
		}
		
		// 条件搜索
		foreach ( $_REQUEST as $name => $val ) {
			if (in_array ( $name, $fields )) {
				$map [$name] = $val;
			}
		}
		
		// 读取模型数据列表
		$row = 20;
		$name = 'signin_log';
		
		// 查询数据
		$data = M ( $name )->where ( $map )->order ( 'id DESC' )->page ( $page, $row )->select ();
		/* 查询记录总数 */
		$count = M ( $name )->where ( $map )->count ();
		// 获取相关的用户信息
		$uids = getSubByKey ( $data, 'uid' );
		$uids = array_filter ( $uids );
		$uids = array_unique ( $uids );
		if (! empty ( $uids )) {
			foreach ( $data as &$vo ) {
				$user = get_userinfo ( $vo ['uid'] );
				$vo ['user_id'] = $user ['uid'];
				$vo ['mobile'] = $user ['mobile'];
				$vo ['nickname'] = $user ['nickname'];
			}
		}
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$this->assign ( '_page', $page->show () );
		}
		
		$this->assign ( 'list_data', $data );
		$this->meta_title = $this->model ['title'] . '列表';
		$this->display ( T ( 'Addons://SingIn@SingIn/lists' ) );
	}
	function config() {
		$this->getModel ();
		if (IS_POST) {
			
			$flag = D ( 'Common/AddonConfig' )->set ( _ADDONS, I ( 'config' ) );
			
			if ($flag !== false) {
				$this->success ( '保存成功', Cookie ( '__forward__' ) );
			} else {
				$this->error ( '保存失败' );
			}
		}
		
		$map ['name'] = _ADDONS;
		$addon = M ( 'addons' )->where ( $map )->find ();
		if (! $addon)
			$this->error ( '插件未安装' );
		$addon_class = get_addon_class ( $addon ['name'] );
		if (! class_exists ( $addon_class ))
			trace ( "插件{$addon['name']}无法实例化,", 'ADDONS', 'ERR' );
		$data = new $addon_class ();
		$addon ['addon_path'] = $data->addon_path;
		$addon ['custom_config'] = $data->custom_config;
		$this->meta_title = '设置插件-' . $data->info ['title'];
		$db_config = D ( 'Common/AddonConfig' )->get ( _ADDONS );
		$addon ['config'] = include $data->config_file;
		if ($db_config) {
			foreach ( $addon ['config'] as $key => $value ) {
				if ($value ['type'] != 'group') {
					! isset ( $db_config [$key] ) || $addon ['config'] [$key] ['value'] = $db_config [$key];
				} else {
					foreach ( $value ['options'] as $gourp => $options ) {
						foreach ( $options ['options'] as $gkey => $value ) {
							! isset ( $db_config [$key] ) || $addon ['config'] [$key] ['options'] [$gourp] ['options'] [$gkey] ['value'] = $db_config [$gkey];
						}
					}
				}
			}
		}
		foreach ( $addon ['config'] as $key => $vo ) {
			$dataArr [$key] = $vo ['value'];
		}

		$this->assign ( 'data', $dataArr );
		
		$this->display ();
	}
}