<?php

namespace Addons\Weizk\Model;

use Think\Model;

/**
 * Survey模型
 */
class ZkEvalItemModel extends Model {
    function getQuestionInfo($survey_id, $update = false, $data = array()) {
        $key = 'ZkEvalItem_getQuestionInfo_' . $survey_id;
        $info = S ( $key );
        if ($info === false || $update) {
            $map ['prj_id'] = $survey_id;
            //$info = ( array ) (empty ( $data ) ? $this->where ( $map )->order ( 'sort asc, id asc' )->select () : $data);
            $info = ( array ) (empty ( $data ) ? $this->where ( $map )->order ( 'id' )->select () : $data);
            S ( $key, $info, 86400 );
        }

        return $info;
    }
}