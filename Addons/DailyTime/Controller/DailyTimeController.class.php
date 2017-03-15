<?php

namespace Addons\DailyTime\Controller;
use Home\Controller\AddonsController;

class DailyTimeController extends AddonsController
{
    protected $model;
    protected $fileImportModel;
    protected $token;
    public function __construct() {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct ();
        $this->model = $this->getModel('WxyDailyTime'); //getModelByName ( $_REQUEST ['_controller'] );
        $this->fileImportModel = $this->getModel('WxyAttendanceimport');
        $this->token = get_token();
        //var_dump($this->token);
        /*var_dump($this->model);
        var_dump($_REQUEST ['_controller']);

        exit();
        $this->model || $this->error ( '模型不存在！' );

        $this->assign ( 'model', $this->model );
        */

    }

    public function del()
    {
        $Model = $this->model;
        $ids = I('ids');
        parent::del($Model,$ids);
    }
    public function add()
    {
        $Model = $this->model;
        parent::add($Model);
    }

    public function edit()
    {
        $data = NULL;

        if (IS_POST) {
            $Model = $this->model;
            $data = I('post.data');
            parent::edit($Model,$data);
        }
        else {
            if (I('id') == NULL) $this->error("学生ID未输入！");
            $attend_id = intval(I('id'));

            $map['id'] = $attend_id;
            $attend_Model = D('WxyDailyTime');
            $data = $attend_Model->where($map)->find();
        }
        $stu_model = D('WxyStudentCard');
        $stu_map['studentno'] = $data['studentID'];
        $stu_data = $stu_model->where($stu_map)->find();

        if ($data['arriveTime'] != 0)
            $data['arriveTime'] = date('Y-m-d H:i:s', $data['arriveTime']);
        else
            $data['arriveTime'] = '-------------';
        if ($data['leaveTime'] != 0)
            $data['leaveTime'] = date('Y-m-d H:i:s', $data['leaveTime']);
        else
            $data['leaveTime'] = '-------------';

        $this->assign('student_name', $stu_data['name']);
        $this->assign('data', $data);

        $this->display('edit');
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

        //var_dump($data);
        /* 查询记录总数 */
        $count = M('WxyDailyTime')->where($map)->count();
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
        /*
        for($x = 0; $x < $count && $x < $row; $x++)
        {
            if($data[$x]['arriveTime'] != '0')
            {
                $data[$x]['arriveTime'] = date('Y-m-d H:i:s',$data[$x]['arriveTime']);
            }
            else{
                $data[$x]['arriveTime'] = '-------------';
            }
            if( $data[$x]['leaveTime'] != '0')
            {
                $data[$x]['leaveTime'] = date('Y-m-d H:i:s',$data[$x]['leaveTime']);
            }
            else{
                $data[$x]['leaveTime'] = '-------------';
            }

            $data[$x]['state'] = $state[(int)$data[$x]['state']];
        }
        */
        //var_dump($data);
        //var_dump($list_data);
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

        $this->display('list');
    }
    private function display_import_file_list()
    {
        $page = I('p', 1, 'intval'); // 默认显示第一页数据

        // 解析列表规则
        $list_data = $this->_get_model_list($this->fileImportModel);//_list_grid($this->model);
        $grids = $list_data ['list_grids'];
        $fields = $list_data ['fields'];

        // 关键字搜索
        $map ['token'] = get_token();
        $key = $this->fileImportModel ['search_key'] ? $this->fileImportModel ['search_key'] : 'title';
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

        $row = empty ($this->fileImportModel ['list_row']) ? 20 : $this->fileImportModel ['list_row'];

        // 读取模型数据列表

        empty ($fields) || in_array('id', $fields) || array_push($fields, 'id');
        $name = parse_name(get_table_name($this->fileImportModel ['id']), true);
        //var_dump($name);
        //exit();
        $data = M($name)->field(empty ($fields) ? true : $fields)->where($map)->order('id')->page($page, $row)->select();

        /* 查询记录总数 */
        $count = M('WxyAttendanceimport')->where($map)->count();
        var_dump($count);
        if ($count > $row) {
            $page = new \Think\Page ($count, $row);
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('list_grids', $grids);
        $this->assign('list_data', $data);
        $this->meta_title = $this->fileImportModel['title'] . '列表';
        $this->display('import');
    }
    public function import(){
        $uid = $this->uid;
        $token = $this->token;
        $list_data = $this->_get_model_list($this->fileImportModel);//_list_grid($this->model);
        $grids = $list_data ['list_grids'];
        $fields = $list_data ['fields'];

        //$file_id = 7;
        //$data = $this->import_data_from_excel($file_id);

        if (IS_POST) {
            //$data['uid'] = $uid;
            //$data['token'] = $token;
            $data['importFile'] = I('post.file');
            $data['importTime'] = date('Y-m-d h:i:sa');
            //data['comment'] = I('post.comment');

            $fileImport = D('WxyAttendanceimport');
            $fileImport->add($data);
            if ($this->import_data_from_excel($data['importFile'])) //import student data from uploaded Excel file.
                $this->success('保存成功！', U ( 'lists'/*'import?model=' . $this->model ['name'], $this->get_param */), 600);
            else
                $this->error('请检查文件格式');
        }
        else {
            $this->display_import_file_list();
            //$this->display('import');
        }
    }

    private function import_data_from_excel($file_id) {
        $data = array();
        $column = array (
            'A' => 'studentID',
            'B'=>'arriveTime',
            'C'=>'leaveTime',
            'D'=>'state',
            'E' =>'description',
        );
        $data = importFormExcel($file_id, $column);
        //var_dump($data);
        $attendance_model = D('WxyDailyTime');
        //var_dump($student_model);
        if ($data['status']) {
            foreach  ($data['data'] as $row) {
                $row['Token'] = $this->token;
                $row['studentID'] = strval($row['studentID']);

                if($row['arriveTime'] != '')
                {
                    $row['arriveTime'] = strtotime($row['arriveTime']);
                }
                if($row['leaveTime'] != '')
                {
                    $row['leaveTime'] = strtotime($row['leaveTime']);
                }

                //var_dump($row);
                $attendance_model->add_attendance($row);
            }
            return true;
        }
        else return false;
    }
}