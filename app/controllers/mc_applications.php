<?

class MC_Account extends MC_Core{
	public function view($params = array()){
		$data = array();

		$data['title'] = $params['atts']['data-page'];
		$data['get'] = $_GET;
		
		echo $this->render(DIR_TEMPLATE_USER . 'page/account/view.tpl', $data);
	}
}