<?php

namespace Addons\Weizk\Model;
use Think\Model;

/**
* Weizk模型
*/
class ZkClientModel extends Model{

    public function approve($client, $user, $token){

        $data['phone'] = string($client['phone']);
        $data['name'] = $client['name'];
        $data['c_school'] = $client['c_school'];
        $data['grand_year'] = $client['grand_year'];
        $data['name'] = $client['name'];
        $data['uid'] = $user['uid'];
        $data['openid'] = $user['openid'];
        $data['token'] = $token;

        $map ['token'] = $data['token'];
        $map ['name'] = $data['name'];
        $map ['uid'] = $data['uid'];
        $map ['openid'] = $data['openid'];
        $vl = $this->where($map)->select();
        $num = count($vl);
        if($num == 1){
            $this->where($map)->save($data);
            return true;
        }
        else if($num == 0){
            $this->add ($data);
            return true;
        }
        else
        {
            return false;
        }
    }

}