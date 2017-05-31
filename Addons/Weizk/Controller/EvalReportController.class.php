<?php

namespace Addons\Weizk\Controller;
use Home\Controller\AddonsController;

class EvalReportController extends AddonsController
{
    private $token;

    public function __construct()
    {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }

        parent::__construct();
        $this->model = $this->getModel('ZkEvalReport');
        $this->token = get_token();
    }

}