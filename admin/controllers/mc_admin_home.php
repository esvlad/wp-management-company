<?

class MC_Admin_Home extends MC_Admin{
	public function view(){
		$data = array();
		
		$data['title'] = 'Счетчики';
		
		echo $this->render(DIR_TEMPLATE_ADMIN . 'admin_page_meters.tpl', $data);
	}
}