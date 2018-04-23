<?

class MC_Admin_Services extends MC_Admin{
	public function view(){
		$data = array();
		
		$data['title'] = 'Поставщики услуг';
		
		echo $this->render(DIR_TEMPLATE_ADMIN . 'admin_page_services.tpl', $data);
	}
}