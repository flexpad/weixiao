<?php

namespace Addons\Course\Model;
use Think\Model;

/**
 * Course模型
 */
class WxyCourseModel extends Model{

    public function send_course_notification_to_user($openId, $url, $info, $token) {
        if ($info == NULL) return false;
        $map['token'] = $token;
        $map['msg_type'] = 'course notify';
        $msg_template = D('WxyWxTemplate')->where($map)->find();
        $template_id = $msg_template['msg_id'];

        $data = array(
            "first"     =>  array(
                'value' => $info['first_data'],
                'color' => '#0000ff'
            ),
            "keyword1"  =>  array(
                'value' => $info['keyword1_data'],
                'color' => '#0000ff'
            ),
            "keyword2"  =>  array (
                'value' => $info['keyword2_data'],
                'color' => '#0000ff'
            ),
            "remark"    =>  array(
                'value' => $info['remark_data'],
                'color' => '#008000'
            )
        );
        return $this->send_msg_form($openId, $template_id, $url, $data);
    }

    private function http_post($url, $param) {
        $oCurl = curl_init ();
        if (stripos ( $url, "https://" ) !== FALSE) {
            curl_setopt ( $oCurl, CURLOPT_SSL_VERIFYPEER, FALSE );
            curl_setopt ( $oCurl, CURLOPT_SSL_VERIFYHOST, false );
        }
        if (is_string ( $param )) {
            $strPOST = $param;
        } else {
            $aPOST = array ();
            foreach ( $param as $key => $val ) {
                $aPOST [] = $key . "=" . urlencode ( $val );
            }
            $strPOST = join ( "&", $aPOST );
        }
        curl_setopt ( $oCurl, CURLOPT_URL, $url );
        curl_setopt ( $oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $oCurl, CURLOPT_POST, true );
        curl_setopt ( $oCurl, CURLOPT_POSTFIELDS, $strPOST );
        $sContent = curl_exec ( $oCurl );
        $aStatus = curl_getinfo ( $oCurl );
        /*echo '<p></p>';
        var_dump($sContent);
        echo '<p></p>';
        var_dump($aStatus);
        echo '<p></p>';
        var_dump($strPOST);
        echo '<p></p>';*/
        curl_close ( $oCurl );
        if (intval ( $aStatus ["http_code"] ) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }
    public function send_msg_form($openid,$template_id,$url,$data){
        $postData = array(
            "touser"=>$openid,
            "template_id"=>$template_id,
            "url"=>$url,
            "topcolor" => "#7B68EE",
            "data"=>$data);
        $acc_token = get_access_token();
        /*echo "<p> the result is: </p>";
        var_dump($postData);
        echo "<p> the result is: </p>";*/
        $retData = false;
        $retData = $this->http_post("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$acc_token,json_encode($postData));
        /*echo "<p> the result is: </p>";
        var_dump($retData);*/

        if ($retData == false){
            addWeixinLog ( "sendMsgForm Error: send message time out");
            return NULL;
        }else {
            return $retData;
        }
    }
}
