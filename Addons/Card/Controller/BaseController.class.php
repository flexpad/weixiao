<?php

namespace Addons\Card\Controller;

use Home\Controller\AddonsController;

class BaseController extends AddonsController {
	function _initialize() {
		parent::_initialize();
		
		$controller = strtolower ( _CONTROLLER );
		
		$res ['title'] = '会员卡设置';
		$res ['url'] = addons_url ( 'Card://Card/config' ,array('mdm'=>I('mdm')));
		$res ['class'] = $controller == 'card' || $controller == 'cardprivilege' || $controller == 'cardlevel' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '会员管理';
		$res ['url'] = addons_url ( 'Card://member/lists' ,array('mdm'=>I('mdm')));
		$res ['class'] = $controller == 'member' ? 'current' : '';
		$nav [] = $res;
		
		if (is_install("Shop")) {
            $res['title'] = '实体店会员管理';
            $res['url'] = addons_url('Card://ShopMember/lists', array(
                'mdm' => I('mdm')
            ));
            $res['class'] = $controller == 'shopmember' ? 'current' : '';
            $nav[] = $res;
        }
		$res ['title'] = '会员交易';
		$res ['url'] = addons_url ( 'Card://MemberTransition/lists' ,array('mdm'=>I('mdm')));
		$res ['class'] = $controller == 'membertransition' ? 'current' : '';
		$nav [] = $res;
		$res ['title'] = '会员营销';
		$res ['url'] = addons_url ( 'Card://MemberMarketing/lists' ,array('mdm'=>I('mdm')));
		$res ['class'] = $controller == 'membermarketing' ? 'current' : '';
		$nav [] = $res;
		$res ['title'] = '通知管理';
		$res ['url'] = addons_url ( 'Card://Notice/lists' ,array('mdm'=>I('mdm')));
		$res ['class'] = $controller == 'notice' ? 'current' : '';
		$nav [] = $res;
		$res ['title'] = '数据统计';
		$res ['url'] = addons_url ( 'Card://Tongji/index',array('mdm'=>I('mdm')) );
		$res ['class'] = $controller == 'tongji' ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
		if ($controller == 'wap'){
			$uid=$this->mid;
			$token = get_token();
			//获取通知数
			$key='cardnotic_'.$token.'_'.$uid;
			$rrs=S($key);
			if($rrs === false){
				$beforetime=7 * 24 * 60 * 60;
				$thetime=strtotime(time_format(time(),'Y-m-d'))-$beforetime;
				$cmap ['token'] = $token;
				$cmap ['uid']= $uid;
				$cardMember=M('card_member')->where($cmap)->find();
				if (!empty($cardMember['level'])){
					$map['cTime']=array('egt',$thetime);
					$map['token']=$token;
						
					$notices =M('card_notice')->where($map)->select();
					foreach ($notices as $v){
						$gradeArr=explode(',',$v['grade']);
						if ($v['to_uid']==0){
							if (in_array(0, $gradeArr) || in_array($cardMember['level'], $gradeArr)){
								$data[]=$v;
							}
						}else if ($v['to_uid']==$uid){
							$data[]=$v;
						}
					}
					$rrs=count($data);
					S($key,$rrs);
				}
			}else if($rrs <= 0){
				$rrs='';
			}
			$this->assign('notice_num',$rrs);
		}
		
		$config = getAddonConfig ( 'Card' );
		$config['instruction']=str_replace("\n","<br/>",$config['instruction']);
		$config ['background_url'] = $config ['background'] == 11 ? $config ['background_custom'] : ADDON_PUBLIC_PATH . '/card_bg_' . $config ['background'] . '.png';
		$config ['back_background_url'] = $config ['back_background'] == 11 ? $config ['back_background_custom'] : ADDON_PUBLIC_PATH . '/card_bg_' . $config ['back_background'] . '.png';
		$this->assign ( 'config', $config );
	}
	
}
