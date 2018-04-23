<?
class MC_Core extends MC{
	const MC_ADMIN_JS = MC_PLUGIN_URL . '/app/view/js/';
	const MC_ADMIN_CSS = MC_PLUGIN_URL . '/app/view/css/';
	const MC_ADMIN_TPL = MC_PLUGIN_URL . '/app/view/';
	
	public function get_page($atts, $content, $tag){
		$class = $atts['data-page'];
		
		require_once MC_PLUGIN_DIR . 'app/controllers/' . $class . '.php';
		
		$page = new $class();
		$action = (isset($_GET['action'])) ? $_GET['action'] : 'view';

		$data = array();
		$data['atts'] = $atts;
		$data['content'] = $content;
		$data['tag'] = $tag;
		
		return $page->$action($data);
	}

	public function get_body_class($classes){
		$classes[] = 'mc_page';

		return $classes;
	}
	
	protected function get_model($model, $data = array()){
		require_once MC_PLUGIN_DIR . 'app/models/' . $model . '.php';
		
		$model = new $model();
		
		return $model;
	}
	
	protected function get_action_link($page, $action = 'add', $element = false){
		$url = $page . '&action=' . $action;
		
		if(!empty($element)) $url .= '&' . $element . '=';
		
		return $url;
	}
	
	protected function get_url_account(){
		$links = array();
		
		$site_url = get_site_url(null, 'lk');
		
		$links[] = array(
			'name' => 'Профиль',
			'page' => $site_url
		);
		
		$links[] = array(
			'name' => 'Оборотная ведомость',
			'page' => $site_url . '/receipts'
		);
			
		$links[] = array(
			'name' => 'Данные счетчиков',
			'page' => $site_url . '/personal_meters'
		);
		
		$links[] = array(
			'name' => 'Оплата услуг',
			'page' => $site_url . '/payments'
		);
		
		$links[] = array(
			'name' => 'Мои заявки',
			'page' => $site_url . '/tikets'
		);
		
		$links[] = array(
			'name' => 'Выход',
			'page' => $site_url . '?action=logout'
		);
		
		return $links;
	}
}
?>