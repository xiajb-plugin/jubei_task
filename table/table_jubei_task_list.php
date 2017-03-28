<?php
/**
 *      [Liangjian] (C)2001-2099 Liangjian Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: table_jubei_task_list.php liangjian $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_jubei_task_list extends discuz_table
{
	public function __construct() {

		$this->_table = 'jubei_task_list';
		$this->_pk    = 'id';

		parent::__construct();
	}
	public function fetch_extcredits($uid){
		return DB::result_first("select extcredits2 from ".DB::table('common_member_count')." where uid=".$uid);
	}

	// public function count_by_uid($uid){
	// 	return DB::result_first("select count(*) from ".DB::table($this->_table)." where islog=0 and uid=".$uid);
	// }
	public function fetch_all_by_loglist($start,$limit){
		return DB::fetch_all("select *  from ".DB::table($this->_table)." where islog=1 order by id desc limit $start,$limit");
	}
	public function fetch_all_by_ql(){
		return DB::fetch_all("select *  from ".DB::table($this->_table)." where islog=0 order by id asc ");
	}
	public function count_by_loglist(){
		return DB::result_first("select count(*)  from ".DB::table($this->_table)." where islog=1 ");
	}
	public function delete_ql($time){
		return DB::query("delete from ".DB::table($this->_table)." where islog='1' and endtime<'".$time."'");
	}



	// public function fetch_all_by_uid($start,$limit){
	// 	return DB::fetch_all("select *  from ".DB::table($this->_table)." group by uid order by id desc limit $start,$limit");
	// }


	public function fetch_all_by_uid_log($uid,$start,$limit){
		return DB::fetch_all("select *  from ".DB::table($this->_table)." where islog=0 and uid=".$uid." order by id desc limit $start,$limit");
	}

	public function count_by_log(){
		return DB::result_first("select count(*)  from ".DB::table($this->_table)." where islog=0 ");
	}



	    //插入一条投票记录    
	    public function insert($data) {
	        return DB::insert($this->_table,$data);
	    }
	    #根据uid查询总数据
	public function count_by_uid($uid){
		return DB::result_first("select count(*) from ".DB::table($this->_table)." where islog=0 and uid=".$uid);
	}
	
	#根据taskid查出所有的数据
	public function fetch_by_id($tid){
		// return DB::fetch_first('SELECT * FROM %t WHERE id=%d', array($this->_table, $id));
		return DB::fetch_first("select * from ".DB::table($this->_table)." where id =".$tid);
	}
	// public function count_by_cid($cid){
	// 	return DB::result_first("select * from ".DB::table($this->_table)." where id=".$cid);
	// }

	# 根据uid取出分页数据
	public function fetch_all_by_uid($uid,$start,$limit){
		return DB::fetch_all("select *  from ".DB::table($this->_table)." where islog=0 and uid=".$uid." order by id desc limit $start,$limit");
	}

	#取出所有的数据，分页
	public function fetch_all_by_log($start,$limit){
		return DB::fetch_all("select *  from ".DB::table($this->_table)." where islog=0 order by id desc limit $start,$limit");
	}

	#更新剩余名额
	public function update_by_id($data,$taskid) {
        return DB::update($this->_table,$data, 'id=' . $taskid);
    }


}


?>