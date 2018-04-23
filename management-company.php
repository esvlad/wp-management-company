<?
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @wordpress-plugin
 * Plugin Name:       Управляющая компания
 * Plugin URI:        https://github.com/esvlad/management-company
 * Description:       Плагин для управляющей компании.
 * Version:           0.0.1
 * Author:            ES Vlad
 * Author URI:        https://es.vlad
 * License:           MIT
 */
 
define ('MC_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define ('DIR_TEMPLATE_ADMIN', plugin_dir_path( __FILE__ ) . 'admin/view/');
define ('DIR_TEMPLATE_USER', plugin_dir_path( __FILE__ ) . 'app/view/');
define ('MC_PLUGIN_URL', plugins_url('', __FILE__));

require_once MC_PLUGIN_DIR . '/includes/MC_Configuration.php';

if ( ! defined( 'WPINC' ) ) {
	die;
}

register_activation_hook( __FILE__, array('MC_Configuration','activate') );
register_deactivation_hook( __FILE__, array('MC_Configuration','deactivate') );
register_uninstall_hook(__FILE__, array('MC_Configuration','uninstall'));

$mydb_param = array(
	'host' => DB_HOST,
	'user' => DB_USER,
	'pass' => DB_PASSWORD,
	'db' => DB_NAME
);

require_once MC_PLUGIN_DIR . 'includes/SafeMySQL.php';
$mydb = new SafeMySQL($mydb_param);

require_once MC_PLUGIN_DIR . 'app/MC.php';

function run_mc_app() {
	$plugin = new MC();
	$plugin->run();
}

function my_upload_mimes() {
	$mime_types = array(
		'xml'   => 'text/xml'
	);
return $mime_types;
}
add_filter( 'upload_mimes', 'my_upload_mimes' );


add_action('wp_ajax_file_import', 'mc_file_import');
function mc_file_import() {
	global $wpdb;
	require_once MC_PLUGIN_DIR . 'admin/MC_Import_File.php';
	
	$file = &$_FILES['file_import'];

	$tmp_file = file_get_contents($file['tmp_name']);
	
	$xml = simplexml_load_file($file['tmp_name'], 'SimpleXMLElement', LIBXML_COMPACT);
	
	$import = new MC_Import_File($xml);
	$import->db_prefix = $wpdb->prefix;
	$import->opt_period = $_POST['period'];
	$import->opt_receipt = $_POST['receipt'];
	$import->create_user = $_POST['create_user'];
	$result = $import->import();
	
	$hand = fopen(MC_PLUGIN_DIR . '\\uploads\\imp.xml','c');
	
	foreach($result as $key => $value){
		$str = "$key => ";
		
		if(is_array($value)){
			$str .= "array ( \n";
			
			foreach($value as $k => $v){
				$str .= " $k => $v,\n";
			}
			
			$str .= "),\n";
		} else {
			$str .= "$value,\n";
		}
		
		fwrite($hand, $str);
	}
	
	fclose($hand);
	
	echo json_encode($result, JSON_UNESCAPED_UNICODE);

	wp_die(); // выход нужен для того, чтобы в ответе не было ничего лишнего, только то что возвращает функция
}

run_mc_app();
?>