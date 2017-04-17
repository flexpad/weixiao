<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2016/11/9
 * Time: 10:13
 */


namespace Addons\Student\Controller;

use Addons\Student\Model\WxyStudentCareViewModel;
use Home\Controller\AddonsController;

class WapController extends AddonsController {
    var $config;
    function _initialize() {
        parent::_initialize ();
        $this->assign('nav',null);
        $config = getAddonConfig ( 'WeiSite' );
        $config ['cover_url'] = get_cover_url ( $config ['cover'] );
        $config['background_arr']=explode(',', $config['background']);
        $config ['background_id'] = $config ['background_arr'][0];
        $config ['background'] = get_cover_url ( $config ['background_id'] );
        $this->config = $config;
        $this->assign ( 'config', $config );
        //dump ( $config );
        // dump(get_token());

        // 定义模板常量
        $act = strtolower ( _ACTION );
        $temp = $config ['template_' . $act];
        $act = ucfirst ( $act );
        $this->assign ( 'page_title', $config ['title'] );
        define ( 'CUSTOM_TEMPLATE_PATH', ONETHINK_ADDON_PATH . 'WeiSite/View/default/Template' );
    }
    var $model;
    var $token;
    var $school;
    //var $openid;
    //var $uid;
    public function __construct() {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct ();
        $this->model = $this->getModel('WxyStudentCare'); //getModelByName ( $_REQUEST ['_controller'] );
        $this->token = get_token();
        $this->school = D('Common/Public')->getInfoByToken($this->token, 'public_name');
        /*var_dump($this->model);
        var_dump($_REQUEST ['_controller']);

        exit();
        $this->model || $this->error ( '模型不存在！' );

        $this->assign ( 'model', $this->model );
        */

    }
    public function bind() {
        $openid = get_openid();
        $studentcare_model = D('WxyStudentCare');
        if (IS_POST) {
            $follow_data = array();
            $studentcare = array();
            $studentcard_model = D('WxyStudentCard');
            $studentno = trim(I('studentno'));
            $name = trim(I('post.name'));
            $phone = trim(I('post.mobile'));

            $map['openid'] = $user['openid'] = $openid;
            $user['uid'] = $this->uid;

            $follow_model = M('public_follow');
            $data = $follow_model->where($map)->find();

            if ($this->token == NULL || $user['openid'] == NULL)
                $this->error("请在微信中打开！");

            $access_token = get_access_token ($this->token);
            $suburl = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token . '&openid=' . $user ['openid'] . '&lang=zh_CN';
            $userdata = file_get_contents ( $suburl );
            $userdata = json_decode ( $userdata, true );
            //var_dump($userdata);
            //exit();
            if ($data == NULL && $userdata['subscribe'] == 1) {
                $follow_data['uid'] = 0;
                $follow_data['openid'] = $map['openid'];
                $follow_data['token'] = $this->token;
                $follow_data['has_subscribe'] = 1;
                $follow_data['syc_status'] = 0;
                $follow_data['remark'] = 'added by student bind';
                $follow_model->add($follow_data); //Add a record for new follower.
                //Need add user data here also TBD.
            }
            else if ($data == NULL && $userdata['subscribe'] == 0)
                $this->error("请关注我们的微信公号后再绑定学生！！！", U('bind'));
            else if ($data != NULL) {
                $data['has_subscribe'] = $userdata['subscribe'];
                if ($userdata['subscribe'] == 0) {
                    $follow_model->where($map)->save($data);
                    $this->error("请关注我们的微信公号后再绑定学生！！！", U('bind'));
                }
                else {
                    $follow_model->where($map)->save($data);
                    $student['studentno'] = $studentno;
                    $student['name'] = $name;
                    $student['phone'] = $phone;
                    $student['token'] = $this->token;
                    //$student = $studentcard_model->verify($student);
                    $res = $studentcare_model->approve($student, $user, $this->token);
                    if ($res == 2) {
                        $this->success("已绑定学号为：". $student['studentno']. "的学生！", U('bind'));
                    }
                    else
                        if ($res == 1)
                            $this->error("已绑定过：". $student['studentno']. "号学生！", U('bind'));
                        else
                            $this->error("学生信息有误，请返回重新输入！", U('bind'));

                }
            }
            $this->error("学生信息有误，请返回重新输入！");
        }
        else {
            $public_id = intval(I('publicid'));
            $public_id = ($public_id > 0) ? $public_id:1;

            $map['id'] = $public_id;
            $data = M('public')->where($map)->find();
            if ($this->token == NULL)
                $this->token = $data['token'];

            //var_dump($public_id);
            //var_dump($this->token);
            $map2['openid'] = $openid;
            $this->assign('care_count', $studentcare_model->where($map2)->count());
            $this->assign('oid', $data['public_name']);
            $this->_footer();
            $this->display('bind_student');
        }
    }

    public function index()
    {
        $this->display('bind_student');
    }

    public function infor() {
        $public_id = intval(I('publicid'));
        $public_id = ($public_id > 0) ? $public_id:1;

        $map['id'] = $public_id;
        $data = M('public')->where($map)->find();

        if ($this->token == NULL)
            $this->token = $data['token'];

        //var_dump($public_id);
        //var_dump($data);

        unset($map);
        unset($data);
        $studentcare_view = D('WxyStudentCareView');
        $map['token'] = $this->token;
        //var_dump($studentcare_view);
        if ($this->uid) {
            $map['uid'] = $this->uid;
        }
        else {
            $map['openid'] = get_openid();
        }
        $map['is_audit'] = 1;
        //var_dump($map);
        $data = $studentcare_view->where($map)->select();
        //var_dump($studentcare_view->_sql());

        $this->assign('list', $data);
        $this->assign('public_id', $public_id);
        $this->_footer();
        $this->display('Infor');
    }

    public function score(){
        // retrieve db table get: course, bind, dailytime, score.
        // filter by courseid.
        $public_id = I('public_id', 0, 'intval');
        empty ($public_id) && $public_id = I('publicid', 0, 'intval');
        $public_id = ($public_id > 0) ? $public_id:1;

        $studentno = I('studentno');
        if ($studentno == NULL) $this->error("学号错误，请输入正确的学号！");

        //$map['id'] = $public_id;
        $map ['token'] = D('Common/Public')->getinfo($public_id, 'token');
        if ($map ['token'] == NULL) $this->error("公众号ID错误，请输入正确的公众号ID！");
        //unset($map);
        //$map['token'] = $this->token;

        $map['studentno'] = $studentno;
        $map['openid'] = get_openid();
        $model = D('WxyStudentPerformView');

        $data = $model->where($map)->select();
        if ($data == NULL)
            $this->error("你尚未关注我校学生，请返回关注后再查询成绩！");

        /*
        $i = 1;
        foreach ($data as $key => $vo) {
            $vo['sid'] = strval($key);
            $vo['public_name'] = D('Common/Public')->getinfo($public_id, 'public_name');
        }
        */
        $student_name = $data[0]['student_name'];

        //var_dump($model->_sql());
        //var_dump($data);
        $this->assign('public_id',$public_id);
        $this->assign('studentno', $studentno);
        $this->assign('student_name', $student_name);

        $this->assign('data', $data);
        //var_dump($data);
        $this->_footer();
        $this->display('score');

    }

    public function comment(){
        // retrieve db table get: course, bind, dailytime, score.
        // filter by courseid.
        $public_id = I('public_id', 0, 'intval');
        empty ($public_id) && $public_id = I('publicid', 0, 'intval');
        $public_id = ($public_id > 0) ? $public_id:1;

        $studentno = I('studentno');
        if ($studentno == NULL) $this->error("学号错误，请输入正确的学号！");

        //$map['id'] = $public_id;
        $map ['token'] = D('Common/Public')->getinfo($public_id, 'token');
        if ($map ['token'] == NULL) $this->error("公众号ID错误，请输入正确的公众号ID！");
        //unset($map);
        //$map['token'] = $this->token;

        $map['studentno'] = $studentno;
        $map['openid'] = get_openid();
        $model = D('WxyStudentCommentsView');

        $data = $model->where($map)->order('courseid')->select();
        if ($data == NULL)
            $this->error("改学生上课的评语还未上传，请关注Q群通知，敬请期待！");

        /*
        $i = 1;
        foreach ($data as $key => $vo) {
            $vo['sid'] = strval($key);
            $vo['public_name'] = D('Common/Public')->getinfo($public_id, 'public_name');
        }
        */
        $student_name = $data[0]['student_name'];

        //var_dump($model->_sql());
        //var_dump($data);
        $this->assign('public_id',$public_id);
        $this->assign('studentno', $studentno);
        $this->assign('student_name', $student_name);

        $this->assign('data', $data);
        $this->_footer();
        $this->display('reviews');

    }

    // 3G页面底部导航
    function _footer($temp_type = 'weiphp') {
        if ($temp_type == 'pigcms') {
            $param ['token'] = $token = get_token ();
            $param ['temp'] = $this->config ['template_footer'];
            $url = U ( 'Home/Index/getFooterHtml', $param );
            $html = wp_file_get_contents ( $url );
            // dump ( $url );
            // dump ( $html );
            $file = RUNTIME_PATH . $token . '_' . $this->config ['template_footer'] . '.html';
            if (! file_exists ( $file ) || true) {
                file_put_contents ( $file, $html );
            }

            $this->assign ( 'cateMenuFileName', $file );
        } else {
            $list = D ( 'Addons://WeiSite/Footer' )->get_list ();
            //var_dump($list);

            foreach ( $list as $k => $vo ) {
                if ($vo ['pid'] != 0)
                    continue;

                $one_arr [$vo ['id']] = $vo;
                unset ( $list [$k] );
            }

            foreach ( $one_arr as &$p ) {
                $two_arr = array ();
                foreach ( $list as $key => $l ) {
                    if ($l ['pid'] != $p ['id'])
                        continue;

                    $two_arr [] = $l;
                    unset ( $list [$key] );
                }

                $p ['child'] = $two_arr;
            }
            $this->assign ( 'footer', $one_arr );
            if (empty ( $this->config ['template_footer'] )) {
                $this->config ['template_footer'] = 'V2';
            }
            //define ('CUSTOM_TEMPLATE_PATH',  './Addons/Weisite/View/default/Template');
            $html = $this->fetch ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateFooter/' . $this->config ['template_footer'] . '/footer.html' );
            $this->assign ( 'footer_html', $html );
        }
    }

    public function show_attendance()
    {

        $public_id = I('public_id', 0, 'intval');
        empty ($public_id) && $public_id = I('publicid', 0, 'intval');
        $public_id = ($public_id > 0) ? $public_id:1;

        $studentno = I('studentno');
        if ($studentno == NULL) $this->error("学号错误，请输入正确的学号！");

        //$map['id'] = $public_id;
        $stuCard_map ['token'] = D('Common/Public')->getinfo($public_id, 'token');
        if ($stuCard_map ['token'] == NULL) $this->error("公众号ID错误，请输入正确的公众号ID！");


        $stuCard_map['studentno'] = $studentno;
        $stu_model = D('WxyStudentCard');

        $stu_data = $stu_model->where($stuCard_map)->find();
        if ($stu_data == NULL)
            $this->error("没有此学号（%s)的学生",$studentno);

        //var_dump(stu_data);
        $search_start_date = I('search_start_date');
        $search_end_date = I('search_end_date');
        if (($search_end_date == NULL) || ($search_start_date==NULL))
        {
            //show all
        }
        else
        {
            $timeBegin = strtotime($search_start_date);
            $timeEnd = strtotime($search_end_date);
            $attend_map['arriveTime'] = array('BETWEEN',array($timeBegin,$timeEnd));
        }
        $attend_map['studentID'] = $studentno;
        $attend_map['Token'] = $stuCard_map ['token'];

        $attend_model = D('WxyDailyTime');
        $data = $attend_model->where($attend_map)->select();
        //var_dump($data);
        $this->assign('public_id',$public_id);
        $this->assign('studentName',$stu_data['name']);
        $this->assign('studentNo',$studentno);
        $state = ['正常','迟到','早退','缺席'];
        foreach ($data as $key=>$vo) {
            if ($vo['arriveTime'] != 0)
                $data[$key]['arriveTime'] = date('Y-m-d H:i:s',$vo['arriveTime']);
            else
                $data[$key]['arriveTime'] = '-------------';
            if ($vo['leaveTime'] != 0)
                $data[$key]['leaveTime'] = date('Y-m-d H:i:s',$vo['leaveTime']);
            else
                $data[$key]['leaveTime'] = '-------------';
            $data[$key]['state'] = $state[(int)$data[$key]['state']];
        }
        $this->assign('data', $data);
        $this->display('attendance_sheet');
    }

    public function attendace_ajax_show()
    {
        if (!IS_POST) $this->error("请在表单中提交！");

        $public_id = I('public_id');
        $stuCard_map ['token'] = D('Common/Public')->getinfo($public_id, 'token');
        if ($stuCard_map ['token'] == NULL) $this->error("公众号ID错误，请输入正确的公众号ID！");

        $studentNo = I('studentNo');

        $data = NULL;
        if ((I('search_start_date')) == '' || (I('search_end_date')) == '')
        {
            $this->ajaxReturn($data,'JSON');
            return;
        }
        $str_start = (I('search_start_date')).' 00:00:00';
        $str_end = (I('search_end_date')).' 23:59:59';
        //var_dump($str_start);
        //var_dump($str_end);
        $start_date = strtotime($str_start);
        $end_date = strtotime($str_end);
        $attend_map['arriveTime'] = array('BETWEEN',array($start_date,$end_date));

        $attend_map['studentID'] = $studentNo;
        $attend_map['Token'] = $stuCard_map ['token'];
        $attend_model = D('WxyDailyTime');
        $data = $attend_model->where($attend_map)->select();
        $state = ['正常','迟到','早退','缺席'];
        foreach ($data as $key=>$vo) {

            if ($vo['arriveTime'] != 0)
                $data[$key]['arriveTime'] = date('Y-m-d H:i:s',$vo['arriveTime']);
            else
                $data[$key]['arriveTime'] = '-------------';
            if ($vo['leaveTime'] != 0)
                $data[$key]['leaveTime'] = date('Y-m-d H:i:s',$vo['leaveTime']);
            else
                $data[$key]['leaveTime'] = '-------------';
            $data[$key]['state'] = $state[(int)$data[$key]['state']];
        }
        $this->ajaxReturn($data,'JSON');
    }

    public function score_ajax_show()
    {
        if (!IS_POST) $this->error("请在表单中提交！");

        $public_id = I('public_id');
        $stuCard_map ['token'] = D('Common/Public')->getinfo($public_id, 'token');
        if ($stuCard_map ['token'] == NULL) $this->error("公众号ID错误，请输入正确的公众号ID！");

        $studentNo = I('studentNo');
        $start_date = (I('search_start_date'));
        $end_date = (I('search_end_date'));
        //var_dump($str_start);
        //var_dump($str_end);
        if ($end_date || ($start_date == NULL && $end_date == NULL))
            $map['classdate'] = array('BETWEEN',array($start_date,$end_date));
        $map['token'] = $stuCard_map ['token'];
        $map['studentno'] = $studentNo;
        $map['openid'] = get_openid();
        $model = D('WxyStudentPerformView');

        $data = $model->where($map)->select();
        /*var_dump($model->_sql());*/
        if ($data == NULL)
            $this->ajaxReturn(NULL,'JSON');
            //$this->error("你尚未关注我校学生，请返回关注后再查询成绩！");
        
        $this->ajaxReturn($data,'JSON');
    }
}
