<?php

namespace Addons\Student\Controller;
use Home\Controller\AddonsController;

class StudentController extends AddonsController{
    protected $model;
    protected $token;
    protected $school;
    public function __construct() {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct ();
        $this->model = $this->getModel('WxyStudentCard'); //getModelByName ( $_REQUEST ['_controller'] );
        $this->token = get_token();
        $this->school = D('Common/Public')->getInfoByToken($this->token, 'public_name');

        /*var_dump($this->model);
        var_dump($_REQUEST ['_controller']);

        exit();
        $this->model || $this->error ( '模型不存在！' );

        $this->assign ( 'model', $this->model );
        */

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
    /*
    public function edit(){

        $this->display();
    }
    */
    public function add(){
        $this->display('add');
    }

    public function edit() {
        if (I('id') == NULL) $this->error("学生ID未输入！");
        $sid  = intval(I('id'));
        //var_dump($sid);

        $model = D('WxyStudentCard');
        $map['id'] = $sid;
        $data = $model->where($map)->find();
        /*
        $grade = ['','一年级', '二年级', '三年级', '四年级', '五年级', '六年级', '七年级', '八年级', '九年级', '高一', '高二', '高三'];
        $gender = ['','女生','男生'];
        $data['gender'] = $gender[intval($data['gender'])];
        $data['grade'] = $grade[intval($data['grade'])];
        */
        $this->assign('data', $data);
        //var_dump($data);
        $this->display('edit');
    }

    public function import(){

        //var_dump($this->uid);
        //var_dump($this->mid);
        $uid = $this->uid;
        $token = $this->token;
        //$file_id = 7;
        //$data = $this->import_data_from_excel($file_id);
        if ($uid == 0) redirect(U('/Home/Public'));
        if (IS_POST) {
            $data['uid'] = $uid;
            $data['token'] = $token;
            $data['file'] = I('post.file');
            $data['date'] = date('Y-m-d h:i:sa');
            $data['comment'] = I('post.comment');
            if (!intval($data['file'])) $this->error("数据文件未上传！");
            $import_model = D('WxyStudentimport');
            $import_model->addImport($data);
            if ($this->import_data_from_excel($data['file'])) //import student data from uploaded Excel file.
                $this->success('保存成功！', U ( 'lists'/*'import?model=' . $this->model ['name'], $this->get_param */), 600);
            else
                $this->error('请检查文件格式');
        }
        else {
            $this->display('import');
        }
    }
    
    private function import_data_from_excel($file_id) {
        $data = array();
        $column = array (
            'A' => 'sid',
            /*
            'B' => 'uid',
            'C' => 'token',
            'D'=>'oid',
            */
            'E'=>'name',
            'F'=>'gender',
            'G'=>'school',
            'H'=>'grade',
            'I'=>'studentno',
            'J'=>'phone'
        );
        $data = importFormExcel($file_id, $column);
        //var_dump($data);
        //exit();
        $student_model = D('WxyStudentCard');
        //var_dump($student_model);
        if ($data['status']) {
            foreach  ($data['data'] as $row) {
                $row['token'] = $this->token;
                $row['uid'] = $this->uid;
                $row['phone'] = strval($row['phone']);
                $row['gender'] = ($row['gender'] == '男') ? 1 : 0;
                if ($row['gender'] == '女') $row['gender'] = 2;

                $student_model->addStudent($row);
            }
            return true;
        }
        else return false;
    }
}
