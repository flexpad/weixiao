<?php

namespace Addons\DailyTime\Model;
use Think\Model;

/**
 * DailyTime模型
 */
class WxyDailyTimeModel extends Model{
    public function add_attendance($data){
        $res = $this->add ( $data );
        return $res;
    }


    public function send_score_to_user($openId, $url, $info){
        if ($info == NULL) return false;

        $template_id = "4yl4CcKuTIVJrSObYB1SsP9uakWRnzzfpVXq7TANV3o";
        $data = array(
            "first"         =>  array(
                'value' => "您好," . $info["name"] . ",您参加考试的会计认证课程成绩已出。",
                'color' => '#0000ff'
            ),
            "keyword1"     =>  array(
                'value' =>  $info["subject"],
                'color' => '#0000ff'
            ),
            "keyword2"    =>  array(
                'value' => $info["examTime"],
                'color' => '#0000ff'
            ),
            "keyword3"          =>  array (
                'value' => $info['examPlace'],
                'color' => '#0000ff'
            ),
            "keyword4"         =>  array(
                'value' =>  $info["socreStr"],
                'color' => '#0000ff'
            ),
            "remark"        =>  array(
                'value' =>"感谢您对微信学校的支持。",
                'color' => '#008000'
            )
        );
        return $this->send_msg_form($openId, $template_id, $url, $data);
    }

    public function send_attend_to_user($openId, $url, $info){
        if ($info == NULL) return false;

        $template_id = "YjbqCbQ8b3CPr0AhYufWQqkko3URK9SdIpymKxaNlfI";
        $data = array(
            "first"         =>  array(
                'value' => $info["name"] . "的家长您好，".$info["name"] ."已经签到",
                'color' => '#0000ff'
            ),
            "keyword1"     =>  array(
                'value' =>  $info["name"],
                'color' => '#0000ff'
            ),
            "keyword2"    =>  array(
                'value' => $info["stuId"],
                'color' => '#0000ff'
            ),
            "keyword3"          =>  array (
                'value' => $info['attendTime'],
                'color' => '#0000ff'
            ),
            "keyword4"         =>  array(
                'value' =>  $info["attendState"],
                'color' => '#0000ff'
            ),
            "remark"        =>  array(
                'value' =>"感谢您的支持。",
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
