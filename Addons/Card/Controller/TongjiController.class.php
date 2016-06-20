<?php

namespace Addons\Card\Controller;

use Addons\Card\Controller\BaseController;

class TongjiController extends BaseController {
	function index() {
	    $year=I('year');
	    $month=I('month');
	    $is_ajax=I('is_ajax');
	    $map['token']=get_token();
	    if ($year && $month && $is_ajax){
	        $start_date=$year.'-'.$month;
	        $end_month=$month+1;
	        $end_date=$year.'-'.$end_month;
	        $start_date=strtotime($start_date);
	        $end_date=strtotime($end_date);
	        $map['cTime']=array('between',array($start_date,$end_date));
	    }else {
	        $now_month=time_format(NOW_TIME,'Y-m');
	        $map['cTime']=array('egt',strtotime($now_month));
	    }
	    //本月总消费金额
	    $totalCount=M('card_member')->where($map)->count();
	    
	    $totalCount=intval($totalCount[0]['totalCount']);
	    $this->assign('total_count',$totalCount);
	     
	    $data=M('card_member')->where($map)->field("count(id) totalCount,from_unixtime(cTime,'%m-%d') allday")->group("allday")->select();
	    foreach ($data as $v){
	        $allDay[]=$v['allday'];
	        $allCount[]=intval($v['totalCount']);
	    }
	    $highcharts['xAxis']=$allDay;
	    $highcharts['series']=$allCount;
	    if ($is_ajax){
	        $highcharts['total_count']=$totalCount;
	        $this->ajaxReturn($highcharts);
	    }else {
	        $highcharts=json_encode($highcharts);
	        $this->assign('highcharts',$highcharts);
	        $this->display();
	    
	    }
	}
}
