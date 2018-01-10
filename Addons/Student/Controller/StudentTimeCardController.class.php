<?php

namespace Addons\Student\Controller;
use Home\Controller\AddonsController;

class StudentTimeCardController extends AddonsController{
    protected $model;
    protected $token;
    protected $school;
    public function __construct() {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct ();
        $this->model = $this->getModel('WxyStudentTimeCard'); //getModelByName ( $_REQUEST ['_controller'] );
        $this->token = get_token();
        $this->school = D('Common/Public')->getInfoByToken($this->token, 'public_name');

        /*var_dump($this->model);
        var_dump($_REQUEST ['_controller']);

        exit();
        $this->model || $this->error ( '模型不存在！' );

        $this->assign ( 'model', $this->model );
        */

    }

    /**Student
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
        $key = $this->model ['search_key'] ? $this->model ['search_key'] : 'studentno';
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

    public function edit() {
        //var_dump($sid);
        $model = D('WxyStudentTimeCard');

        if (IS_POST) {
            //$data = I('post.data');
            $sid = I('post.id');


            $data['studentno'] = I('post.studentno');
            $data['name'] = I('post.name');
            $data['cardno'] = I('post.cardno');
            $data['serial_no'] = I('post.serial_no');
            $data['updatetime'] = I('post.updatetime');
            /*var_dump($data);
            var_dump(I('post.updatetime'));
            */
            $map['id'] = $sid;
            $map['token'] = $this->token;
            $model->where($map)->save($data);
            $this->success("学生资料更新成功！");
        }
        else {
            if (I('id') == NULL) $this->error("学生ID未输入！");
            $sid  = intval(I('id'));
            $map['id'] = $sid;
            $data = $model->where($map)->find();
            $this->assign('data', $data);
            //var_dump($data);
            $this->display('edit');
        }
    }

    public function rfid_import(){

        //var_dump($this->uid);
        //var_dump($this->mid);
        //U('edit', array('id'=>I('request.id')));
        $token = $this->token;
        //$file_id = 7;
        //$data = $this->import_student_data_from_excel($file_id);

        if (IS_POST) {
            $password = I('post.password');

            if (strcmp($password,"wx@123456") !=0 ) {
                $this->error('输入口令有误！');
            }
            $data['token'] = $token;
            $data['file'] = I('post.file');
            $data['date'] = date('Y-m-d');
            $data['comment'] = I('post.comment');

            if (!intval($data['file'])) $this->error("数据文件未上传！");
            $import_model = D('WxyStudentimport');
            $import_model->addImport($data);
            if ($this->import_rfid_data_from_excel($data['file'])) //import student data from uploaded Excel file.
                $this->success('保存成功！', U ( 'lists'/*'import?model=' . $this->model ['name'], $this->get_param */), 600);
            else
                $this->error('请检查文件格式');
        }
        else {
            $this->display('rfid_import');
        }
    }

    public function import(){

        //var_dump($this->uid);
        //var_dump($this->mid);
        //U('edit', array('id'=>I('request.id')));
        $token = $this->token;
        //$file_id = 7;
        //$data = $this->import_student_data_from_excel($file_id);

        if (IS_POST) {
            $data['token'] = $token;
            $data['file'] = I('post.file');
            $data['date'] = date('Y-m-d');
            $data['comment'] = I('post.comment');

            if (!intval($data['file'])) $this->error("数据文件未上传！");
            $import_model = D('WxyStudentimport');
            $import_model->addImport($data);
            if ($this->import_student_data_from_excel($data['file'])) //import student data from uploaded Excel file.
                $this->success('保存成功！', U ( 'lists'/*'import?model=' . $this->model ['name'], $this->get_param */), 600);
            else
                $this->error('请检查文件格式');
        }
        else {
            $this->display('import');
        }
    }
    
    private function import_student_data_from_excel($file_id) {
        $data = array();
        $column = array (
            'A' => 'name',
            'B' => 'studentno',
            'C' => 'cardno',        //RFID考号
            'D' => 'serial_no'      //卡正面序列号
        );

        $data = importFormExcel($file_id, $column);

        $timeStamp = time();
        $stuTimeCardmodel = D('WxyStudentTimeCard');

        if ($data['status'])
        {
            //$stuTimeCardmodel->where('1')->delete();
            foreach  ($data['data'] as $row) {
                $row['token'] = $this->token;
                $row['updatetime'] = $timeStamp;
                $stuTimeCardmodel->addStudentTimeCard($row);
            }
            return true;
        }
        else return false;
    }

    private function import_rfid_data_from_excel($file_id) {
        $data = array();
        $column = array (
            'A' => 'rfid_no',    //RFID号码
            'B' => 'serial_no' //序列号
        );

        $data = importFormExcel($file_id, $column);

        $timeStamp = time();
        $stuTimeCardmodel = D('WxyRfidData');

        if ($data['status'])
        {
            //$stuTimeCardmodel->where('1')->delete();
            foreach  ($data['data'] as $row) {
                $row['token'] = $this->token;
                $row['updatetime'] = $timeStamp;
                $stuTimeCardmodel->addRfidCard($row);
            }
            return true;
        }
        else return false;
    }
}
