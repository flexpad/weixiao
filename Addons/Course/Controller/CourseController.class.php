<?php

namespace Addons\Course\Controller;
use Home\Controller\AddonsController;

class CourseController extends AddonsController{
    protected $model;
    protected $token;
    protected $school;
    public function __construct() {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct ();
        $this->model = $this->getModel('WxyCourse'); //getModelByName ( $_REQUEST ['_controller'] );
        $this->token = get_token();
        $this->school = D('Common/Public')->getInfoByToken($this->token, 'public_name');

        /*var_dump($this->model);
        var_dump($_REQUEST ['_controller']);

        exit();
        $this->model || $this->error ( '模型不存在！' );
        */

        $this->assign ( 'model', $this->model );


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

        $row = empty ($this->model ['list_row']) ? 20 : $this->model ['list_row'];

        // 读取模型数据列表

        empty ($fields) || in_array('id', $fields) || array_push($fields, 'id');
        $name = parse_name(get_table_name($this->model ['id']), true);
        //var_dump($name);
        //exit();
        $data = M($name)->field(empty ($fields) ? true : $fields)->where($map)->order('id')->page($page, $row)->select();

        /* 查询记录总数 */
        $count = M($name)->where($map)->count();

        //var_dump($list_data);
        //var_dump($data);
        //var_dump($name);
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
        $this->meta_title = $this->model ['title'] . '列表';

        $this->display();
    }

    public function add() {
        //var_dump($this);
        if (IS_POST) {
            $data['name'] = I('post.name');
            $data['teacher'] = I('post.teacher');
            $data['intro'] = I('post.intro');
            $data['cover'] = intval(I('post.cover'));
            $data['token'] = $this->token;
            $data['sdate'] = I('post.sdate');
            $data['edate'] = I('post.edate');
            $data['length'] = I('post.length');
            $data['timestamp'] = date('Y-m-d H:i:s');
            //var_dump($data['timestamp']);
            $id = I('post.id');
            //$map['cover'] = $data['cover'];
            $model = M('WxyCourse');
            if (!intval($id)) {
                $map['name'] = array('like',$data['name']);
                if ($model->where($map)->select())
                    $this->error("该课程已经录入");
                else {
                    M('WxyCourse')->add($data);
                    $this->success("课程内容录入成功");
                }
            }
            else {
                $map['id'] = I('post.id');
                $model->where($map)->save($data);
                $this->success("课程内容更新成功");
            }
        }
        else {
            //$data['teacher'] = '任老师';
            //$data['sdate'] = '2017-01-12';
            //$this->assign('data', $data);
            $this->assign('id', '0');
            $this->display();
        }
    }

    public function edit() {
        //var_dump($this);
        //$data['teacher'] = '任老师';
        //$data['sdate'] = '2017-01-12';
        //$this->assign('data', $data);
        $map['id'] = I('id');
        $model = M('WxyCourse');
        $data = $model->where($map)->find();
        $this->assign('id', $map['id']);
        $this->assign('data', $data);
        $this->display('add');
    }

    public function scoreimport() {
        $id = I('id');
        $model = M('WxyCourse');
        
        if (IS_POST) {
            $data['file'] = I('post.file');
            $data['courseid'] = ltrim(strstr(I('post.course'), '.', true));
            $data['classdate'] = I('post.classdate');
            $data['comment'] = I('post.comment');
            $data['token'] = $this->token;
            if (!intval($data['file'])) $this->error("数据文件未上传！");
            $import_model = D('WxyScoreimport');
            $import_model->addImport($data);
            if ($this->import_data_from_excel($data['file'], $data['courseid'], $data['classdate'])) //import student data from uploaded Excel file.
                $this->success('保存成功！', U ( 'lists'/*'import?model=' . $this->model ['name'], $this->get_param */), 600);
            else
                $this->error('请检查文件格式');
        }
        else {
            if ($id) $map['id'] = $id;
            $map['token'] = $this->token;
            $data = $model->where($map)->select();
            $this->assign('lists', $data);
            $this->display('import');
        }
    }

    public function comment() {
        $id = I('id');
        $model = M('WxyCourse');

        if (IS_POST) {
            $data['file'] = I('post.file');
            $data['courseid'] = ltrim(strstr(I('post.course'), '.', true));
            $data['comment'] = I('post.comment');
            $data['token'] = $this->token;
            if (!intval($data['file'])) $this->error("数据文件未上传！");
            $import_model = M('wxy_course_commentsimport');
            $import_model->add($data);
            if ($this->import_comments_from_excel($data['file'], $data['courseid'], $data['classdate'])) //import student data from uploaded Excel file.
                $this->success('保存成功！', U ( 'lists'/*'import?model=' . $this->model ['name'], $this->get_param */), 600);
            else
                $this->error('请检查文件格式');
        }
        else {
            if ($id) $map['id'] = $id;
            $map['token'] = $this->token;
            $data = $model->where($map)->select();
            $this->assign('lists', $data);
            $this->display('commentimport');
        }
    }

    private function import_comments_from_excel($file_id, $courseid = NULL, $classdate = NULL) {
        if ($courseid == NULL) return false;
        $data = array();
        $column = array (
            'A' => 'studentno',
            /*
            'B' => 'uid',
            'C' => 'token',
            'D'=>'oid',
            */
            'B'=>'comments_txt',
            /*'C'=>'score2',
            'D'=>'score3',
            'E'=>'score',
            'F'=>'exmscore',
            'G'=>'comment'*/
        );
        $data = importFormExcel($file_id, $column);
        $score_model = D('WxyCourseComments');
        //var_dump($student_model);
        if ($data['status']) {
            foreach  ($data['data'] as $row) {
                $row['token'] = $this->token;
                $row['courseid'] = $courseid;
                $score_model->addComments($row);
            }
            return true;
        }
        else return false;
    }

    private function import_data_from_excel($file_id, $courseid = NULL, $classdate = NULL) {
        if ($courseid == NULL) return false;
        $data = array();
        $column = array (
            'A' => 'studentno',
            /*
            'B' => 'uid',
            'C' => 'token',
            'D'=>'oid',
            */
            'B'=>'score1',
            'C'=>'score2',
            'D'=>'score3',
            'E'=>'score',
            'F'=>'exmscore',
            'G'=>'comment'
        );
        $data = importFormExcel($file_id, $column);
        $score_model = D('WxyScore');
        if ($data['status']) {
            foreach  ($data['data'] as $row) {
                $row['token'] = $this->token;
                $row['courseid'] = $courseid;
                $row['classdate'] = $classdate;
                $score_model->addScore($row);
            }
            return true;
        }
        else return false;
    }


    private function dateDiff($date_1 , $date_2 , $differenceFormat = '%a' )
    {
        $datetime1 = date_create($date_1);
        $datetime2 = date_create($date_2);
        $interval = date_diff($datetime1, $datetime2);
        return $interval->format($differenceFormat);
    }
    
    
}
