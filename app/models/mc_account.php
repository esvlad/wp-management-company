<?

class MC_Account extends MC_Core{
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