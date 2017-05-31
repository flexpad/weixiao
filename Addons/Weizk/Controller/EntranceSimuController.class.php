<?php

namespace Addons\Weizk\Controller;
use Home\Controller\AddonsController;

class EntranceSimuController extends AddonsController
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

}