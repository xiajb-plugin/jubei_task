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

class table_jubei_task_message extends discuz_table
{
	public function __construct() {

		$this->_table = 'jubei_task_message';
		$this->_pk    = 'id';

		parent::__construct();
	}

    //插入一条记录    
    public function insert($data) {
        return DB::insert($this->_table,$data);
    }

	#更新剩余名额
	public function update_by_id($data,$uid) {
        return DB::update($this->_table,$data, 'uid=' . $uid);
    }

	public function fetch_by_uid($uid){
		// return DB::fetch_first('SELECT * FROM %t WHERE id=%d', array($this->_table, $id));
		return DB::fetch_first("select * from ".DB::table($this->_table)." where uid =".$uid);
	}

}


?>