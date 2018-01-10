<?php

namespace Addons\Weizk\Model;

use Think\Model;

/**
 * Survey模型
 */
class ZkEvalReportModel extends Model {
    protected $tableName = 'zk_eval_report';
    function getReportInfo($survey_id,$follow_id,$client_id,$update = false, $data = array()) {
        $key = 'ZkEvalReport_getReprotInfo_' . $survey_id.'_'.$follow_id.'_'.$client_id;
        $info = S ( $key );

        if ($info === false || $update) {
            $map['prj_id'] = $survey_id;
            $map['client_id']=$client_id;
            $map['uid'] = $follow_id;
            $info = ( array ) (empty ( $data ) ? $this->where ( $map )->find () : $data);
            //$info =  $this->where ( $map )->find ();
            S ( $key, $info, 86400 );
        }

        return $info;
    }
    function updateReportInfo($survey_id,$follow_id,$client_id,$data=array()){
        $map ['prj_id'] = $survey_id;
        $map['client_id']=$client_id;
        $map ['uid'] = $follow_id;
        $res = $this->where ( $map )->save ( $data );
        if ($res) {
            $this->getReportInfo ( $survey_id,$follow_id,$client_id, true );
        }
        return $res;
    }
}
