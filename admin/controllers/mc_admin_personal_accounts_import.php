<?

class MC_Admin_Personal_Accounts_Import extends MC_Admin{
	public function view(){
		$data = array();
		
		$data['title'] = 'Импорт лицевых счетов';
		$data['pre'] = DIR_TEMPLATE_ADMIN;
		
		$data['script'][] = array('src'=> MC_Admin::MC_ADMIN_JS . 'mc.js');
		
		echo $this->render(DIR_TEMPLATE_ADMIN . 'page/personal_account/import.tpl', $data);
	}
}