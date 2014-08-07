<?php

/**
 * Class Containing quick functions that does specific tasks or is connected to current user
 * @author Stephan Wessels
 *
 */
class G2_User {

	static $instance = null;
	private $user_model = null;
	private $user = null;

	/**
	 * Function creates user in database
	 * @param string $username
	 * @param string $password
	 */
	private function create_user($username, $password, $field_data = array()) {
		$new_user = clone $this->user_model;
		$new_user->username = $username;
		$new_user->password = $this->hash_pass($password);
		$new_user->sharedGroup = [$this->get_default_group()];


		foreach ($field_data as $field_key => $field_value) {
			$new_user->{$field_key} = $field_value;
		}

		R::store($new_user);
		return $new_user;
	}

	public function get_user() {
		$this->init_user_session();
		return $this->user;
	}

	public function get_default_group() {
		$group = R::findOne('group', 'name = :name', ['name' => 'default']);
		if (empty($group)) {
			$group = Mvc_Db::dispense('group');
			$group->name = 'default';
		}

		return $group;
	}

	public function load_group($name) {
		if (!empty($name)) {
			$group = R::findOne('group', 'name = :name', ['name' => $name]);
			if (empty($group)) {
				$group = Mvc_Db::dispense('group');
				$group->name = $name;
			}

			return $group;
		} else
			return false;
	}

	/**
	 * Checks if the current user is a public user
	 *
	 * @param type $group
	 * @return boolean
	 */
	public function is_group($group_n) {
		$user = $this->get_user();
		if (!$user || !$user->group) {
			$group = $this->load_group('Public');
		} else {
			$group = $user->group;
		}
		if ($group->name == $group_n) {
			return true;
		} else
			return false;
	}

	/**
	 * hashes a given password
	 * @param string $password
	 * @return string
	 */
	public function hash_pass($password) {
		return md5(PASS_SALT . $password);
	}

	public function create_user_if_not_exist($username, $password, $field_data = array()) {
		if (!R::find('user', "username = '$username'")) {
			$this->create_user($username, $password, $field_data);
		}
	}

	public function create_user_folder($user) {

	}

	public static function init() {
		global $g_instance;
		$g_instance = new self();
		$g_instance->init_user_session();
	}

	function __construct() {
		//Load all user attributes from config file plus add default ones
//		$fields = bootloader()->get_section('user_model');
//		$fields = $fields['fields'];
		$fields = array();
		//add required fields
		$required = array('username', 'password',);
		$fields = array_unique(array_merge($fields, $required));

		$this->user_model = R::dispense('user');
		foreach ($fields as $field) {
			$this->user_model->{$field} = null;
		}
//
	}

	/**
	 * Initiliazes user session
	 */
	private function init_user_session() {
		if (isset($_SESSION['user_object'])) {

			$user = R::findOne('user', 'id = :id', array('id' => $_SESSION['user_object']));
			$this->user = $user;
		}
	}

	public function log_in_user($user) {
		$this->user = $user;
		$_SESSION['user_object'] = $user->id;
	}

	public function check_login($user, $password) {
		$user = R::findOne('user', 'username = :username', ['username' => $user]);
		if (!empty($user) && $this->hash_pass($password) == $user->password) {
			return $user;
		} else {
			return false;
		}
	}

	public function logged_in() {
		if ($this->user)
			return true;
		else
			return false;
	}

	function __destruct() {
		if ($this->user) {
			R::store($this->user);
		}
	}

}

/**
 *
 * @global G $g_instance
 * @return G2_User
 */
function G() {
	global $g_instance;
	if (!$g_instance) {
		$g_instance = new G();
	}

	return $g_instance;
}
