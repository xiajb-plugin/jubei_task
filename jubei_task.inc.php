<?php
/**
 *      [Liangjian] (C)2001-2099 Liangjian Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: fingerguess.inc.php liangjian $
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
// require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'common.php';
global $_G;
$uid = $_G['uid'];
$complete_num=C::t("#jubei_task#jubei_task_complete")->count_by_uid($uid);

$my_message = C::t("#jubei_task#jubei_task_message")->fetch_by_uid($uid);

$perpage=10;
$model = daddslashes($_GET['model']);
$complete_group = $_G['cache']['plugin']['jubei_task']['complete_group'];
$gold = $_G['cache']['plugin']['jubei_task']['gold'];
#我的金币

$mygold = C::t("#jubei_task#jubei_task_list")->fetch_extcredits($_G['uid']);



# 创建任务
if($model=='create_task'){
	if (in_array($_G['groupid'], unserialize($_G['cache']['plugin']['jubei_task']['create_group']))) {
		# 允许推单
		$is_true = 1;
    }else{
    	$is_true = 0;
    }

    if ($mygold < $gold) {
    	# 金币不足，不能推单
    	$is_gold = 0;
    }else{
    	$is_gold =1;
    }
    // if (($is_true ==1) && ($is_gold== 1)) {
    	# code...

		if(submitcheck('create_submit') && $is_gold && $is_gold){
			$get_data = file_get_contents("php://input");
			parse_str($get_data, $data);

			$message = $data['message'];
			$pingtai_name = $data['pingtai_name'];
			$type = $data['type'];

			$data = array_filter($data);
	 
			unset($data['message'],$data['pingtai_name'],$data['type'],$data['formhash'],$data['create_submit']);

			$lenght = count($data)/2;
			$new_data = array();
			for ($i=1; $i <= $lenght; $i++) { 
				$money_k = 'money'.$i;
				$num_k = 'num'.$i;
				$new_data[$data[$money_k]] = $data[$num_k];
				
			}
			$list = json_encode($new_data);


			# 以json的方式存储
			// file_put_contents("/Users/breaking/www/upload/source/plugin/jubei_task/data.txt", print_r($new_data,true),FILE_APPEND);

			$begintime = date("m-d H:i",time());
			$insert_data = array(
				'pingtai_name'=>$pingtai_name,
				'list' => $list,
				'taskremark'=>$message, 
				'username'=>$_G['username'], 
				'uid'=>$_G['uid'], 
				'type'=>$type,
				'begintime'=>$begintime,
				'status'=>1
				);
			C::t("#jubei_task#jubei_task_list")->insert($insert_data);

			updatemembercount($_G['uid'],array('extcredits2'=>'-'.($gold)));
				showmessage(lang('plugin/jubei_task','create_task_success'),'plugin.php?id=jubei_task');
				exit;
	    // }
	}else{
		include template('jubei_task:create_task');

	}

}else if($model=='mymessage'){
		
	if(submitcheck('submit_message')){
		$uid = $_G['uid'];
		$res = C::t("#jubei_task#jubei_task_message")->fetch_by_uid($uid);
		# 如果个人信息为空，插入，否则update
		if (empty($res)) {
			$zfb = $_POST['zfb'];
			$qq = $_POST['qq'];
			$data = array("zfb"=>$zfb,"qq"=>$qq,"uid"=>$_G['uid'],"username"=>$_G['username']);
			C::t("#jubei_task#jubei_task_message")->insert($data);
		}else{
			$zfb = $_POST['zfb'];
			$qq = $_POST['qq'];
			$data = array("zfb"=>$zfb,"qq"=>$qq,"uid"=>$_G['uid'],"username"=>$_G['username']);
			C::t("#jubei_task#jubei_task_message")->update_by_id($data);
		}
		showmessage(lang('plugin/jubei_task','message_success'),'plugin.php?id=jubei_task');
		exit;


	}

	include template('jubei_task:mymessage');


# 任务详情

}else if($model=='task_pro'){
	$uid = $_G['uid'];
	$taskid = $_GET['taskid'];
	$res = C::t("#jubei_task#jubei_task_list")->fetch_by_id($taskid);
	$homelist = json_decode($res['list'],true);
	$lenght = count($homelist);
	// file_put_contents("/Users/breaking/www/upload/source/plugin/jubei_task/data.txt",print_r($homelist,true) ,FILE_APPEND);

	include template('jubei_task:task_pro');


# 我发布的任务
}else if($model=='myrelease'){

	$uid = $_G['uid'];
    $page = daddslashes($_GET['page']);
	$currpage=$page?$page:1;
	
	$num=C::t("#jubei_task#jubei_task_list")->count_by_log($uid);
	$start=($currpage-1)*$perpage;
	$homelist=C::t("#jubei_task#jubei_task_list")->fetch_all_by_uid($uid,$start,$perpage);
	$paging = helper_page :: multi($num, $perpage, $currpage, 'plugin.php?id=jubei_task&model=myrelease', 0, 10, false, false);
	include template('jubei_task:myrelease');




}else if($model=='complete_pro'){

	$taskid = $_GET['taskid'];
	$task_res = C::t("#jubei_task#jubei_task_list")->fetch_by_id($taskid);
	$uid = $_G['uid'];
	$task_uid = $task_res['uid'];
	if ($uid == $task_uid) {
	    $page = daddslashes($_GET['page']);
		$currpage=$page?$page:1;
		
		$num=C::t("#jubei_task#jubei_task_complete")->count_by_taskid($taskid);
		$start=($currpage-1)*$perpage;
		$homelist = C::t("#jubei_task#jubei_task_complete")->fetch_all_by_taskid($taskid,$start,$perpage);
		# 循环取出平台名字
		for ($i=0; $i <count($homelist) ; $i++) { 
			$taskid = $homelist[$i]['taskid'];
			$res = C::t("#jubei_task#jubei_task_list")->fetch_by_id($taskid);
			$homelist[$i]['pingtai_name'] = $res['pingtai_name'];
			// file_put_contents("/Users/breaking/www/upload/source/plugin/jubei_task/data.txt",print_r($res,true) ,FILE_APPEND);
		}
		$paging = helper_page :: multi($num, $perpage, $currpage, 'plugin.php?id=jubei_task&model=complete_pro&taskid='.$taskid, 0, 10, false, false);
	}else{
		$see_is_not = 1;
	}

	include template('jubei_task:complete_pro');

}else if($model=='reservation_pro'){

	$taskid = $_GET['taskid'];
	$task_res = C::t("#jubei_task#jubei_task_list")->fetch_by_id($taskid);
	$uid = $_G['uid'];
	$task_uid = $task_res['uid'];
	if ($uid == $task_uid) {
	    $page = daddslashes($_GET['page']);
		$currpage=$page?$page:1;
		
		$num=C::t("#jubei_task#jubei_task_get")->count_by_taskid($taskid);
		$start=($currpage-1)*$perpage;
		$homelist = C::t("#jubei_task#jubei_task_get")->fetch_all_by_taskid($taskid,$start,$perpage);
		# 循环取出平台名字
		for ($i=0; $i <count($homelist) ; $i++) { 
			$taskid = $homelist[$i]['taskid'];
			$res = C::t("#jubei_task#jubei_task_list")->fetch_by_id($taskid);
			$homelist[$i]['pingtai_name'] = $res['pingtai_name'];
			// file_put_contents("/Users/breaking/www/upload/source/plugin/jubei_task/data.txt",print_r($res,true) ,FILE_APPEND);
		}
		$paging = helper_page :: multi($num, $perpage, $currpage, 'plugin.php?id=jubei_task&model=reservation_pro&taskid='.$taskid, 0, 10, false, false);
	}else{
		$see_is_not = 1;
	}

	include template('jubei_task:reservation_pro');


# 交单
}else if($model=='submit_task'){
	if(submitcheck('submit_task')){
		$get_data = file_get_contents("php://input");
		parse_str($get_data, $data);
		$data = array_filter($data);
 
		$username = $_G['username'];
		$uid = $_G['uid'];

		$taskid = $data['taskid'];
		$zfb = $data['zfb'];
		$qq = $data['qq'];
		$other = $data['other'];

		unset($data['taskid'],$data['zfb'],$data['qq'],$data['other'],$data['formhash'],$data['submit_task']);

		// $arr = json_encode($data);
		// file_put_contents("/Users/breaking/www/upload/source/plugin/jubei_task/data.txt", count($data),FILE_APPEND);

		$lenght = count($data)/3;

		# 逐条插入，一个交单号码名字，就是一条数据
		for ($i=1; $i <= $lenght; $i++) { 
			$money_k = 'money'.$i;
			$name_k = 'name'.$i;
			$tel_k = 'tel'.$i;
			

		
			$insert_data = array(
				'money' => $data[$money_k], 
				'name'=>$data[$name_k],
				'tel'=>$data[$tel_k],
				'username'=>$username,
				'uid'=>$uid,
				'taskid'=>$taskid,
				'qq'=>$qq,
				'zfb'=>$zfb,
				'other'=>$other,
				'begintime'=>date("m-d H:i",time())
				);
			C::t("#jubei_task#jubei_task_complete")->insert($insert_data);
		
			
		}

			showmessage(lang('plugin/jubei_task','sublimt_task_success'),'plugin.php?id=jubei_task&model=mycomplete');
			exit;
	}else{
		# 交单界面

		$message = C::t("#jubei_task#jubei_task_message")->fetch_by_uid($_G['uid']);
		$taskid = $_GET['taskid'];
		$res = C::t("#jubei_task#jubei_task_list")->fetch_by_id($taskid);
		$homelist = json_decode($res['list'],true);
		$lenght = count($homelist);
		include template('jubei_task:submit_task');
	}




# 预约任务
}else if($model=='get_task'){

	# 预约任务提交表单
	if(submitcheck('get_task_submit')){
		$get_data = file_get_contents("php://input");
		parse_str($get_data, $data);

		$taskid = $data['taskid'];
		$qq = $data['qq'];
		$username = $_G['username'];
		$uid = $_G['uid'];

		unset($data['formhash'],$data['get_task_submit'],$data['taskid'],$data['qq']);


	// file_put_contents("/Users/breaking/www/upload/source/plugin/jubei_task/data.txt", print_r($data,true));
		$lenght = count($data)/2;
		$task_res = C::t("#jubei_task#jubei_task_list")->fetch_by_id($taskid); #根据taskid取出集合
		$task_res['list'] = json_decode($task_res['list'],true);
		$keys = array_keys($task_res['list']);


		# 逐条插入，一个档位预约就是一条数据
		for ($i=1; $i <= $lenght; $i++) { 
			$money_k = 'money'.$i;
			$num_k = 'num'.$i;

			$money = $data[$money_k];
			$num = $data[$num_k];


			#判断输入的档位是否在任务已有的档位之中
			if (in_array($money, $keys)) {
				$value = $task_res['list'][$money];
				#判断剩余的名额是否足够
				if ((int)$value >= (int)$num) {
					$shengyu_num = (int)$value - (int)$num;

					$task_res['list'][$money] = $shengyu_num;
					// file_put_contents("/Users/breaking/www/upload/source/plugin/jubei_task/data.txt",$shengyu_num.'-'.$money.'-'.$value,FILE_APPEND);
				}else{
					showmessage(lang('plugin/jubei_task','shengyu_num_error'),'plugin.php?id=jubei_task&model=get_task&taskid='.$taskid);
					exit;
				}
				
			}else{
				showmessage(lang('plugin/jubei_task','get_money_error'),'plugin.php?id=jubei_task&model=get_task&taskid='.$taskid);
				exit;
			}

			$insert_data = array(
				'num' => $data[$num_k], 
				'money'=>$data[$money_k],
				'qq'=>$qq,
				'username'=>$username,
				'uid'=>$uid,
				'taskid'=>$taskid,
				'begintime'=>date("m-d H:i",time())
				);
			C::t("#jubei_task#jubei_task_get")->insert($insert_data);


			#更新剩余名额数据
			$list = array('list'=>json_encode($task_res['list']));

			C::t("#jubei_task#jubei_task_list")->update_by_id($list,$taskid);

		}
		showmessage(lang('plugin/jubei_task','get_task_success'),'plugin.php?id=jubei_task&model=myreservation');
		exit;	
		

	}else{

		# 预约任务界面
		$taskid = $_GET['taskid'];
		$res = C::t("#jubei_task#jubei_task_list")->fetch_by_id($taskid);
		$homelist = json_decode($res['list'],true);
		$lenght = count($homelist);
		// file_put_contents("/Users/breaking/www/upload/source/plugin/jubei_task/data.txt",print_r($homelist,true) ,FILE_APPEND);
		
		include template('jubei_task:get_task');
	}

# 我的预约
}else if($model=='myreservation'){
	# 分页取出数据
	$uid = $_G['uid'];
    $page = daddslashes($_GET['page']);
	$currpage=$page?$page:1;
	
	$num=C::t("#jubei_task#jubei_task_get")->count_by_uid($uid);
	$start=($currpage-1)*$perpage;
	$homelist=C::t("#jubei_task#jubei_task_get")->fetch_all_by_uid($uid,$start,$perpage);
	# 循环取出平台名字
	for ($i=0; $i <count($homelist) ; $i++) { 
		$taskid = $homelist[$i]['taskid'];
		$res = C::t("#jubei_task#jubei_task_list")->fetch_by_id($taskid);
		$homelist[$i]['pingtai_name'] = $res['pingtai_name'];
	}
	$paging = helper_page :: multi($num, $perpage, $currpage, 'plugin.php?id=jubei_task&model=myreservation', 0, 10, false, false);

	include template('jubei_task:myreservation');

}else if($model=='down'){
		$uid = $_G['uid'];
		$taskid = $_GET['taskid'];
		if (empty($taskid)) {
			showmessage(lang('plugin/jubei_task','not_data'),'plugin.php?id=jubei_task&model=myreservation');
			exit;

		}

		$row = C::t("#jubei_task#jubei_task_complete")->fetch_all_taskid($taskid);
		// file_put_contents("/Users/breaking/www/upload/source/plugin/jubei_task/data.txt",print_r($row,true) ,FILE_APPEND);

		$output = fopen('php://output', 'w') or die("can't open php://output");  
		fwrite($file, chr(0XEF) . chr(0xBB) . chr(0XBF));
		//告诉浏览器这个是一个csv文件  
		$filename = "交单信息表" . date('Y-m-d', time());  
		header("Content-Type: application/csv");  
		header("Content-Disposition: attachment; filename=$filename.csv");  
		
		//输出表头  
		$table_head = array('id','姓名','电话','档位', '支付宝', 'qq','备注');
		
		fputcsv($output, $table_head);  

		for ($i=0; $i < count($row); $i++) { 
			$e = array(($i+1),$row[$i]['name'],$row[$i]['tel'],$row[$i]['money'],$row[$i]['zfb'],$row[$i]['qq'],$row[$i]['other']);  
			chr(255).chr(254);
			
			fputcsv($output, array_values($e));
		}
		fclose($output) or die("can't close php://output");  
		exit;

# 我完成的任务
}else if($model=='mycomplete'){


	$uid = $_G['uid'];
    $page = daddslashes($_GET['page']);
	$currpage=$page?$page:1;
	
	$num=C::t("#jubei_task#jubei_task_complete")->count_by_uid($uid);
	$start=($currpage-1)*$perpage;
	$homelist = C::t("#jubei_task#jubei_task_complete")->range_by_uid($uid,$start,$perpage);
	for ($i=0; $i <count($homelist) ; $i++) { 
		$taskid = $homelist[$i]['taskid'];
		$res = C::t("#jubei_task#jubei_task_list")->fetch_by_id($taskid);
		$homelist[$i]['pingtai_name'] = $res['pingtai_name'];
		// file_put_contents("/Users/breaking/www/upload/source/plugin/jubei_task/data.txt",print_r($res,true) ,FILE_APPEND);
	}
	$paging = helper_page :: multi($num, $perpage, $currpage, 'plugin.php?id=jubei_task&model=mycomplete', 0, 10, false, false);
	include template('jubei_task:mycomplete');

}else if($model=='zanting'){
	
	$taskid = $_GET['taskid'];
	$data = array("status"=>2);
	C::t("#jubei_task#jubei_task_list")->update_by_uid_taskid($data,$uid,$taskid);  //根据两个id来更新，防止远程提交

	showmessage(lang('plugin/jubei_task','zanting_task'),'plugin.php?id=jubei_task&model=myrelease');
	exit;


}else {


    $page = daddslashes($_GET['page']);
	$currpage=$page?$page:1;
	
	$num=C::t("#jubei_task#jubei_task_list")->count_by_log();
	$start=($currpage-1)*$perpage;
	$homelist=C::t("#jubei_task#jubei_task_list")->fetch_all_by_log($start,$perpage);

	$paging = helper_page :: multi($num, $perpage, $currpage, 'plugin.php?id=jubei_task&model=index', 0, 10, false, false);
	// C::t("#jubei_task#jubei_task_list")->fetch_all_by_log($start,$limit);
	include template('jubei_task:index');
}


  



// require DISCUZ_ROOT.'./source/plugin/jubei_task/module/jubei_task_'.$mod.'.php';
?>