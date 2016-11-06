<?php

namespace Addons\Student\Controller;
use Home\Controller\AddonsController;

class StudentController extends AddonsController{
    function add(){
        $this->display('edit');
    }
}
