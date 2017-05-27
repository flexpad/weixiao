<?php

namespace Addons\Weizk\Controller;
use Home\Controller\AddonsController;

class HschoolPerformanceController extends AddonsController{
    protected $model;
    protected $tableName = 'zk_hschool_performance';

    public function __construct() {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct ();
        $this->model = $this->getModel('ZkHschoolPerformance');
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

    public function import(){

        if ($this->uid == 0) redirect(U('/Home/Public'));
        if (IS_POST) {
            $data['file'] = I('post.file');
            $item_year = I('post.reportdate');
            $data['comment'] = I('post.comment');
            if ($item_year == NULL ) $this->error('请务必输入此次导入数据对应的年号！');
            $import_model = D('ZkFileImport');
            $data['uid'] = $this->uid;
            $data['type'] = "HschoolPerformance";
            $data['timestamp'] = date('y/m/d h:i:sa');
            //$res = $import_model->addImport($data);
            if($this->import_performance_from_exl($data['file'],$item_year)){
                //$this->success('保存成功！', U ( 'lists'), 30);
            }
            else{
                //$this->error('请检查文件格式');
            }
        }
        else{
            $this->display('import');
        }
    }

    private function import_performance_from_exl($file_id,$item_year) {
        $column = array (
            'A' => 'school_name',     //学校名称
            'L'=>'count_1',     //分数范围内的人数
            'M'=>'ratio_1',   //L分数下人数的比例
            'N'=>'count_2',
            'O'=>'ratio_2',   //O分数下人数的比例
            'P'=>'count_3',
            'Q'=>'ratio_3',   //P分数下人数的比例
            'R'=>'count_4',
            'S'=>'ratio_4',   //R分数下人数的比例
            'T'=>'count_5',
            'U'=>'ratio_5',   //T分数下人数的比例
            'V'=>'count_6',
            'W'=>'ratio_6',   //V分数下人数的比例
            'X'=>'count_7',
            'Y'=>'ratio_7',   //X分数下人数的比例
        );

        $data = importFormExcel($file_id, $column);
        $item_format['high_1'] = 1000;
        $performance_model = D('zk_hschool_performance');
        if ($data['status']) {
            foreach  ($data['data'] as $key=>$row) {
                if($key < 4) {
                    continue;
                }
                else if($key == 4){
                    $item_format['low_1'] = intval(substr($row['count_1'],0,3));
                    $item_format['high_2'] = intval(explode('-',$row['count_2'])[1]);
                    $item_format['low_2'] = intval(explode('-',$row['count_2'])[0]);
                    $item_format['high_3'] = intval(explode('-',$row['count_3'])[1]);
                    $item_format['low_3'] = intval(explode('-',$row['count_3'])[0]);
                    $item_format['high_4'] = intval(explode('-',$row['count_4'])[1]);
                    $item_format['low_4'] = intval(explode('-',$row['count_4'])[0]);
                    $item_format['high_5'] = intval(explode('-',$row['count_5'])[1]);
                    $item_format['low_5'] = intval(explode('-',$row['count_5'])[0]);
                    $item_format['high_6'] = intval(explode('-',$row['count_6'])[1]);
                    $item_format['low_6'] = intval(explode('-',$row['count_6'])[0]);
                    $item_format['high_7'] = intval(substr($row['count_7'],0,3));
                    $item_format['low_7'] = 0;
                }
                else {
                    for($x = 1; $x<8; $x++){
                        $item['hschool_name'] = $row['school_name'];
                        $item['score_seg_low']  = $item_format['low_'.$x];
                        $item['score_seg_high']  = $item_format['high_'.$x];
                        $item['count'] = $row['count_'.$x];
                        $item['ratio_1'] = floatval($row['ratio_'.$x]);
                        $item['year'] = $item_year;
                        //var_dump($item);
                        $performance_model->addPerformance($item);
                    }
                    /*
                    if( $key == 7)
                        break;
                    */
                }

            }
            return true;
        }
        else return false;
    }
}