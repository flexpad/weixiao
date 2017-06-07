<?php

namespace Addons\Weizk\Controller;
use Home\Controller\AddonsController;

class HschoolController extends AddonsController{
    protected $model;
    protected $token;
    protected $tableName = 'zk_hschool';

    public function __construct() {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct ();
        $this->model = $this->getModel('zk_hschool');
        $this->token = get_token();
    }

    /**
     * 显示指定模型列表数据
     */
    public function lists()
    {
        $page = I('p', 1, 'intval'); // 默认显示第一页数据


        // 解析列表规则
        $list_data = $this->_get_model_list($this->model);

        $grids = $list_data ['list_grids'];
        $fields = $list_data ['fields'];

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

        /* 查询记录总数 */
        $count = M($name)->where($map)->count();

        if ($count > $row) {
            $page = new \Think\Page ($count, $row);
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('list_grids', $grids);
        $this->assign('list_data', $data);


        $this->display();
    }

    public function add(){
        $this->display('import');
    }
/*
    public function edit() {
        //var_dump($this);
        $model = M('zk_hschool');

        if (!IS_POST) {
            $map['id'] = I('id');
            $data = $model->where($map)->find();
            $this->assign('id', $map['id']);
            $this->assign('data', $data);
            $this->display();
        }
        else {
            $data['name'] = I('post.name');
            $data['level'] = I('post.level');
            $data['address'] = I('post.address');
            $data['website'] = I('post.website');
            $data['rank_int'] = I('post.rank_int');
            $data['rank_literary'] = I('post.rank_literary');
            $data['rank_science'] = I('post.rank_science');
            $data['factor_bk1'] = I('post.factor_bk1');
            $data['factor_bk2'] = I('post.factor_bk2');
            $data['factor_growth'] = I('post.factor_growth');
            $data['factor_asc'] = I('post.factor_asc');
            $data['intro'] = I('post.intro');

            $map['id'] = I('post.id');
            $map['token'] = $this->token;
            $model->where($map)->save($data);
            $this->success("高中信息更新成功！", U('lists'));
            //To do here.
        }
    }
*/
    public function import(){
        $id = I('id');
        $model = M('zk_hschool');

        if (IS_POST) {
            var_dump('import post!\n');
            $data['file'] = I('post.file');
            $item_year = I('post.reportdate');
            $data['comment'] = I('post.comment');
            if ($item_year == NULL ) $this->error('请务必输入此次导入数据对应的年号！');
            $import_model = D('ZkFileImport');
            $data['uid'] = $this->uid;
            $data['type'] = "Hschool";
            $data['timestamp'] = date('y/m/d h:i:sa');

            if($this->import_hschool_from_excel($data['file'], $item_year)){
                $this->success('保存成功！', U ( 'lists'), 30);
            }
            else{
                $this->error('请检查文件格式');
            }
        }
        else {
            if ($id) $map['id'] = $id;
            $map['token'] = $this->token;
            $data = $model->where($map)->select();
            $this->assign('lists', $data);
            $this->display('import');
        }
    }

    private function import_hschool_from_excel($file_id, $item_year) {
        $column = array (
            'A' => 'name',     //学校名称
            'G'=>'address',     //学校地址
            'I'=>'website',   //官网网址
            'P'=>'factor_growth', //成才指数
            'Q'=>'factor_bk1',  //一本率
            'R'=>'factor_bk2',   //二本率
        );

        $data = importFormExcel($file_id, $column);
        $hschool_model = D('zk_hschool');
        if ($data['status']) {
            foreach  ($data['data'] as $row) {
                $row['token'] = $this->token;
                //$row['factor_bk1'] = round(floatval($row['factor_bk1']), 1);
                //$row['factor_bk2'] = round(floatval($row['factor_bk2']), 1);
                //var_dump($row);
                $hschool_model->addHschool($row);
            }
            return true;
        }
        else
            return false;

    }
}