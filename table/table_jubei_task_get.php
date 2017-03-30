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

class table_jubei_task_get extends discuz_table
{
	public function __construct() {

		$this->_table = 'jubei_task_get';
		$this->_pk    = 'id';

		parent::__construct();
	}
	public function range_by_uid($uid,$start,$limit){
		// return DB::fetch_all("select *  from ".DB::table($this->_table)." where uid=$uid order by desc limit $start,$limit");	
		return DB::fetch_all("select *  from ".DB::table($this->_table)." where uid=".$uid." order by id desc limit $start,$limit");		
	}

	# 根据uid取出一共有多少条数据
	public function count_by_uid($uid){
		return DB::result_first("select count(*) from ".DB::table($this->_table)." where uid=".$uid);
	}

	# 插入数据
	    public function insert($data) {
	        return DB::insert($this->_table,$data);
	    }
	# 根据uid分页查出数据
	public function fetch_all_by_uid($uid,$start,$limit){
		return DB::fetch_all("select *  from ".DB::table($this->_table)." where uid=" .$uid. " order by id desc limit $start,$limit");
	}


	#根据uid和task_id联合查询查询总数据
	public function count_by_uid_taskid($uid,$taskid){
		return DB::result_first("select count(*) from ".DB::table($this->_table)." where uid=$uid and taskid=$taskid");
	}
	#根据uid和task_id联合查询查询分页数据
	public function fetch_all_by_uid_taskid($uid,$taskid,$start,$limit){
		return DB::fetch_all("select *  from ".DB::table($this->_table)." where uid=".$uid." and taskid=".$taskid." order by id desc limit $start,$limit");
	}

	#根据task_id查询查询总数据
	public function count_by_taskid($taskid){
		return DB::result_first("select count(*) from ".DB::table($this->_table)." where taskid=$taskid");
	}
	#根据task_id查询查询分页数据
	public function fetch_all_by_taskid($taskid,$start,$limit){
		return DB::fetch_all("select *  from ".DB::table($this->_table)." where taskid=".$taskid." order by id desc limit $start,$limit");
	}

	
}


?>