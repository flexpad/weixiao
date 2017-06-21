<?php

namespace Addons\Weizk\Model;
use Think\Model;

/**
* Weizk模型
*/
class ZkClientModel extends Model{

    public function approve($client, $user, $token){

        $data['phone'] = ($client['phone']);
        $data['name'] = $client['name'];
        $data['c_school'] = $client['c_school'];
        $data['grand_year'] = $client['grand_year'];
        $data['name'] = $client['name'];
        $data['uid'] = $user['uid'];
        $data['openid'] = $user['openid'];
        $data['token'] = $token;
        $data['classtype'] = $client['class_type'];

        $map ['token'] = $data['token'];
        $map ['uid'] = $data['uid'];
        $map ['openid'] = $data['openid'];
        $vl = $this->where($map)->select();
        $num = count($vl);
        if($vl != NULL){
            foreach ($vl as $item){
                if ($item['name'] === $data['name']){
                    $map['id'] = $item['id'];
                    $this->where($item)->save($data);
                    return ture;
                }
            }
            if($num < 3) {
                $this->add($data);
                return true;
            }
            else {
                return false;
            }

        }
        else {
            $this->add ($data);
            return true;
        }

    }

}