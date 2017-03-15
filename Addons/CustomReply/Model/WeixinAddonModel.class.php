<?php

namespace Addons\CustomReply\Model;

use Home\Model\WeixinModel;

/**
 * CustomReply的微信模型
 */
class WeixinAddonModel extends WeixinModel {
	function reply($dataArr, $keywordArr = array()) {
		$map ['id'] = $keywordArr ['aim_id'];
		$param ['token'] = get_token ();
		$param ['openid'] = get_openid ();
		//addWeixinLog('keywordArr输出值：',$keywordArr);
		
		if($dataArr['Content']=='location'){
			$latitude = $dataArr ['Location_X'];
			$longitude = $dataArr ['Location_Y'];
			$pos = file_get_contents ( 'http://lbs.juhe.cn/api/getaddressbylngb?lngx=' . $latitude . '&lngy=' . $longitude );
			$pos_ar = json_decode ( $pos, true );
			$this->replyText ( htmlspecialchars_decode ( $pos_ar ['row'] ['result'] ['formatted_address'] ) );
		}elseif ($keywordArr ['extra_text'] == 'custom_reply_mult') {
			// 多图文回复
			$mult = M ( 'custom_reply_mult' )->where ( $map )->find ();
			$map_news ['id'] = array (
					'in',
					$mult ['mult_ids'] 
			);
			$list = M ( 'custom_reply_news' )->where ( $map_news )->select ();
			
			foreach ( $list as $k => $info ) {
				if ($k > 8)
					continue;
				
				$articles [] = array (
						'Title' => $info ['title'],
						'Description' => $info ['intro'],
						'PicUrl' => get_cover_url ( $info ['cover'] ),
						'Url' => $this->_getNewsUrl ( $info, $param ) 
				);
			}
			
			$res = $this->replyNews ( $articles );
		} elseif ($keywordArr ['extra_text'] == 'custom_reply_news') {
			// 单条图文回复
			$info = M ( 'custom_reply_news' )->where ( $map )->find ();
			
			// 组装微信需要的图文数据，格式是固定的
			$articles [0] = array (
					'Title' => $info ['title'],
					'Description' => $info ['intro'],
					'PicUrl' => get_cover_url ( $info ['cover'] ),
					'Url' => $this->_getNewsUrl ( $info, $param ) 
			);
			
			$res = $this->replyNews ( $articles );
		} else {
			// 增加积分
			add_credit ( 'custom_reply', 300 );

			//addWeixinLog('当前接收的消息ID', $map['id']);
			
			// 文本回复
			$info = M ( 'custom_reply_text' )->where ( $map )->find ();
			//addWeixinLog('当前接收消息的匹配结果', $info['content']);
			$content = $this->message_process($info, $dataArr, $keywordArr);
			//$content = replace_url ( htmlspecialchars_decode ( $info ['content'] ) );
			$this->replyText ( $content );
		}
	}
	function _getNewsUrl($info, $param) {
		if (! empty ( $info ['jump_url'] )) {
			$url = replace_url ( $info ['jump_url'] );
		} else {
			$param ['id'] = $info ['id'];
			$url = addons_url ( 'CustomReply://CustomReply/detail', $param );
		}
		return $url;
	}
	// 上报地理位置事件 感谢网友【blue7wings】和【strivi】提供的方案
	public function location($dataArr) {

	}

	private function message_process($info = NULL, $dataArr, $keywordArr) {
		$model = M('WxyStudyOrder');
		if ($info == NULL)
			return $content = '亲，你输入的消息内容系统无法识别，请检查后再输入正确的消息！';
		else {
			$keyword = rtrim(ltrim($keywordArr['keyword']));
			$map['openid'] = $dataArr['FromUserName'];
			$map['token'] = $dataArr['ToUserName'];
			switch ($keyword) {
				case '@':
					$pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
					$email = $keywordArr['prefix'].$keywordArr['keyword'].$keywordArr['suffix'];
					if ( preg_match( $pattern, $email ) ) {//Email Address OK.
						if (!$model->where($map)->select()) {
							$data['token'] = $map['token'];
							$data['openid'] = $map['openid'];
							$data['email'] = $email;
							$model->add($data);
						} else {
							$data['email'] = $email;
							$model->where($map)->field('email')->save($data);
						}
						return $content = replace_url(htmlspecialchars_decode($info ['content']));
					}
					else
						return $content = '亲，你输入电子邮箱地址不正确，请查正后再继续输入！';
					break;
				//订阅 学习 教辅 习题 真题 资料
				case '订阅':
				case '资料':
				case '学习':
				case '真题':
				case '教辅':
				case '习题':
					if (!$model->where($map)->select()) {
						$data['token'] = $map['token'];
						$data['openid'] = $map['openid'];
						$model->add($data);
					}
					return $content = replace_url ( htmlspecialchars_decode ( $info ['content'] ) );
					break;
				case '高中':
				case '初中':
				case '小学':
				case '通用':
					if (!$model->where($map)->select()) {
						$data['token'] = $map['token'];
						$data['openid'] = $map['openid'];
						$data['stage'] = $keyword;
						$model->add($data);
					}
					else {

						$data['stage'] = $keyword;
						$model->where($map)->field('stage')->save($data);
					}
					return $content = replace_url ( htmlspecialchars_decode ( $info ['content'] ) );
					break;
				default:
					return $content = replace_url ( htmlspecialchars_decode ( $info ['content'] ) );
			}
		}
	}

}
        	