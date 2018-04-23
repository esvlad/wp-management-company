<?
class MC_User extends MC_Core{
	private $user_cookie;

	public function get_user_id($data = array()){
		$table_name = $this->prefix . 'mc_user';
		$sql = 'SELECT `id` FROM ?n WHERE `login`=?s AND `password`=?s';

		$user_id = $this->mydb->query($sql, $table_name, $data['mc_name'], $data['mc_password']);

		if(isset($user_id)){
			$sql_update = 'UPDATE ?n SET ?u WHERE `id` = ?i';

			$this->mydb->query($sql_update, $table_name, array('checkword_time'=>date('Y-m-d H:i:s')), (int)$user_id);

			$user_cookie = $this->set_user_auth_cookie($data['mc_name'], $data['mc_password']);

			return $user_cookie;
		} else {
			return false;
		}
	}

	private function set_user_auth_cookie($login, $password){
		$pass = new PasswordHash(8, true);
		$code = $pass->HashPassword(trim($password));

		$user_cookie = $login . 'solyanka' . $code;

		#add_action( 'init', array($this, 'set_mc_cookie') );

		return $user_cookie;
	}

	public function get_account_info($user_id){
		$table_pa = $this->prefix . 'mc_personal_account';
		
		$data = $this->mydb->getRow('SELECT * FROM ?n WHERE `user_id`=?i', $table_pa, (int)$user_id);
		
		unset($data['password_1c']);

		$data['mc_object'] = $this->get_object_info($data['object_id']);

		return $data;
	}

	protected function get_object_info($object_id){
		return $this->mydb->getRow('SELECT * FROM ?n WHERE `id`=?i', $this->prefix . 'mc_object', $object_id);
	}
}