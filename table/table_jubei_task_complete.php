<?php
/**
 *      [Liangjian] (C)2001-2099 Liangjian Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: table_alj_fingerguess.php liangjian $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_jubei_task_complete extends discuz_table
{
	public function __construct() {

		$this->_table = 'jubei_task_complete';
		$this->_pk    = 'uid';

		parent::__construct();
	}
	public function range_by_uid($uid,$start,$limit){
		// return DB::fetch_all("select *  from ".DB::table($this->_table)." where uid=$uid order by desc limit $start,$limit");	
		return DB::fetch_all("select *  from ".DB::table($this->_table)." where uid=".$uid." order by id desc limit $start,$limit");	
	}
	# 根据uid取出分页数据
	public function fetch_all_by_uid($uid,$start,$limit){
		return DB::fetch_all("select *  from ".DB::table($this->_table)." where islog=0 and uid=".$uid." order by id desc limit $start,$limit");
	}
	#根据uid查询总数据
	public function count_by_uid($uid){
		return DB::result_first("select count(*) from ".DB::table($this->_table)." where uid=".$uid);
	}
	    public function insert($data) {
	        return DB::insert($this->_table,$data);
	    }


	#根据uid和task_id联合查询查询总数据
	public function count_by_uid_taskid($uid,$taskid){
		return DB::result_first("select count(*) from ".DB::table($this->_table)." where uid=$uid and taskid=$taskid");
	}
	#根据uid和task_id联合查询查询分页数据
	public function fetch_all_by_uid_taskid($uid,$taskid,$start,$limit){
		return DB::fetch_all("select *  from ".DB::table($this->_table)." where uid=".$uid." and taskid=".$taskid." order by id desc limit $start,$limit");
	}

	#根据uid和task_id联合查询查询总数据，提供导出下载
	public function fetch_all_uid_taskid($uid,$taskid){
		return DB::fetch_all("select *  from ".DB::table($this->_table)." where uid=".$uid." and taskid=".$taskid);
	}



	#根据task_id查询查询总数据
	public function count_by_taskid($taskid){
		return DB::result_first("select count(*) from ".DB::table($this->_table)." where taskid=$taskid");
	}
	#根据task_id联合查询查询分页数据
	public function fetch_all_by_taskid($taskid,$start,$limit){
		return DB::fetch_all("select *  from ".DB::table($this->_table)." where taskid=".$taskid." order by id desc limit $start,$limit");
	}

	public function fetch_all_taskid($taskid){
		return DB::fetch_all("select *  from ".DB::table($this->_table)." where taskid=".$taskid);
	}

	// public function count_guessmoney_by_uid($start,$perpage,$count){
	// 	return DB::fetch_all("select username,sum(guessmoney) money  from ".DB::table($this->_table)." group by username order by money desc  limit $start,$perpage");
	// }
	// public function count_countmoney_by_uid(){
	// 	$num=DB::fetch_all("select username,sum(guessmoney) money  from ".DB::table($this->_table)." group by username ");
	// 	$conutnum=count($num);
	// 	return $conutnum;
	// }
	// public function count_luckymoney_by_uid($start,$perpage){
	// 	return DB::fetch_all("select username,sum(guessmoney) money  from ".DB::table($this->_table)." group by username order by money desc  limit $start,$perpage");
		
	// }
	// public function count_recessionmoney_by_uid($start,$perpage){
	// 	return DB::fetch_all("select username,sum(guessmoney) money  from ".DB::table($this->_table)." group by username order by money asc  limit $start,$perpage");
		
	// }
	// public function count_right_by_uid($uid){
	// 	return DB::result_first("select count(*)  from ".DB::table($this->_table)." where uid=$uid and guessmoney>0");
	// }
	// public function delete_ql($time){
	// 	return DB::query("delete from ".DB::table($this->_table)." where guesstime<'".$time."'");
	// }
	
	
}


?>