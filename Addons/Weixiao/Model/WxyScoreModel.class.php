<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2016/11/23
 * Time: 16:02
 */
namespace Addons\Weixiao\Model;
use Think\Model;
/**
 * Score模型
 */
class WxyScoreModel extends Model{
    public function addScore($data){
        $map ['token'] = $data['token'];
        $map ['courseid'] = $data['courseid'];
        $map ['studentno'] = $data['studentno'];
        $map ['classdate'] = $data['classdate'];
        //var_dump($data);
        //var_dump($this->where[$map]);
        if ($this->where($map)->select() != NULL) {
            return false;
        } else {
            $res = $this->add ( $data );
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

    public function send_score_to_user($openId, $url, $info){
        if ($info == NULL) return false;

        $template_id = "4yl4CcKuTIVJrSObYB1SsP9uakWRnzzfpVXq7TANV3o";
        $data = array(
            "first"         =>  array(
                                'value' => "亲爱的" . $info["stuname"] . "家长," . $info["stuname"] . "同学的在" . $info["exam"] . "考试中取得以下成绩：",
                                'color' => '#0000ff'
                                ),
            "childName"     =>  array(
                                'value' =>  $info["stuname"],
                                'color' => '#0000ff'
                                ),
            "courseName"    =>  array(
                                'value' => $info["course_name"],
                                'color' => '#0000ff'
                                ),
            "time"          =>  array (
                                'value' => $info['classdate'],
                                'color' => '#0000ff'
                                ),
            "score"         =>  array(
                                'value' =>  $info["socreStr"],
                                'color' => '#0000ff'
                                ),
            "remark"        =>  array(
                                'value' =>"老师评语：".$info['comment'],
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
