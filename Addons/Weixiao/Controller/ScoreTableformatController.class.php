<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2017/5/12
 * Time: 13:18
 */


namespace Addons\Weixiao\Controller;
use Home\Controller\AddonsController;

class ScoreTableformatController extends AddonsController
{
    protected $model;
    protected $token;
    protected $school;

    public function __construct()
    {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct();
        $this->model = $this->getModel('WxyScoreTableformat'); //getModelByName ( $_REQUEST ['_controller'] );
        $this->token = get_token();
        $this->school = D('Common/Public')->getInfoByToken($this->token, 'public_name');
        $this->schooltype = D('Common/Public')->getInfoByToken($this->token, 'public_type');
        $this->public_id = D('Common/Public')->getInfoByToken($this->token, 'id');

        /*var_dump($this->model);
        var_dump($_REQUEST ['_controller']);

        exit();
        $this->model || $this->error ( '模型不存在！' );
        */

        //$this->assign('model', $this->model);
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
        $data = M($name)->field(empty ($fields) ? true : $fields)->where($map)->order('id')->page($page, $row)->select();

        foreach ($data as $key => $val) {
            if ($val['grade'] != NULL) {
                $data[$key]['grade'] = $this->zh_grade($val['grade']);
            }
        }

        /* 查询记录总数 */
        $count = M($name)->where($map)->count();

        // 分页
        if ($count > $row) {
            $page = new \Think\Page ($count, $row);
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('list_grids', $grids);
        $this->assign('list_data', $data);
        $this->assign('search_key', 'name');
        $this->meta_title = $this->model ['title'] . '列表';

        $this->display();
    }

    public function add() {        
        if (IS_POST) {
            $data['token'] = $this->token;
            $data['oid'] = $this->school;
            $data['column'] = I('post.column');
            $data['subject'] = I('post.subject');
            $data['course_name'] = I('post.course_name');
            D('WxyScoreTableformat')->add($data);
            $this->success("课程更新成功！", U('lists'));
        }
        else {
            $this->assign('public_id', $this->public_id);
            $this->display();
        }
    }

    public function edit() {
        if (IS_POST) {
            $data['token'] = $this->token;
            $data['oid'] = $this->school;
            $data['column'] = I('post.column');
            $data['subject'] = I('post.subject');
            $data['course_name'] = I('post.course_name');
            $map['id'] = I('post.id');
            D('WxyScoreTableformat')->where($map)->save($data);
            $this->success("课程更新成功！", U('lists'));
        }
        else {
            $map['id'] = I('id');
            $data = D('WxyScoreTableformat')->where($map)->find();
            $this->assign('public_id', $this->public_id);
            $this->assign('data', $data);
            $this->display('edit');
        }
    }
    
    public function score_ajax_filter(){
        if (!IS_POST) $this->error("请在表单中提交！");

        $public_id = I('public_id');
        $map ['token'] = D('Common/Public')->getinfo($public_id, 'token');
        if ($map ['token'] == NULL) {
            $sel_course = null;
            //$this->error("公众号ID错误，请输入正确的公众号ID！");
        }
        else {
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
            foreach ($course_data as $key => $vol){
                $sel_course[$key]["id"] = $vol['id'];
                $sel_course[$key]["name"] = $vol['course_name'];
                $sel_course[$key]["teacher"] = $vol['teacher'];
            }
        }
        
        //var_dump($sel_course);
        $this->ajaxReturn($sel_course,'JSON');
    }
}