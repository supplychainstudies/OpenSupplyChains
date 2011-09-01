<?php defined('SYSPATH') or die('No direct script access.');

class Database_PDO extends Kohana_Database_PDO {

	public function query($type, $sql, $as_object) {
		$result = parent::query($type, $sql, $as_object);
		FirePHP_Profiler::instance()->query($result, $type, $sql);
		return $result;
	}

}

