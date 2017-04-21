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



# 创建任务
if($model=='create_task'){

    // if (($is_true ==1) && ($is_gold== 1)) {
    	# code...

		if(submitcheck('create_submit') && $is_gold && $is_gold){
			$get_data = file_get_contents("php://input");
			parse_str($get_data, $data);

			$message = $data['message'];
			$pingtai_name = $data['pingtai_name'];
			$type = $data['type'];
			$note = $data['note'];

			$data = array_filter($data);
	 
			unset($data['note'],$data['message'],$data['pingtai_name'],$data['type'],$data['formhash'],$data['create_submit']);

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
				'note'=>$note,
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
			$list = json_decode($homelist[$i]['list'],true);
			$homelist[$i]['list'] = $list;
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

		$getid = $data['getid'];
		$zfb = $data['zfb'];
		$qq = $data['qq'];
		$other = $data['other'];
		$taskid = $data['taskid'];

		unset($data['taskid'],$data['getid'],$data['zfb'],$data['qq'],$data['other'],$data['formhash'],$data['submit_task']);

		// $arr = json_encode($data);
		// file_put_contents("/Users/breaking/www/upload/source/plugin/jubei_task/data.txt", count($data),FILE_APPEND);

		$lenght = count($data)/3;

		$task_res = C::t("#jubei_task#jubei_task_list")->fetch_by_id($taskid);
		if ($task_res['type'] == 0) { //限量单


			$get_task_res = C::t("#jubei_task#jubei_task_get")->fetch_by_id($getid);
			$get_list = json_decode($get_task_res['list'],true);
			$get_num = array_values($get_list);
			$get_money = array_keys($get_list);
			$submit_task_list = array();
			$shengyu_list = array();
			$success_num = 0;
			# 逐条插入，一个交单号码名字，就是一条数据
			# 此时仅仅判断提交的数据是否符合预约的数据
			for ($i=1; $i <= $lenght; $i++) { 
				$money_k = 'money'.$i;
				$name_k = 'name'.$i;
				$tel_k = 'tel'.$i;
				if (!in_array($data[$money_k], $get_money)) { //档位不对，//判断当前的交单档位是否在预约档位中，
					showmessage(lang('plugin/jubei_task','sublimt_task_error'),'plugin.php?id=jubei_task&model=mycomplete');
					exit;
				}
				$submit_task_num = array_values($submit_task_list);
				$submit_task_money = array_keys($submit_task_list);	

				if (in_array($data[$money_k], $submit_task_money)) {
					$submit_task_list[$data[$money_k]] = $submit_task_list[$data[$money_k]]+1;
				}else{
					$submit_task_list[$data[$money_k]] = 1;
				}
				if ($submit_task_list[$data[$money_k]] > $get_list[$data[$money_k]]) { //交单数量大于领取名额数量，抛出错误
					showmessage(lang('plugin/jubei_task','sublimt_task_error2'),'plugin.php?id=jubei_task&model=mycomplete');
					exit;
				}
				// $shengyu_list[$data[$money_k]] = $get_list[$data[$money_k]] - $submit_task_list[$data[$money_k]];
			}

			$submit_money = array_keys($submit_task_list);

	// foreach ($submit_money as $va) {  //判断当前的交单档位是否在预约档位中，与上面的代码重复
	//     if (!in_array($va, $get_money)) {  
	// 		showmessage(lang('plugin/jubei_task','sublimt_task_error3'),'plugin.php?id=jubei_task&model=mycomplete');
	// 		exit;
	//     }  
	// } 


			for ($n=0; $n < count($get_money); $n++) { 
				
				//根据预约数量和上面的到的交单数量，得到剩余未完成的预约数量
				if (in_array($get_money[$n], $submit_money)) {
					$shengyu_list[$get_money[$n]] = $get_list[$get_money[$n]] - $submit_task_list[$get_money[$n]];
				}else{
					$shengyu_list[$get_money[$n]] = $get_list[$get_money[$n]];
				}
				
			}

		}

			// file_put_contents("/Users/breaking/www/upload/source/plugin/jubei_task/data.txt",print_r($shengyu_list,true),FILE_APPEND);



// showmessage('您已更新店铺名为：{name} ', 'plugin.php?id=jubei_task&model=mycomplete', array('name' => 'DZ起点网'));

		//这个for循环用来插入数据
		for ($k=1; $k <= $lenght; $k++) { 
			$money_k = 'money'.$k;
			$name_k = 'name'.$k;
			$tel_k = 'tel'.$k;
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
			$insert_status = C::t("#jubei_task#jubei_task_complete")->insert($insert_data);

			if ($insert_status == 1 && $task_res['type'] == 0) {
				// $success_num = $success_num +1;
				$shengyu_list = json_encode($shengyu_list);
				// file_put_contents("/Users/breaking/www/upload/source/plugin/jubei_task/data.txt",$shengyu_list,FILE_APPEND);
				// file_put_contents("/Users/breaking/www/upload/source/plugin/jubei_task/data.txt",'success_num='.$success_num,FILE_APPEND);

				$update_get_data = array("list"=>$shengyu_list);
				C::t("#jubei_task#jubei_task_get")->update_by_id($update_get_data,$getid);
				$shengyu_list = json_decode($shengyu_list,true);

			}
		}
			showmessage(lang('plugin/jubei_task','sublimt_task_success'),'plugin.php?id=jubei_task&model=mycomplete');
			exit;
// showmessage('您已更新店铺名为：{name} ', 'plugin.php?id=jubei_task&model=mycomplete', array('name' => 'DZ起点网'));


	}else{
		# 交单界面
		$getid = $_GET['getid'];
		$taskid = $_GET['taskid'];

		if ($getid) {
			$get_task_res = C::t("#jubei_task#jubei_task_get")->fetch_by_id($getid);
			$taskid = $get_task_res['taskid'];
			$homelist = json_decode($get_task_res['list'],true);
		}
		$message = C::t("#jubei_task#jubei_task_message")->fetch_by_uid($_G['uid']);
		
		
		$res = C::t("#jubei_task#jubei_task_list")->fetch_by_id($taskid);
		$homelist = json_decode($res['list'],true);
		$lenght = count($homelist);
		include template('jubei_task:submit_task');
	}




# 预约任务
}else if($model=='get_task'){

	# 预约任务。，提交表单
	if(submitcheck('get_task_submit')){
		$get_data = file_get_contents("php://input");
		parse_str($get_data, $data);

		$taskid = $data['taskid'];
		$qq = $data['qq'];
		$username = $_G['username'];
		$uid = $_G['uid'];

		unset($data['formhash'],$data['get_task_submit'],$data['taskid'],$data['qq']);
		$lenght = count($data)/2;

	// file_put_contents("/Users/breaking/www/upload/source/plugin/jubei_task/data.txt", print_r($data,true));
		
		$task_res = C::t("#jubei_task#jubei_task_list")->fetch_by_id($taskid); #根据taskid取出当前任务的数量信息
		$task_res['list'] = json_decode($task_res['list'],true);
		$keys = array_keys($task_res['list']);

		$data_status = false;
		# 作为一个list插入
		$new_data = array();
		for ($i=1; $i <= $lenght; $i++) { 
			$money_k = 'money'.$i;
			$num_k = 'num'.$i;
			$new_data[$data[$money_k]] = $data[$num_k];

			#判断输入的档位是否在任务已有的档位之中
			if (in_array($data[$money_k], $keys)) {
				$value = $task_res['list'][$data[$money_k]];
				#判断剩余的名额是否足够
				if ((int)$value >= (int)$data[$num_k]) {
					$shengyu_num = (int)$value - (int)$data[$num_k];

					$task_res['list'][$data[$money_k]] = $shengyu_num;
					$data_status = true;
					

					// file_put_contents("/Users/breaking/www/upload/source/plugin/jubei_task/data.txt",$shengyu_num.'-'.$money.'-'.$value,FILE_APPEND);
				}else{
					$data_status = false;
					showmessage(lang('plugin/jubei_task','shengyu_num_error'),'plugin.php?id=jubei_task&model=get_task&taskid='.$taskid);
					exit;
				}
				
			}else{
				$data_status = false;
				showmessage(lang('plugin/jubei_task','get_money_error'),'plugin.php?id=jubei_task&model=get_task&taskid='.$taskid);
				exit;
			}			
		}
		$list = json_encode($new_data);
		// for ($i=1; $i <= $lenght; $i++) { 
		// 	$money_k = 'money'.$i;
		// 	$num_k = 'num'.$i;

		// 	$money = $data[$money_k];
		// 	$num = $data[$num_k];


		// 	#判断输入的档位是否在任务已有的档位之中
		// 	if (in_array($money, $keys)) {
		// 		$value = $task_res['list'][$money];
		// 		#判断剩余的名额是否足够
		// 		if ((int)$value >= (int)$num) {
		// 			$shengyu_num = (int)$value - (int)$num;

		// 			$task_res['list'][$money] = $shengyu_num;
		// 			$data_status = true;
					

		// 			// file_put_contents("/Users/breaking/www/upload/source/plugin/jubei_task/data.txt",$shengyu_num.'-'.$money.'-'.$value,FILE_APPEND);
		// 		}else{
		// 			$data_status = false;
		// 			showmessage(lang('plugin/jubei_task','shengyu_num_error'),'plugin.php?id=jubei_task&model=get_task&taskid='.$taskid);
		// 			exit;
		// 		}
				
		// 	}else{
		// 		$data_status = false;
		// 		showmessage(lang('plugin/jubei_task','get_money_error'),'plugin.php?id=jubei_task&model=get_task&taskid='.$taskid);
		// 		exit;
		// 	}
		// }
		if ($data_status == true) {
			

				$insert_data = array(
					'list'=>$list,
					'qq'=>$qq,
					'username'=>$username,
					'uid'=>$uid,
					'taskid'=>$taskid,
					'begintime'=>date("m-d H:i",time()),
					'status'=>1
					);
				C::t("#jubei_task#jubei_task_get")->insert($insert_data);
				$shengyu_list_num = array_values($task_res['list']);
				if (array_unique($shengyu_list_num) == array(0)) {
					$shengyu_list = array("status"=>0);
				}

				#更新剩余名额数据
				$shengyu_list['list'] = json_encode($task_res['list']);

				C::t("#jubei_task#jubei_task_list")->update_by_id($shengyu_list,$taskid);
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


# quxiao yuyue
}else if($model=='cancel_task_get'){
	if(submitcheck('cancel_task_get')){
		$get_data = file_get_contents("php://input");
		parse_str($get_data, $data);
		$getid = $data['getid'];
		$taskid = $data['taskid'];
		unset($data['taskid'],$data['getid'],$data['formhash']);
		$cancel_task_list = array();
		$res = C::t("#jubei_task#jubei_task_get")->fetch_by_id($getid);
		$get_list = json_decode($res['list'],true);
		for ($i=1; $i <= (count($data)/2); $i++) { 
			$money_k = 'money'.$i;
			$num_k = 'num'.$i;
			$cancel_task_list[$data[$money_k]] = $data[$num_k];
		}
		$cancel_money = array_keys($cancel_task_list);

		for ($n=0; $n < count($cancel_money); $n++) { 
			if($cancel_task_list[$cancel_money[$n]] > $get_list[$cancel_money[$n]]){
				showmessage(lang('plugin/jubei_task','cancel_task_get_error'),'plugin.php?id=jubei_task&model=myreservation');
			}
		}
		$shengyu_list = array();
		$get_money = array_keys($get_list);


		for ($n=0; $n < count($get_money); $n++) { 
			//根据预约数量和上面的到的退单数量，得到剩余未完成的预约数量、
			//遍历数量少的，判断数量多的的key是否在数量少的的key里面，有的话，减去，没有的话数量不变
			if (in_array($get_money[$n], $cancel_money)) {
				$shengyu_list[$get_money[$n]] = $get_list[$get_money[$n]] - $cancel_task_list[$get_money[$n]];
			}else{
				$shengyu_list[$get_money[$n]] = $get_list[$get_money[$n]];
			}
			
		}


		$shengyu_list = json_encode($shengyu_list);
		$update_get_data = array("list"=>$shengyu_list);
		C::t("#jubei_task#jubei_task_get")->update_by_id($update_get_data,$getid);

		//更新任务的名额
		$total_update_data = array();
		$total_res = C::t("#jubei_task#jubei_task_list")->fetch_by_id($taskid);
		$total_list = json_decode($total_res['list'],true);
		$total_list_num = array_values($total_list);
		if (array_unique($total_list_num) == array(0)) { //如果任务余额是0，更新状态
			$total_update_data["status"] = 1;
		}
		for ($m=0; $m < count($cancel_money); $m++) { 
			//循环更新任务的数量
			$total_list[$cancel_money[$m]] = $total_list[$cancel_money[$m]]+ $cancel_task_list[$cancel_money[$m]];
		}
		$total_update_data["list"] = json_encode($total_list);
		C::t("#jubei_task#jubei_task_list")->update_by_id($total_update_data,$taskid);
		showmessage(lang('plugin/jubei_task','cancel_task_success'),'plugin.php?id=jubei_task&model=myreservation');


	}else{
		$getid = $_GET['getid'];
		$res = C::t("#jubei_task#jubei_task_get")->fetch_by_id($getid);
		$taskid = $res['taskid'];
		include template('jubei_task:cancel_task_get');
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
	// $homelist['list'] = json_decode($homelist['list'],true);
	# 循环取出平台名字
	for ($i=0; $i <count($homelist) ; $i++) { 
		$taskid = $homelist[$i]['taskid'];
		$res = C::t("#jubei_task#jubei_task_list")->fetch_by_id($taskid);
		$homelist[$i]['pingtai_name'] = $res['pingtai_name'];
		$homelist[$i]['list'] = json_decode($homelist[$i]['list'],true);
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
		header("Content-Type: application/csv;charset=utf8");  
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

		// header('Content-Type: application/csv;charset=utf8');
		// header('Content-Disposition: attachment; filename=test.csv');
		// // header('Pragma: no-cache');
		// // header('Expires: 0');
		// $fp = fopen('php://output', 'w');
		// //输出BOM头
		// fwrite($fp, chr(0XEF) . chr(0xBB) . chr(0XBF));
		// //输出头
		// fputcsv($fp, array('id','姓名','电话','档位', '支付宝', 'qq','备注'));
		// for ($i=0; $i < count($row); $i++) { 
		// 	$e = array(($i+1),$row[$i]['name'],$row[$i]['tel'],$row[$i]['money'],$row[$i]['zfb'],$row[$i]['qq'],$row[$i]['other']);  
		// 	chr(255).chr(254);
			
		// 	fputcsv($output, array_values($e));
		// }
		// fclose($fp);
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

}else if($model=='update_task'){

	if(submitcheck('update_submit')){
			$get_data = file_get_contents("php://input");
			parse_str($get_data, $data);

			$message = $data['message'];
			$pingtai_name = $data['pingtai_name'];
			$type = $data['type'];
			$note = $data['note'];
			$taskid = $data['taskid'];
			$data = array_filter($data);
	 
			unset($data['taskid'],$data['note'],$data['message'],$data['pingtai_name'],$data['type'],$data['formhash'],$data['create_submit']);

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
			$update_data = array(
				'pingtai_name'=>$pingtai_name,
				'list' => $list,
				'taskremark'=>$message, 
				'username'=>$_G['username'], 
				'note'=>$note,
				'uid'=>$_G['uid'], 
				'type'=>$type,
				'begintime'=>$begintime,
				'status'=>1
				);
			C::t("#jubei_task#jubei_task_list")->update_by_uid_taskid($update_data,$_G['uid'],$taskid);
			showmessage(lang('plugin/jubei_task','create_task_success'),'plugin.php?id=jubei_task');
			exit;

	}else{
		$uid = $_G['uid'];
		$taskid = $_GET['taskid'];
		$res = C::t("#jubei_task#jubei_task_list")->fetch_by_id($taskid);
		$homelist = json_decode($res['list'],true);
		$lenght = count($homelist);
		include template('jubei_task:update_task');
	}

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