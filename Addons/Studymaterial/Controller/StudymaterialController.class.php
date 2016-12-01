<?php

namespace Addons\Studymaterial\Controller;
use Home\Controller\AddonsController;

class StudymaterialController extends AddonsController{
    protected $model;
    protected $token;
    protected $school;
    public function __construct() {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct ();
        $this->model = $this->getModel('WxyStudyMaterial'); //getModelByName ( $_REQUEST ['_controller'] );
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
        //$this->assign('token',$this->token);
        if (IS_POST) {
            $data['stage'] = I('post.stage');
            $data['subject'] = I('post.subject');
            $data['fileid'] = I('post.file');
            $data['title'] = I('post.title');
            $data['description'] = I('post.description');
            $data['token'] = $this->token;
            $model = D('WxyStudyMaterial');
            $map['fileid'] = $data['fileid'];
            if ($model->where($map)->select()) {
                $this->error("文件已经存在，请重新上传文件。");
            }
            else
                if ($data['fileid'] != 0) {
                    $model->add($data);
                    $this->success("资料提交成功，刷新后可继续提交");
                }
                else
                    $this->error("文件上传错误，请成功上传文件后再提交表单");

        }
        else {
            $this->display();
        }

    }

    public function send () {
        if (1) {
            $map['id'] = I('id');
            //var_dump($fileid);
            require_once(VENDOR_PATH . '/phpmailer/MailModel.class.php');
            $mail = new \MailModel ();
            $mail->option = array(
                'email_sendtype' => C('email_sendtype'),
                'email_host' => C('email_host'),
                'email_port' => C('email_port'),
                'email_ssl' => C('email_ssl'),
                'email_account' => C('email_account'),
                'email_password' => C('email_password'),
                'email_sender_name' => C('email_sender_name'),
                'email_sender_email' => C('email_sender_email'),
                'email_reply_account' => C('email_sender_email')
            );

            $mail->option['email_sender_name'] = $this->school;
            //var_dump($mail->option);
            $material = D('WxyStudyMaterial')->where($map)->find();
            $mail_list = D('WxyStudyOrder')->order_list($material['stage']);
            $download = C('DOWNLOAD_UPLOAD');
            $file_root = $download['rootPath'];
            //var_dump($mail_list);
            $count = 0;
            $count_ok = 0;
            foreach ($mail_list as $vo) {
                $sendto_email = $vo['email'];
                $subject = $material['title'];
                $body = $material['description'];
                $mapfile['id'] = $material['fileid'];
                $file = M('file')->where($mapfile)->find();
                if ($file != NULL) {
                    $attachment = $file_root . $file['savepath'] . $file['savename'];
                    $extension_name = substr(strrchr($file['savename'], '.'), 1);
                    $attach_name = $material['title'] . '.' . $extension_name;
                    $result = $mail->send_email($sendto_email, $subject, $body, '', $attachment, $attach_name);
                    $count++;
                    if ($result) $count_ok++;

                    //var_dump($result);

                }
            }
            $this->success("邮件总共发送". strval($count)."封，其中成功发送".strval($count_ok)."封。");
        }
    }
}
