<?php

namespace Addons\Weizk\Controller;
use Addons\Weizk\Controller\BaseController;

class EntranceSimuController extends BaseController
{
    private $token;

    public function __construct()
    {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct();
        $this->model = $this->getModel('ZkEntranceSimu');
        $this->token = get_token();
    }
    public function lists()
    {
        parent::lists($this->model);
    }

    public function updateForm()
    {

    }
}