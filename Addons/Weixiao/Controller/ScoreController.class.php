<?php

namespace Addons\Weixiao\Controller;
use Home\Controller\AddonsController;

class ScoreController extends AddonsController{
    protected $model;
    protected $token;
    protected $school;
    protected $schooltype;
    protected $public_id;
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

        //var_dump($this->uid);
        //var_dump($this->mid);
        //U('edit', array('id'=>I('request.id')));
        $uid = $this->uid;
        $token = $this->token;
        //$file_id = 7;
        //$data = $this->import_student_data_from_excel($file_id);

        if ($uid == 0) redirect(U('/Home/Public'));
        if (IS_POST) {
            $data['token'] = $token;
            $course_obj = explode('.',I('post.courseid'));
            $data['teacher'] = $course_obj[2];
            $data['courseid'] = $course_obj[0];
            $data['term'] = I('post.term');
            $data['file'] = I('post.file');
            $data['classdate'] = I('post.classdate');
            $data['comment'] = I('post.comment');
            $sendflag = (I('post.msgsend') == "on")?true:false ;

            if (!intval($data['file'])) $this->error("数据文件未上传！");
            $import_model = D('WxyScoreimport');
            $res = $import_model->addImport($data);
            $data['termid'] = $res;
            $data['subject'] = $course_obj[1];

            if ($this->import_student_score_from_excel($data['file'],$data,$sendflag)) //import student data from uploaded Excel file.
                $this->success('保存成功！', U ( 'lists'/*'import?model=' . $this->model ['name'], $this->get_param */), 600);
            else
                $this->error('请检查文件格式');
        }
        else {
            $this->assign('public_id', $this->public_id);
            $this->assign('course_valid_date',date('Y-m-d',strtotime('-1 year')));
            $this->display('import');
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

    private function wx_send_msg($score_id){
        $map['id'] = $score_id;
        $score_data = D('WxyScoreNotifyView')->where($map)->select();
        //var_dump($score_data);

        foreach ($score_data as $value) {
            $url = U('addon/Weixiao/Wap/score', array('publicid'=>$this->public_id, 'studentno' => $value['studentno']));
            //var_dump($value);
            $retdata = D('WxyScore')->send_score_to_user($value['openid'], $url, $value);
            if($retdata["errcode"] == 0)
                usleep(30000);
            else
                usleep(1000);
        };

    }
    //This function was modified for full time school under Weixiao addon.
    private function import_student_score_from_excel($file_id,$base_data,$sendflag) {
        $data = array();
        $column = array (
            'A' => 'studentno',  //学生编号
            'B'=>'score1',     //课堂表现
            'C'=>'score2',     //出勤情况
            'D'=>'score3',      //作业完成
            'E'=>'exmscore',    //测试分
            'F'=>'comment',     //备注
        );
        $data = importFormExcel($file_id, $column);
        $score_model = D('WxyScore');
        if ($data['status']) {
            foreach  ($data['data'] as $row) {
                $row['token'] = $this->token;
                $row['termid']= $base_data['termid'];
                $row['classdate'] = $base_data['classdate'];
                $row['courseid'] = $base_data['courseid'];
                $row['subject'] = $base_data['subject'];
                $row['term'] = $base_data['term'];

                $map['token'] =  $this->token;
                $map['studentno'] =  $row['studentno'];
                $stu_arry = M('WxyStudentCard')->where($map)->select();

                if (count($stu_arry) != 0) $row['name'] = $stu_arry[0]['name'];
                //var_dump($map,$stu_arry,$stu_arry[0]['name'],count($stu_arry),$row);
                $row['weixinmsgsend'] = $sendflag?"已发送":"未发送";
                $it = $score_model->addScore($row);
                if($sendflag){
                    //var_dump($it);
                    $this->wx_send_msg($it);
                }

            }
            return true;
        }
        else return false;
    }

}
