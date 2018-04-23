<?

function print_pre($data){
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}

require_once './includes/SafeMySQL.php';
$mydb = new SafeMySQL();

require_once './admin/MC_Import_File_Test.php';

$xml = simplexml_load_file('./uploads/management-company.xml', 'SimpleXMLElement', LIBXML_COMPACT);
	
	$import = new MC_Import_File($xml);
	$import->opt_period = 1;
	$import->opt_receipt = 1;
	$import->create_user = 1;
	$result = $import->import();
	
print_pre($result);
?>
