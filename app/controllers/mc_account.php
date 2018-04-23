<?

require_once MC_PLUGIN_DIR . 'app/models/mc_user.php';

class MC_Account extends MC_Core{
	public function view($params = array()){
		$data = array();

		$data['title'] = $params['atts']['data-page'];

		#$data['script'][] = array('src'=> MC_Admin::MC_ADMIN_JS . 'mc.js');

		$user_id = $this->is_mc_user();

		if($user_id){
			$mc_user = new MC_User();

			$data['mc_user'] = $mc_user->get_account_info($user_id);
			
			$data['mc_btn_links'] = $this->get_url_account();

			echo $this->render(DIR_TEMPLATE_USER . 'page/account/view.tpl', $data);
		} else {
			$data['action_auth'] = '../lk?action=login';

			echo $this->render(DIR_TEMPLATE_USER . 'page/account/auth.tpl', $data);
		}
	}

	public function login(){
		if(empty($_POST['mc_name']) || empty($_POST['mc_password'])) return false;

		$mc_user = new MC_User();
		
		$user = $mc_user->get_user_id($_POST);

		$reload_url = get_site_url(null, 'lk');

		if(isset($user)){
			echo "<script>";
			echo "var date = new Date(new Date().getTime() + 60 * 1000 * 60 * 24 * 30);";
			echo "document.cookie = 'mc_user=$user; path=/; expires=' + date.toUTCString();";
			echo "document.location.href = '$reload_url'";
			echo "</script>";
			wp_die();
		}

		return false;
	}
}