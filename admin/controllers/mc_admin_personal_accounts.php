<?

class MC_Admin_Personal_Accounts extends MC_Admin{
	public function view(){
		$data = array();
		
		$data['title'] = 'Лицевые счета';
		
		echo $this->render(DIR_TEMPLATE_ADMIN . 'admin_page_personal_accounts.tpl', $data);
	}
}