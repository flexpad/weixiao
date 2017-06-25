<?php

namespace Addons\Weizk\Controller;

use Home\Controller\AddonsController;

class EvalPrjController extends AddonsController {
    function survey_question() {
        $param ['survey_id'] = I ( 'id', 0, 'intval' );
        $url = addons_url ( 'Weizk://EvalItem/lists', $param );
        // dump($url);
        redirect ( $url );
    }
    function survey_answer() {
        $param ['survey_id'] = I ( 'id', 0, 'intval' );
        $url = addons_url ( 'Weizk://EvalItemAnswer/lists', $param );
        redirect ( $url );
    }
    function preview() {
        $id = I ( 'id', 0, 'intval' );
        $url = U ( 'index', array (
            'id' => $id
        ) );
        $this->assign ( 'url', $url );
        $this->display ( SITE_PATH . '/Application/Home/View/default/Addons/preview.html' );
    }
    function survey() {
        $id = I ( 'get.id', 0, 'intval' );
        $num = I ( 'num', 1, 'intval' );
        $token = get_token ();
        $survey = D ( 'ZkEvalPrj' )->getSurveyInfo ( $id );
        $list = D ( 'ZkEvalItem' )->getQuestionInfo ( $id );
        if (IS_POST) {
            $follow_id = $this->mid;
            $question_id = I ( 'post.question_id', 0, 'intval' );
            $answer = D ( 'ZkEvalItemAnswer' )->getAnswerInfo ( $id, $question_id, $follow_id );

            $data ['cTime'] = time ();
            $data ['answer'] = serialize ( $_POST ['answer'] );
            if ($answer) {
                D ( 'ZkEvalItemAnswer' )->updateAnswer ( $id, $question_id, $follow_id, $data );
            } else {
                $data ['prj_id'] = $id;
                $data ['token'] = $token;
                $data ['item_id'] = $question_id;
                $data ['uid'] = $follow_id;
                $data ['openid'] = get_openid ();
                M ( 'ZkEvalItemAnswer' )->add ( $data );
                D ( 'ZkEvalItemAnswer' )->getAnswerInfo ( $id, $question_id, $follow_id, true );
            }
            $num = $num + 1;
        }
        $question_id = I ( 'post.next_id', 0, 'intval' );
        if ($question_id == '-1') {
            redirect ( U ( 'finish', 'survey_id=' . $id ) );
        }
        if (empty ( $question_id )) {
            $question = $list [0];
            $next_id = isset ( $list [1] ['id'] ) ? $list [1] ['id'] : '-1';
        } else {
            foreach ( $list as $k => $vo ) {
                if ($vo ['id'] == $question_id) {
                    $question = $vo;
                    $next_id = isset ( $list [$k + 1] ['id'] ) ? $list [$k + 1] ['id'] : '-1';
                }
            }
        }

        $extra = parse_config_attr ( $question ['extra'] );
        $this->assign ( 'survey', $survey );
        $this->assign ( 'question', $question );
        $this->assign ( 'next_id', $next_id );
        $this->assign ( 'extra', $extra );
        $this->assign ( 'num', $num );

        $this->display ();
    }
    function index() {
        $id = $map ['id'] = I ( 'id', 0, 'intval' );
//		$openid = get_openid ();
        $map ['token'] = get_token ();
        $public_info = get_token_appinfo ( $map ['token'] );
        $overtime = $this->_is_overtime ( $id );
        //	$overtime = $overtime ? 1 :( $overtime ? 2 : 0 ) ;

// 		if($overtime= 1)$status="调研进行中";
// 		if($overtime= 2)$status="调研未开始";
// 		if($overtime= 0)$status="调研已结束";
        $this->assign ( 'overtime', $overtime );
// 		$this->assign ( 'status', $status);

        $info = M ( 'ZkEvalPrj' )->where ( $map )->find ();
        $this->assign ( 'info', $info );
        $this->assign ( 'public_info', $public_info );
        $this->display ();
    }
    function finish() {
        $survey_id = I ( 'survey_id', 0, 'intval' );
        // $map ['token'] = get_token ();
        // $info = M ( 'survey' )->where ( $map )->find ();
        $info = D ( 'ZkEvalPrj' )->getSurveyInfo ( $survey_id );
        $public_id = get_token_appinfo ( $map ['token'] )['id'];

        $this->assign ( 'info', $info );
        $this->assign('$public_id',$public_id);
        // 增加积分
        //add_credit ( 'survey' );
        $this->display ();
    }

    // 已过期返回 true ,否则返回 false
    private function _is_overtime($survey_id) {
        // 先看调研期限过期与否
        $the_survey = M ( "ZkEvalPrj" )->find ( $survey_id );

        if ((! empty ( $the_survey ['start_time'] )) && $the_survey ['start_time'] < NOW_TIME && $the_survey ['end_time'] > NOW_TIME)

            return 1; //进行中

        // $deadline = $the_survey ['end_date'] + 86400;
        if ((! empty ( $the_survey ['start_time'] )) && $the_survey ['start_time'] > NOW_TIME )

            return 2; //未开始

        if ((! empty ( $the_survey ['start_time'] )) &&  $the_survey ['end_time'] < NOW_TIME)
            return 0;  //结束



    }
    function lists() {
        $isAjax = I ( 'isAjax' );
        $isRadio = I ( 'isRadio' );
        $model = $this->getModel ( 'ZkEvalPrj' );
        $list_data = $this->_get_model_list ( $model, 0, 'id desc', true );
        // 判断该活动是否已经设置投票调查
        if ($isAjax) {
            $this->assign ( 'isRadio', $isRadio );
            $this->assign ( $list_data );
            $this->display ( 'ajax_lists_data' );
        } else {
            $this->assign ( $list_data );
            $this->display ();
        }
    }
    function add() {
        $this->display ( 'edit' );
    }
    function edit() {
        $id = I ( 'id', 0, 'intval' );
        $model = $this->getModel ( 'ZkEvalPrj' );

        if (IS_POST) {
            //$this->checkDate();
            $act = empty ( $id ) ? 'add' : 'save';
            $Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
            // 获取模型的字段信息
            $Model = $this->checkAttr ( $Model, $model ['id'] );
            $res = false;
            $Model->create () && $res = $Model->$act ();
            if ($res !== false) {
                $act == 'add' && $id = $res;

                $this->_setAttr ( $id, $_POST );
                D ( 'Common/Keyword' )->set ( I ( 'post.keyword' ), 'Weizk', I ( 'post.id' ) );
                $this->success ( '保存成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
            } else {
                $this->error ( $Model->getError () );
            }
        } else {
            // 获取数据
            $data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
            $data || $this->error ( '数据不存在！' );

            $token = get_token ();
            if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
                $this->error ( '非法访问！' );
            }
            $this->assign ( 'data', $data );

            // 字段信息
            $map ['prj_id'] = $id;
            $map ['token'] = $token;
            $list = M ( 'ZkEvalItem' )->where ( $map )->order ( 'id' )->select ();

            $this->assign ( 'attr_list', $list );

            $this->display ( 'edit' );
        }
    }
    // 保存字段信息
    function _setAttr($forms_id, $data) {
        $dao = M ( 'ZkEvalItem' );
        $save ['prj_id'] = $forms_id;

        $old_ids = $dao->where ( $save )->getFields ( 'id' );

        $sort = 0;
        foreach ( $data ['attr_title'] as $key => $val ) {
            $save ['title'] = safe ( $val );
            if (empty ( $save ['title'] ))
                continue;

            $save ['extra'] = safe ( $data ['extra'] [$key] );
            $save ['type'] = safe ( $data ['type'] [$key] );
            $save ['is_must'] = intval ( $data ['is_must'] [$key] );
            $save ['value'] = safe ( $data ['value'] [$key] );
            $save ['remark'] = safe ( $data ['remark'] [$key] );
            $save ['validate_rule'] = safe ( $data ['validate_rule'] [$key] );
            $save ['error_info'] = safe ( $data ['error_info'] [$key] );
            $save ['sort'] = $sort;

            $id = intval ( $data ['attr_id'] [$key] );
            if (! empty ( $id )) {
                $ids [] = $map ['id'] = $id;
                $dao->where ( $map )->save ( $save );
            } else {
                $save ['token'] = get_token ();
                $ids [] = $dao->add ( $save );
            }

            $sort += 1;
        }

        $diff = array_diff ( $old_ids, $ids );
        if (! empty ( $diff )) {
            $map2 ['id'] = array (
                'in',
                $diff
            );
            $dao->where ( $map2 )->delete ();
        }
    }

    function checkDate(){
        // 判断时间选择是否正确
        if (! I ( 'post.start_time' )) {
            $this->error ( '请选择开始时间' );
        } else if (! I ( 'post.end_time' )) {
            $this->error ( '请选择结束时间' );
        } else if (strtotime ( I ( 'post.start_time' ) ) >= strtotime ( I ( 'post.end_time' ) )) {
            $this->error ( '开始时间不能大于或等于结束时间' );
        }
    }
}
