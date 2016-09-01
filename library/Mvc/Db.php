<?php

include_once SYSTEM_DIR . '/includes/rb.php';

class Mvc_Db extends R {

	private static $dsn, $username, $password, $frozen, $connected, $page_count, $current_page;

	public static function setup_db($dsn = NULL, $username = NULL, $password = NULL, $frozen = FALSE) {
		self::$dsn = $dsn;
		self::$username = $username;
		self::$password = $password;
		self::$frozen = $frozen;
	}

	public static function setup() {
		if (self::$connected == false) {
			parent::setup(self::$dsn, self::$username, self::$password, self::$frozen);
			self::$connected = true;
		}
	}

	/**
	 *
	 * @param type $type
	 * @param type $limit
	 * @param type $sql
	 * @param type $bindings
	 * @return type
	 */
	public static function paginate_findAll($type, $limit, $sql = NULL, $bindings = array()) {

		//Get the current Page
		if (isset($_GET['p'])) {
			$page = $_GET['p'];
		} else
			$page = 1;
		self::$current_page = $page;
		self::$page_count = ceil(R::count($type, $sql, $bindings) / $limit);
		return self::findAll($type, $sql . ' LIMIT ' . (($page - 1) * $limit ) . ', ' . $limit, $bindings);
	}

	public static function paginate_query($sql, $limit) {
		if (isset($_GET['p'])) {
			$page = $_GET['p'];
		} else
			$page = 1;
		self::$current_page = $page;
		$data = self::getAll($sql . ' LIMIT ' . (($page - 1) * $limit ) . ', ' . $limit, $bindings);
		self::$page_count = ceil(count(self::getAll($sql)) / $limit);
		return $data;
	}

	/**
	 * Returns the last paginate_findAll page count
	 * @return type
	 */
	public static function get_last_total_pages() {
		return self::$page_count;
	}

	public static function get_current_page() {
		return self::$current_page;
	}

}
