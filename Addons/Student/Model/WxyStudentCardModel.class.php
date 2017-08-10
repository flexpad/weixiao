<?php

namespace Addons\Student\Model;
use Think\Model;

/**
 * Student_Card模型
 */
class WxyStudentCardModel extends Model{
    protected $studentManMode;
    public function __construct() {
        if (_ACTION == 'show') {
            $GLOBALS ['is_wap'] = true;
        }
        parent::__construct ();
        $this->studentManMode = M('WxyStudentCardManage');
    }

    public function addStudent($data, $timeStamp){
        $map ['token'] = $data['token'];
        $map ['studentno'] = $data['studentno'];
        $map['name'] = $data['name'];
        //var_dump($map);
        //var_dump($this->where[$map]);

        $stuManData['name'] = $data['name'];
        $stuManData['studentno'] = $data['studentno'];
        $stuManData['cardno'] = $data['cardno'];
        $stuManData['timestamp'] = $timeStamp;
        $stuManData['pri'] = '0';
        $stuManData['group'] = '1';

        $dbData = $this->where($map)->select();

/*        var_dump('data = ', $data);
        var_dump('---- dbData = ', $dbData);*/
        if ($dbData != NULL)
        {
            if(($data['cardno'] != NULL) &&
                ($data['cardno'] != $dbData[0]['cardno']))
            {
                $this->where($map)->save($data);
                $stuManData['operation'] = ($dbData[0]['cardno']==NULL)?'add':'change';
                $this->studentManMode->delete();
                $this->studentManMode->add($stuManData);
            }
            return false;
        }
        else
        {
            $res = $this->add ( $data );
            $stuManData['operation'] = 'add';
            $this->studentManMode->delete();
            $this->studentManMode->add($stuManData);
            return $res;   
        }           
    }
    
    public function verify($map) {
        $data = $this->where($map)->find();
        if ($data != NULL) {
            return $data;
        }
        else
            return false;
    }
}
