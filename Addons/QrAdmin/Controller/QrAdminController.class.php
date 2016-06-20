<?php

namespace Addons\QrAdmin\Controller;

use Home\Controller\AddonsController;

class QrAdminController extends AddonsController {
	public function lists() {
		// 获取用户组信息
		$map ['token'] = get_token ();
		$groupArr = M ( 'auth_group' )->where ( $map )->getFields ( 'id,title' );
		
		// 获取用户标签信息
		$tagArr = M ( 'user_tag' )->where ( $map )->getFields ( 'id,title' );
		
		$model = $this->getModel ( 'qr_admin' );
		
		$list_data = $this->_get_model_list ( $model );
		foreach ( $list_data ['list_data'] as &$vo ) {
			empty ( $vo ['qr_code'] ) || $vo ['qr_code'] = '<img class="list_img" src="' . $vo ['qr_code'] . '">';
			$vo ['action_name'] = $vo ['action_name'] == 'QR_SCENE' ? '临时二维码' : '永久二维码';
			$vo ['group_id'] = $groupArr [$vo ['group_id']];
			
			$tagTitle = array ();
			$tag_ids = explode ( ',', $vo ['tag_ids'] );
			foreach ( $tag_ids as $id ) {
				$tagTitle [] = $tagArr [$id];
			}
			
			$vo ['tag_ids'] = implode ( ',', $tagTitle );
		}
		$this->assign ( $list_data );
		// dump ( $list_data );
		
		$this->display ();
	}
	function add() {
		$model = $this->getModel ( 'qr_admin' );
		if (IS_POST) {
			
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				$save ['qr_code'] = D ( 'Home/QrCode' )->add_qr_code ( I ( 'action_name' ), 'QrAdmin', $id );
				$map ['id'] = $id;
				if ($save ['qr_code']) {
					M ( 'qr_admin' )->where ( $map )->save ( $save );
				} else {
					M ( 'qr_admin' )->where ( $map )->delete ();
					
					$msg = '获取二维码失败';
					if ($save ['qr_code'] == - 1) {
						$msg = '二维码数量已经达到上限，增加失败';
					} elseif ($save ['qr_code'] == - 3) {
						$msg = '保存二维码失败';
					}
					$this->error ( $msg );
					exit ();
				}
				
				$this->success ( '添加二维码成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$this->assign ( 'fields', $fields );
			
			$this->display ();
		}
	}
	function edit() {
		$model = $this->getModel ( 'qr_admin' );
		$id = I ( 'id' );
		// 获取数据
		$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
		$data || $this->error ( '数据不存在！' );
		if (IS_POST) {
			
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			
			if ($Model->create () && $Model->save ()) {
				// 清空缓存
				method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
				
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->display ();
		}
	}
}
