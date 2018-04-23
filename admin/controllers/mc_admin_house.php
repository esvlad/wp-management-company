<?

class MC_Admin_House extends MC_Admin{
	public function view(){
		$data = array();
		
		$data['title'] = 'Жилкомсервис - Дома';
		
		echo $this->render(DIR_TEMPLATE_ADMIN . 'admin_page_house.tpl', $data);
	}
}