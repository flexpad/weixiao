<?php
/*
 * Created by PhpStorm.
 * User: zhuke
 * Date: 2017/6/2
 * Time: 20:13
 */


namespace Addons\Weizk\Controller;

use Addons\Weizk\Controller\BaseController;

class EvalPrjWapController extends BaseController
{
    var $config;
    var $token;
    var $publicid;

    function _initialize()
    {
        parent::_initialize();
    }

    public function __construct()
    {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct();
        $this->model = $this->getModel('ZkEvalPrj');
        $this->token = get_token();
        $this->publicid = D('Common/Public')->getInfoByToken($this->token, 'id');
    }

    public function show(){
        $map['id'] = intval(I('survey_id'));
        $public_id = intval(I('publicid'));
        $data = M('ZkEvalPrj')->where($map)->find();
        if($data == NULL)  $this->error("没有这个测试项目！");
        $infor['title'] = $data['name'];
        $this->assign('info',$infor);
        $this->display('show');
    }

    function survey() {
        $id = I ( 'get.id', 0, 'intval' );
        $num = I ( 'num', 1, 'intval' );
        $token = get_token ();
        $survey = D ( 'ZkEvalPrj' )->getEvalInfo ( $id );
        $list = D ( 'ZkEvalItem' )->getItemInfo ( $id );
        if (IS_POST) {
            $follow_id = $this->mid;
            $question_id = I ( 'post.question_id', 0, 'intval' );
            $answer = D ( 'SurveyAnswer' )->getAnswerInfo ( $id, $question_id, $follow_id );

            $data ['cTime'] = time ();
            $data ['answer'] = serialize ( $_POST ['answer'] );
            if ($answer) {
                D ( 'SurveyAnswer' )->updateAnswer ( $id, $question_id, $follow_id, $data );
            } else {
                $data ['survey_id'] = $id;
                $data ['token'] = $token;
                $data ['question_id'] = $question_id;
                $data ['uid'] = $follow_id;
                $data ['openid'] = get_openid ();
                M ( 'survey_answer' )->add ( $data );
                D ( 'SurveyAnswer' )->getAnswerInfo ( $id, $question_id, $follow_id, true );
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

        $this->display ("survey");
    }
}