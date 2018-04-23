<?

class MC_Admin_Meters_Download extends MC_Admin{
	public function view(){
		$data = array();
		
		$data['title'] = 'Выгрузка счётчиков';
		
		echo $this->render(DIR_TEMPLATE_ADMIN . 'admin_page_meters_download.tpl', $data);
	}
}