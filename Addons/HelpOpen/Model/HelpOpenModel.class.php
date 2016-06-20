<?php

namespace Addons\HelpOpen\Model;

use Think\Model;

/**
 * HelpOpen模型
 */
class HelpOpenModel extends Model {
	function getInfo($id) {
		$info = $this->find ( $id );
		return $info;
	}
	function update($id, $save) {
		$map ['id'] = $id;
		$res = $this->where ( $map )->save ( $save );
		return $res;
	}
}
