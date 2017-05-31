<?php

namespace Addons\Weizk\Controller;
use Home\Controller\AddonsController;

class ExamRecordController extends AddonsController
{
    private $token;

    public function __construct()
    {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct();
        $this->model = $this->getModel('ZkExamRecord');
        $this->token = get_token();
    }
    public function lists()
    {
        parent::lists($this->model);
    }
}