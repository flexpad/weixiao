<?php

namespace Addons\Weixiao\Controller;
use Home\Controller\AddonsController;

class ScoreController extends AddonsController{
    protected $model;
    protected $token;
    protected $school;
    protected $schooltype;
    protected $public_id;
    protected $wx_msg_sending;
    public function __construct() {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct ();
        $this->model = $this->getModel('WxyScore'); //getModelByName ( $_REQUEST ['_controller'] );
        $this->token = get_token();
        $this->school = D('Common/Public')->getInfoByToken($this->token, 'public_name');
        $this->schooltype = D('Common/Public')->getInfoByToken($this->token, 'public_type');
        $this->public_id = D('Common/Public')->getInfoByToken($this->token, 'id');
        $this->wx_msg_sending = false;
        $this->stu_year = 17;  //2017学年，后续实现需要从数据库基础表去数据哈
        //var_dump($this->model);
    }

    /**
     * 显示指定模型列表数据
     */
    public function lists()
    {
        $page = I('p', 1, 'intval'); // 默认显示第一页数据
        // 解析列表规则
        $list_data = $this->_get_model_list($this->model);//_list_grid($this->model);
        $grids = $list_data ['list_grids'];
        $fields = $list_data ['fields'];
        $map ['token'] = get_token();

        // 关键字搜索
        $key = $this->model ['search_key'] ? $this->model ['search_key'] : 'title';

        if (isset ($_REQUEST [$key])) {
            $map [$key] = array(
                'like',
                '%' . htmlspecialchars($_REQUEST [$key]) . '%'
            );
            unset ($_REQUEST [$key]);
        }
        // 条件搜索
        //$date_range = array();
        foreach ($_REQUEST as $name => $val) {
            if (in_array($name, $fields)) {
                $map [$name] = $val;
            }

            if($name == 'start_time' && $val !="")
            {
                ($map['classdate'] == NULL)?$map['classdate']= array(array('gt',$val)):array_push($map['classdate'],array('egt',$val));
            }
            if($name == 'end_time' && $val !="")
            {
                ($map['classdate'] == NULL)?$map['classdate']= array(array('lt',$val)):array_push($map['classdate'],array('elt',$val));
            }
        }
        $row = empty ($this->model ['list_row']) ? 20 : $this->model ['list_row'];

        // 读取模型数据列表
        //var_dump($map);
        empty ($fields) || in_array('id', $fields) || array_push($fields, 'id');
        $name = parse_name(get_table_name($this->model ['id']), true);
        //var_dump($name);
        //exit();
        $data = M($name)->field(empty ($fields) ? true : $fields)->where($map)->order('id')->page($page, $row)->select();

        /* 查询记录总数 */
        $count = M($name)->where($map)->count();

        //var_dump($list_data);
        //var_dump($data);
        //var_dump($count);
        //var_dump($grids);
        //var_dump($this->model);
        //exit();
        // 分页
        if ($count > $row) {
            $page = new \Think\Page ($count, $row);
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('list_grids', $grids);
        $this->assign('list_data', $data);
        $this->assign('search_button',false);

        $key_datetime = array("type"=>"datetime","title"=>"有效时间","start_time"=>"","end_time"=>date("Y/m/d"));
        //$key_datetime = array("type"=>"datetime","title"=>"有效时间");
        //$key_course = array("type"=>"select","title"=>"科目","name"=>"subject","options"=>array(array("value"=>1,"name"=>"语文","title"=>"语文"),array("value"=>2,"name"=>"数学","title"=>"数学")));
        $key_course = array("type"=>"input","title"=>"科目","name"=>"subject");
        $muti_keys = array($key_datetime,$key_course,NULL);
        $this->assign('muti_search',$muti_keys);
        $this->assign('search_key',array('classdate','subject'));
        $this->meta_title = $this->model ['title'] . '列表';

        $this->display('lists');
    }
    /*
    public function edit(){

        $this->display();
    }
    */

    public function add(){
        $courseImport = D('WxyClassCourseimport')->query("SELECT * FROM `wp_wxy_class_course` ORDER by `valid_date` desc limit 1");
        $map = array("token"=>$this->token,"grade"=>"2","class_id"=>"2","valid_date"=>$courseImport[0]["valid_date"]);
        $course_data = M('WxyClassCourse')->where($map)->select();
        //var_dump($course_data);
        $sel_course = array();
        for ($i = 0; $i < count($course_data);$i++){
            $sel_course[$i]["id"] = $course_data[$i]['id'];
            $sel_course[$i]["name"] = $course_data[$i]['course_name'];
            $sel_course[$i]["teacher"] = $course_data[$i]['teacher'];
        }
        //var_dump($sel_course);
        $this->assign('public_id', $this->public_id);
        $this->assign('course_lists',$sel_course);
        $this->assign('course_valid_date',$course_data[0]["valid_date"]);
        $this->display('import');
    }

    public function score_ajax_filter(){
        if (!IS_POST) $this->error("请在表单中提交！");

        $public_id = I('public_id');
        $map ['token'] = D('Common/Public')->getinfo($public_id, 'token');
        if ($map ['token'] == NULL) $this->error("公众号ID错误，请输入正确的公众号ID！");

        $map['class_id'] = I('exam_class');
        $map['grade'] = I('exam_grade');
        $valid_date = I('valid_date');
        if ($valid_date != NULL) {
            $map['valid_date'] = array('egt',$valid_date);
        }

        //var_dump($map);
        $course_data = M('WxyClassCourse')->where($map)->select();
        //var_dump($course_data);

        $sel_course = array();
        for ($i = 0; $i < count($course_data);$i++){
            $sel_course[$i]["id"] = $course_data[$i]['id'];
            $sel_course[$i]["name"] = $course_data[$i]['course_name'];
            $sel_course[$i]["teacher"] = $course_data[$i]['teacher'];
        }
        //var_dump($sel_course);
        $this->ajaxReturn($sel_course,'JSON');
    }

    public function import(){
        $uid = $this->uid;
        $token = $this->token;
        //$file_id = 7;
        //$data = $this->import_student_data_from_excel($file_id);

        if ($uid == 0) redirect(U('/Home/Public'));
        if (IS_POST) {
            $data['token'] = $token;

            $data['term'] = I('post.term');
            $data['file'] = I('post.file');
            $data['classdate'] = I('post.classdate');
            $data['comment'] = I('post.comment');
            $grade = I('post.exam_grade');
            $class_id = I('class_id');
            if ($grade == NULL || $class_id == NULL) $this->error('请务必输入此次导入成绩对应的年级和班号！');
            else $grade = sprintf('%2s', $this->stu_year - intval($grade));

            $sendflag = (I('post.msgsend') == "on")?true:false ;

            if (!intval($data['file'])) $this->error("数据文件未上传！");
            $import_model = D('WxyScoreimport');
            $res = $import_model->addImport($data);
            $data['termid'] = $res;
            //$data['subject'] = $course_obj[1];

            if ($this->import_student_score_from_excel($data['file'], $data, $grade, $class_id, $sendflag)) //import student data from uploaded Excel file.
            {
                if ($sendflag == true)
                {
                    $this->wx_msg_sending = true;
                    $this->success("系统正在发送微信消息....",U ('send_wx_msg?pageid=1&termid='.$data['termid']),6 );
                }
                else
                    $this->success('保存成功！', U ( 'lists'/*'import?model=' . $this->model ['name'], $this->get_param */), 600);
            }
            else
                $this->error('请检查文件格式');
        }
        else {
            if($this->wx_msg_sending){
                //$this->jump(U('send_wx_msg?termid='.$base_data['termid'].'&pageid=1'), "系统正在发送微信消息....");
            }
            else{
                $this->assign('public_id', $this->public_id);
                $this->assign('course_valid_date',date('Y-m-d',strtotime('-1 year')));
                $this->display('import');
            }

        }
    }

    // Send a Weixin template message to use to notify the score:
    public function send()
    {
        $score_id = I('id');
        $map['id'] = $score_id;
        $score_data = D('WxyScoreNotifyView')->where($map)->select();
        //var_dump($score_data);
        $statue = 0;
        foreach ($score_data as $value) {
            $url = U('addon/Weixiao/Wap/score', array('publicid'=>$this->public_id, 'studentno' => $value['studentno']));
            //var_dump($value);
            $retdata = D('WxyScore')->send_score_to_user($value['openid'], $url, $value);

            $statue += ($retdata["errcode"] == 0)?0:1;
            usleep(60000);
        };

        if($statue == 0) {
            $this->success("此次成绩通知单已经发送到关注该学生的微信号上！");

            $data = M('WxyScore')->where($map)->select()[0];
            $data["weixinmsgsend"] = "已发送";
            //var_dump($data);
            M('WxyScore')->where($map)->save($data);
        }
        else
            $this->error("成绩通知单发送错误！");
    }

    public function send_wx_msg()
    {
        $page = I('pageid', 1, 'intval'); // 默认显示第一页数据
        $map['token'] = $this->token;
        $map['termid'] = I('termid');
        $row = 10;
        //var_dump($map);
        $data = M('WxyScore')->where($map)->order('id')->page($page, $row)->select();
        //var_dump($data);
        if(count($data) == 0) {
            $this->wx_msg_sending = false;
            $this->success("微信消息发送成功",U ('lists'), 5);
        }
        else {
            foreach ($data as $item) {
                $this->wx_send_msg($item['id']);
                $tmp_map['id'] = $item['id'];
                $item["weixinmsgsend"] = "已发送";
                M('WxyScore')->where($tmp_map)->save($item);
            }
            $page += 1;
            $this->jump(U('send_wx_msg?pageid='.$page.'&termid='.$map['termid']), "系统正在发送微信消息....");
        }

    }

    private function wx_send_msg($score_id){
        $map['id'] = $score_id;
        $score_data = D('WxyScoreNotifyView')->where($map)->select();
        //var_dump($score_data);

        foreach ($score_data as $value) {
            $url = U('addon/Weixiao/Wap/score', array('publicid'=>$this->public_id, 'studentno' => $value['studentno']));
            //var_dump($value);
            $retdata = D('WxyScore')->send_score_to_user($value['openid'], $url, $value);
            //echo($retdata);
            if($retdata["errcode"] == 0)
                usleep(30000);
            else
                usleep(1000);
        };

    }
    //This function was modified for full time school under Weixiao addon.
    private function add_score_in_model($row,$fromat)
    {
        $score_model = D('WxyScore');
        $data['studentno'] = $row['studentno'];
        $data['token'] = $this->token;
        $data['classdate'] = $row['classdate'];
        $data['termid'] = $row['termid'];
        $data['term'] = $row['term'];
        $data['name'] = $row['studentname'];

        foreach ($fromat as $item) {
            if( $row[$item['course_name']] == '') continue; //The blank item will not be saved.
            else {
                $data['subject'] = $item['subject']; // To separate subject and course classification.
                $data['course_name'] = $item['course_name'];
                $data['exmscore'] = $row[$item['course_name']];
                $data['weixinmsgsend'] = "未发送";
                $score_model->addScore($data);
            }
        }
    }
    private function import_student_score_from_excel($file_id, $base_data, $grade, $class_id, $sendflag) {
        $tableform_map['token'] = $this->token;
        $fromat = M('WxyScoreTableformat')->where($tableform_map)->select();

        $column = array (
            'A' => 'classid',     //班级
            'B'=>'studentno',     //学号
            'C'=>'studentname',   //学生姓名
            'D'=>'examnum',       //考号
        );
        foreach ($fromat as $item) {
            $column[$item['column']] = $item['course_name'];
        }

        $data = importFormExcel($file_id, $column);

        if ($data['status']) {
            foreach  ($data['data'] as $row) {
                $row['token'] = $this->token;
                $row['termid']= $base_data['termid'];
                $row['classdate'] = $base_data['classdate'];
                $row['term'] = $base_data['term'];
                if (strlen($row['studentno']) < 3) { //Convert the short format student number to normal format!
                    $row['studentno'] = $grade. sprintf('%02s', $class_id). sprintf('%02s', $row['studentno']);
                }
                //dump($row);
                $this->add_score_in_model($row, $fromat);
            }
            return true;
        }
        else return false;
    }

    function jump($url, $msg) {
        $this->assign ( 'url', $url );
        $this->assign ( 'msg', $msg );
        $this->display ( 'jump' );
        //exit ();
    }
}
