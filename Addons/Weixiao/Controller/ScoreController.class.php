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

        //var_dump($grids);
        // 关键字搜索
        $map ['token'] = get_token();
        $key = $this->model ['search_key'] ? $this->model ['search_key'] : 'title';
        
        if (isset ($_REQUEST [$key])) {
            $map [$key] = array(
                'like',
                '%' . htmlspecialchars($_REQUEST [$key]) . '%'
            );
            unset ($_REQUEST [$key]);
        }
        // 条件搜索
        foreach ($_REQUEST as $name => $val) {
            if (in_array($name, $fields)) {
                $map [$name] = $val;
            }
        }

        var_dump($map);
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
        $this->assign('search_key', 'name');
        $key_datetime = array("type"=>"datetime","title"=>"有效时间","start_time"=>date("Y/m/d"),"end_time"=>date("Y/m/d"));
        $key_course = array("type"=>"select","title"=>"科目","options"=>array(array("value"=>1,"name"=>"语文","title"=>"语文"),array("value"=>2,"name"=>"数学","title"=>"数学")));
        $muti_keys = array($key_datetime,$key_course,NULL);
        $this->assign('muti_search',$muti_keys);
        $this->meta_title = $this->model ['title'] . '列表';

        $this->display('lists');
    }
    /*
    public function edit(){

        $this->display();
    }
    */
    public function add(){
        //$map = array('token'=>this.token);
        //$fields = array('token');

        //$course_data = M('')->field(empty ($fields) ? true : $fields)->where($map);

        $sel_course = array(["id"=>1,"name"=>"语文"],["id"=>2,"name"=>"数学"]);
        $this->assign('course_lists',$sel_course);
        $this->display('import');
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
            $data['teacher'] = I('post.teacher');
            $data['courseid'] = explode('.',I('post.courseid'))[0];
            $data['term'] = I('post.term');
            $data['file'] = I('post.file');
            $data['classdate'] = I('post.classdate');
            $data['comment'] = I('post.comment');
            if (!intval($data['file'])) $this->error("数据文件未上传！");
            $import_model = D('WxyScoreimport');
            $res = $import_model->addImport($data);
            $data['termid'] = $res;
            $data['subject'] = explode('.',I('post.courseid'))[1];

            if ($this->import_student_score_from_excel($data['file'],$data)) //import student data from uploaded Excel file.
                $this->success('保存成功！', U ( 'lists'/*'import?model=' . $this->model ['name'], $this->get_param */), 600);
            else
                $this->error('请检查文件格式');
        }
        else {
            $this->assign('public_id', $this->public_id);
            $this->display('import');
        }
    }

    //This function was modified for full time school under Weixiao addon.
    private function import_student_score_from_excel($file_id,$base_data) {
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
                $score_model->addScore($row);
            }
            return true;
        }
        else return false;
    }
    
}
