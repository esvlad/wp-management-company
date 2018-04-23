<?

class MC_Admin_Periods extends MC_Admin{
	public function view(){
		$data = array();
		
		$data['title'] = 'Периоды';
		
		echo $this->render(DIR_TEMPLATE_ADMIN . 'admin_page_periods.tpl', $data);
	}
}