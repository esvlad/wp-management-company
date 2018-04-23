<?

class MC_Admin extends MC{
	const MC_ADMIN_JS = MC_PLUGIN_URL . '/admin/view/js/';
	const MC_ADMIN_CSS = MC_PLUGIN_URL . '/admin/view/css/';
	const MC_ADMIN_TPL = MC_PLUGIN_URL . '/admin/view/';
	
	public function get_page(){
		$class = ($_GET['page'] != 'mc_admin_home') ? $_GET['page'] : 'mc_admin_personal_accounts_import';
		
		require_once MC_PLUGIN_DIR . 'admin/controllers/' . $class . '.php';
		
		$page = new $class();
		$action = (isset($_GET['action'])) ? $_GET['action'] : 'view';
		
		return $page->$action();
	}
	
	protected function get_model($model, $data = array()){
		require_once MC_PLUGIN_DIR . 'admin/models/' . $model . '.php';
		
		return new $model();
	}
	
	protected function get_action_link($page, $action = 'add', $element = false){
		$url = 'admin.php?page=' . $page . '&action=' . $action;
		
		if(!empty($element)) $url .= '&' . $element . '=';
		
		return admin_url($url);
	}
}
?>