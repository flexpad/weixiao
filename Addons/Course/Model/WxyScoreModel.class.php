<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2016/11/23
 * Time: 16:02
 */
namespace Addons\Course\Model;
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

    public function send_course_comment_to_user($openId, $url, $info, $token) {
        if ($info == NULL) return false;
        $map['token'] = $token;
        $map['msg_type'] = 'course comment';
        $msg_template = D('WxyWxTemplate')->where($map)->find();
        $template_id = $msg_template['msg_id'];

        $data = array(
            "first"         =>  array(
                'value' => "亲爱的" . $info["stuname"] . "家长，" . $info["stuname"] . "同学在：\n" . $info["course"] . "课程\n学习中的评价情况如下：",
                'color' => '#0000ff'
            ),
            "keyword1"    =>  array(
                'value' => $info["course"],
                'color' => '#0000ff'
            ),
            "keyword2"          =>  array (
                'value' => $info['teacher'],
                'color' => '#0000ff'
            ),
            "keyword3"         => array(
                'value' => $info['date'],
                'color' => '#0000ff'
            ),
            "remark"        =>  array(
                'value' =>"老师评语：".$info['comment'],
                'color' => '#008000'
            )
        );
        return $this->send_msg_form($openId, $template_id, $url, $data);
    }
    public function send_score_to_user($openId, $url, $info, $token, $public_name){
        if ($info == NULL) return false;
        $map['token'] = $token;
        $map['msg_type'] = 'score notification';

        $msg_template = D('WxyWxTemplate')->where($map)->find();
        $template_id = $msg_template['msg_id'];

        $test_score = ($info["exmscore"]==NULL)?'':"\n                    测试成绩：".$info["exmscore"];
        $data = array(
            "first"         =>  array(
                'value' => "亲爱的" . $info["stuname"] . "家长，" . $info["stuname"] . "同学在：\n" . $info["course"] . "课程\n学习中的成绩情况如下：",
                'color' => '#0000ff'
            ),

            "keyword1"    =>  array(
                'value' => $info["course"],
                'color' => '#0000ff'
            ),
            "keyword2"          =>  array (
                'value' => $info['classdate'],
                'color' => '#0000ff'
            ),
            "keyword3"         => array(
                'value' => $public_name,
                'color' => '#0000ff'
            ),

            "keyword4"         =>  array(
                'value' =>  "\n                    课堂表现：".$info["score1"]."\n                    作业情况：". $info["score2"]. $test_score,
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
