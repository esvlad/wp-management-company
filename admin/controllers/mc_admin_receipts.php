<?

class MC_Admin_Receipts extends MC_Admin{
	public function view(){
		$data = array();
		
		$data['title'] = 'Квитанции';
		
		echo $this->render(DIR_TEMPLATE_ADMIN . 'admin_page_receipts.tpl', $data);
	}
}