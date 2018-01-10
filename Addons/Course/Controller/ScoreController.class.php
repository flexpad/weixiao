<?php

namespace Addons\Course\Controller;
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

        $key_datetime = array("type"=>"datetime","title"=>"有效时间","start_time"=>"","end_time"=>date("Y-m-d"));
        //$key_datetime = array("type"=>"datetime","title"=>"有效时间");
        //$key_course = array("type"=>"select","title"=>"科目","name"=>"subject","options"=>array(array("value"=>1,"name"=>"语文","title"=>"语文"),array("value"=>2,"name"=>"数学","title"=>"数学")));
        //$key_course = array("type"=>"input","title"=>"科目","name"=>"subject");
        $key_studentno = array("type"=>"input","title"=>"学号","name"=>"studentno");
        $muti_keys = array($key_datetime, $key_studentno,NULL);
        $this->assign('muti_search',$muti_keys);
        $this->assign('search_key',array('classdate','studentno'));
        $this->meta_title = $this->model ['title'] . '列表';

        $this->display('lists');
    }

    // Send a Weixin template message to use to notify the score:

    public function send()
    {
        $score_id = I('id');
        $map['id'] = $score_id;
        $model = D('WxyScoreNotifyView');
        $score_data = $model->where($map)->select();
        /*var_dump($model->getLastSql());
        var_dump($score_data);
        */
        $statue = 0;
        foreach ($score_data as $value) {
            $url = U('addon/Student/Wap/score', array('publicid'=>$this->public_id, 'studentno' => $value['studentno']));
            //var_dump($value);
            $retdata = D('WxyScore')->send_score_to_user($value['openid'], $url, $value, $this->token, $this->school);

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

}
