<?

include ABSPATH . 'wp-includes/class-phpass.php';

class MC{
	protected $db;
	protected $mydb;
	protected $plugin_dir;
	protected $prefix;
	protected $user_id;
	
	public function __construct(){
		global $wpdb;
		global $mydb;
		
		$this->db = $wpdb;
		$this->mydb = $mydb;
		$this->plugin_dir = plugin_dir_url( __FILE__ );
		$this->prefix = $wpdb->prefix;
	}
	
	public function run(){
		if(is_admin()){
			require_once MC_PLUGIN_DIR . 'admin/mc_admin.php';
			
			$this->run_admin();
			wp_enqueue_style('management_company_style_admin_css', MC_PLUGIN_URL . '/admin/view/css/style.css');
			//wp_enqueue_script('management_company_admin_js', plugin_dir_url( __FILE__ ) . 'view/admin/js/mc.js');
		} else {
			require_once MC_PLUGIN_DIR . 'app/mc_core.php';
			$this->run_app();
		}
	}
	
	public function run_admin(){
		add_action('admin_menu', array($this, 'add_menu_admin'));
		
		return true;
	}
	
	public function add_menu_admin(){
		$admin_class = new MC_Admin();
		
		add_menu_page('Управляющая компания','Управляющая компания',5,'mc_admin_home',array('MC_Admin', 'get_page'),'dashicons-admin-post',26);
		
		add_submenu_page('mc_admin_home','Объекты управления','Объекты управления', 5,'mc_admin_objects',array('MC_Admin', 'get_page'));
		add_submenu_page('mc_admin_home','Поставщики','Поставщики', 'manage_options','mc_admin_services',array('MC_Admin', 'get_page'));
		add_submenu_page('mc_admin_home','Дома','Дома', 'manage_options','mc_admin_house',array('MC_Admin', 'get_page'));
		add_submenu_page('mc_admin_home','Лицевые счета','Лицевые счета', 'manage_options','mc_admin_personal_accounts',array('MC_Admin', 'get_page'));
		add_submenu_page('mc_admin_home','Импорт счетов','Импорт счетов', 'manage_options','mc_admin_personal_accounts_import',array('MC_Admin', 'get_page'));
		add_submenu_page('mc_admin_home','Счетчики','Счетчики', 'manage_options','mc_admin_meters',array('MC_Admin', 'get_page'));
		add_submenu_page('mc_admin_home','Счетчики выгрузка','Счетчики выгрузка', 'manage_options','mc_admin_meters_download',array('MC_Admin', 'get_page'));
		add_submenu_page('mc_admin_home','Периоды','Периоды', 'manage_options','mc_admin_periods',array('MC_Admin', 'get_page'));
		add_submenu_page('mc_admin_home','Квитанции','Квитанции', 'manage_options','mc_admin_receipts',array('MC_Admin', 'get_page'));
	}
	
	public function run_app(){
		$app = new MC_Core();

		add_shortcode('mc_app', array('MC_Core', 'get_page'));
		add_filter('body_class',array('MC_Core', 'get_body_class'));
		wp_enqueue_style('management_company_style_css', MC_PLUGIN_URL . '/app/view/css/style.css');

		return false;
	}
	
	protected function render($template, $data = null){
		$file = $template;
		
		if (file_exists($file)) {
			if(isset($data)) extract($data);
			
			ob_start();

			require($file);

			$output = ob_get_contents();

			ob_end_clean();
		} else {
			echo '<h3>Отсутствует файл шаблона - <b>' . $file . '</b>!</h3>';
			exit();
		}

		return $output;
	}

	protected function pre_print($data){
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}

	protected function is_mc_user(){
		if(!empty($_COOKIE['mc_user'])){
			$data_cookie_user = explode('solyanka', $_COOKIE['mc_user']);

			$login = $data_cookie_user[0];
			$password = trim($data_cookie_user[1]);

			$pass = new PasswordHash(8, true);

			$table_name = $this->prefix . 'mc_user';
			$sql_code = 'SELECT `code` FROM ?n WHERE `login`=?s';

			$check_code = $this->mydb->getOne($sql_code, $table_name, $login);

			$check_pass = $pass->CheckPassword(trim($check_code), $password);

			if($check_pass === true){
				$sql = 'SELECT `id` FROM ?n WHERE `login`=?s';

				$this->user_id = $this->mydb->getOne($sql, $table_name, $login);
			}

			if(isset($this->user_id)){
				return $this->user_id;
			} else {
				unset($_COOKIE['mc_user']);
				return false;
			}
		}

		return false;
	}
}