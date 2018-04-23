<?

include ABSPATH . 'wp-includes/class-phpass.php';

class MC_Import_File{
	private $xml;
	private $prefix;
	private $object_id;
	private $period_id;
	private $receipt;
	private $mydb;
	public $db_prefix;
	public $opt_period;
	public $opt_receipt;
	public $create_user;
	
	public function __construct($file){
		$this->xml = $file;
		$this->mydb = $GLOBALS['mydb'];
	}
	
	public function import(){
		$org = $this->import_org();
		
		if(!empty($org['error'])){	
		
			return $org;
			
		} else {
			$object_id = (int)$org['id'];
			
			$contractors = $this->import_contractors();

			$period_id = $this->import_period($object_id, $this->opt_period);
			
			$hmeters = $this->import_hmeters($object_id, $period_id);
			
			$personal_account = $this->import_personal_accounts($object_id);
			
			$receipts = $this->import_receipt($object_id, $period_id, $this->opt_receipt);

			$receipts_items = $this->import_receipts_items($object_id, $period_id, $receipts, $this->opt_receipt);
			$receipts_meters = $this->import_receipts_meters($object_id, $period_id, $receipts, $this->opt_receipt);	
			$receipts_contractors = $this->import_receipts_contractors($object_id, $period_id, $receipts, $this->opt_receipt);
			
			$json = array(
				'contractors' => $contractors,
				'hmeters' => $hmeters,
				'personal_account' => $personal_account,
				'receipts' => count($receipts),
				'receipts_items' => $receipts_items,
				'receipts_meters' => $receipts_meters,
				'receipts_contractors' => $receipts_contractors
			);
			
			return $json;
		}
	}
	
	private function import_org(){
		$org_name = trim($this->xml['name']);
		
		$org_data = array(
			'bank' => $this->xml['bank'],
			'inn' => $this->xml['inn'],
			'kpp' => $this->xml['kpp'],
			'bik' => $this->xml['bik'],
			'rs' => $this->xml['rs'],
			'ks' => $this->xml['ks']
		);
		
		$data = array();
		
		$table_name = $this->db_prefix . 'mc_object';
		
		$org = $this->mydb->getRow('SELECT `id`, `bank`, `inn`, `kpp`, `bik`, `rs`, `ks` FROM ?n WHERE `name` = ?s', $table_name, $org_name);
		
		if(isset($org)){
			foreach($org as $key => $value){
				if($key == 'id') break;
				
				if($org_data[$key] == $value) unset($org_data[$key]);
			}
			
			if(count($org_data) > 0){
				$this->mydb->query('UPDATE ?n SET ?u WHERE `name` = ?s', $table_name, $org_data, $org_name);
			}
			
			$data['id'] = $org['id'];
		} else {
			$data['error'] = array(
				'response' => true,
				'message' => 'Не найдено управляющей компании!'
			);
		}
		
		return $data;
	}
	
	private function import_contractors(){
		$table_name = $this->db_prefix . 'mc_contractors';
		
		$c_update = 0;
		$c_insert = 0;
		foreach($this->xml->contractors->contractor as $contractor){
			if(!empty($contractor['executor']) && $contractor['executor'] == 1){
				$executor = 1;
			} else {
				$executor = 0;
			}
			
			$contractors = array(
				'xml_id' => $contractor['id'],
				'name' => htmlspecialchars(trim($contractor['name'])),
				'services' => $contractor['services'],
				'body' => $contractor->__toString(),
				'executor' => $executor,
				'address' => $contractor['address'],
				'phone' => $contractor['phone'],
				'bank' => $this->isset_or_null($contractor['bank']),
				'inn' => $this->isset_or_null($contractor['inn']),
				'kpp' => $this->isset_or_null($contractor['kpp']),
				'bik' => $this->isset_or_null($contractor['bik']),
				'rs' => $this->isset_or_null($contractor['rs']),
				'ks' => $this->isset_or_null($contractor['ks']),
			);
			
			if($executor == 1){
				$sql = 'SELECT * FROM ?n WHERE `xml_id`=?s AND `executor`=?i';
				$contractor_db = $this->mydb->getRow($sql, $table_name, $contractor['id'], $executor);
			} else {
				$sql = 'SELECT * FROM ?n WHERE `xml_id`=?s AND `services`=?s';
				$contractor_db = $this->mydb->getRow($sql, $table_name, $contractor['id'], $contractor['services']);
			}
			
			if(isset($contractor_db)){
				foreach($contractor_db as $key => $value){
					if($key == 'id') break;
					
					if($contractors[$key] == $value) unset($contractors[$key]);
				}
				
				unset($contractors['executor']);
				
				if(count($contractors) > 0){
					$update = 'UPDATE ?n SET ?u WHERE `id` = ?i';
					$this->mydb->query($update, $table_name, $contractors, $contractor_db['id']);
					
					$c_update++;
				}
			} else {
				$insert = 'INSERT INTO ?n SET ?u';
				$this->mydb->query($insert, $table_name, $contractors);
				
				$c_insert++;
			}
		}
		
		$contractor_data = array(
			'count_all' => count($this->xml->contractors->contractor),
			'count_insert' => $c_insert,
			'count_update' => $c_update
		);
		
		return $contractor_data;
	}
	
	private function import_period($org_id, $option){
		if($option == 1){
			$period_id = $this->add_period($org_id);
		} else {
			$table_name = $this->db_prefix . 'mc_periods';
			$period_id = $this->mydb->getOne('SELECT `id` FROM ?n WHERE `object_id`=?i ORDER BY `id` DESC LIMIT 0,1', $table_name, $org_id);
		}
		
		return $period_id;
	}
	
	private function add_period($org_id){
		$table_org = $this->db_prefix . 'mc_object';
		$org_periods = $this->mydb->getRow('SELECT `period_start`, `period_end` FROM ?n WHERE `id`=?i', $table_org, $org_id);
		
		$get_next_date = getdate(mktime(0, 0, 0, date("m")+1, date("d"), date("Y")));
		
		$period_month_name_rus = array(
			'1' => 'январь',
			'2' => 'февраль',
			'3' => 'март',
			'4' => 'апрель',
			'5' => 'май',
			'6' => 'июнь',
			'7' => 'июль',
			'8' => 'август',
			'9' => 'сентябрь',
			'10' => 'октябрь',
			'11' => 'ноябрь',
			'12' => 'декабрь'
		);
		
		$period = array(
			'object_id' => $org_id,
			'date_start' => date('Y-n-') . $org_periods['period_start'],
			'date_end' => $get_next_date['year'] . '-' . $get_next_date['mon'] . '-' . $org_periods['period_end'],
			'name' => $period_month_name_rus[date('n')] . ' ' . date('Y'),
			'date_create' => date('Y-m-d')
		);
		
		$table_name = $this->db_prefix . 'mc_periods';
		$this->mydb->query('INSERT INTO ?n SET ?u', $table_name, $period);
		$period_id = $this->mydb->insertId();
		
		return $period_id;
	}
	
	private function import_hmeters($object_id, $period_id){
		$hmeters = array();
		$data = array();
		
		foreach($this->xml->hmeters->hmeter as $hmeter){
			$hmeters[trim($hmeter['kod'])] = array(
				'value_count' => (int)$hmeter['value_count'],
				'indiccur1' => $hmeter['indiccur1'],
				'kod' => trim($hmeter['kod']),
				'name' => $hmeter['name'],
				'service' => $hmeter['service'],
				'object_id' => $object_id,
				'period_id' => $period_id
			);
		}
		
		$data['count_all'] = count($hmeters);
		
		$table_name = $this->db_prefix . 'mc_hmeters';
		
		$is_period_hmeters = $this->mydb->getAll('SELECT `id`, `kod` FROM ?n WHERE `object_id`=?i AND `period_id`=?i', $table_name, $object_id, $period_id);
		
		if(!empty($is_period_hmeters)){
			foreach($is_period_hmeters as $is_meter){
				$this->mydb->query('UPDATE ?n SET ?u WHERE `id` = ?i', $table_name, $hmeters[$is_meter['kod']], $is_meter['id']);
			}
			
			$data['count_update'] = count($is_period_hmeters);
		} else {
			$data_hmeters = array();
			
			foreach($hmeters as $h_meter){
				$data_hmeters[] = $this->mydb->parse("(null,?i,?s,?i,?s,?s,?i,?i)", $h_meter['value_count'], $h_meter['indiccur1'], $h_meter['kod'], $h_meter['name'], $h_meter['service'], $object_id, $period_id);
			}
			
			$insert_hmeters = implode(",",$data_hmeters);
			
			$this->mydb->query('INSERT INTO ?n VALUES ?p', $table_name, $insert_hmeters);
			
			$data['count_insert'] = count($data_hmeters);
		}
		
		return $data;
	}
		
	private function import_personal_accounts($object_id){
		$personal_accounts = array();
		$insert_pa = array();
		$count_pa = 0;
		
		$table_name = $this->db_prefix . 'mc_personal_account';
		
		foreach($this->xml->PersAcc as $personal_account){
			$is_pa = $this->is_personal_account($personal_account['login']);
			
			$personal_accounts = array(
				'user_id' => $this->get_user_id($personal_account),
				'xml_id' => $personal_account['name_ls'],
				'object_id' => $object_id,
				'name' => $personal_account['name'],
				'name_ls' => $personal_account['name_ls'],
				'region' => $personal_account['AddressRegion'],
				'city' => $personal_account['AddressCity'],
				'street' => $personal_account['AddressStreet'],
				'district' => $personal_account['AddressDistrict'],
				'house' => $personal_account['AddressHouse'],
				'flat' => $personal_account['AddressFlat'],
				'flat_abbr' => $personal_account['FlatAbbr'],
				'settlement' => $personal_account['AddressSettlement'],
				'commonarea' => $this->price_replace($personal_account['commonarea']),
				'email' => $personal_account['email'],
				'habarea' => $this->price_replace($personal_account['habarea']),
				'kod_ls' => $personal_account['kod_ls'],
				'login' => $personal_account['login'],
				'people' => $personal_account['people'],
				'num_of_reg' => $personal_account['num_of_reg'],
				'password_1c' => $personal_account['password'],
				'active' => $this->is_personal_account_active($personal_account['password'])
			);
			
			if(empty($is_pa)){
				$insert_pa[] = $personal_accounts;
			} else {
				$xml_id = $personal_accounts['xml_id'];
				
				unset($personal_accounts['user_id']);
				unset($personal_accounts['xml_id']);
				unset($personal_accounts['login']);
				
				$sql = 'UPDATE ?n SET ?u WHERE `xml_id` = ?s';
				
				$this->mydb->query($sql, $table_name, $personal_accounts, $xml_id);
				$count_pa++;
			}
		}
		
		if(count($insert_pa) > 0){
			$data_pa = array();
			
			foreach($insert_pa as $field){
				$active = $this->is_personal_account_active($field['password']);
				$data_pa[] = $this->mydb->parse("(null,?i,?s,?i,?s,?s,?s,?s,?s,?s,?s,?i,?s,?s,?s,?s,?s,?s,?s,?i,?i,?s,?i,?i)", (int)$field['user_id'], $field['xml_id'], (int)$field['object_id'], $field['name'], $field['name_ls'], $field['region'], $field['city'], $field['street'], $field['district'], $field['house'], (int)$field['flat'], $field['flat_abbr'], $field['settlement'], $field['commonarea'], $field['email'], $field['habarea'], $field['kod_ls'], $field['login'], (int)$field['people'], (int)$field['num_of_reg'], $field['password_1c'], 1, $active);
			}
			
			$instr = implode(",",$data_pa);
			
			$this->mydb->query('INSERT INTO ?n VALUES ?p', $table_name, $instr);
		}
		
		$report_pa = array(
			'count_all' => count($this->xml->PersAcc),
			'count_update' => $count_pa,
			'count_insert' => count($insert_pa)
		);
		
		return $report_pa;
	}
	
	private function is_personal_account($login){
		$table_name = $this->db_prefix . 'mc_personal_account';
		return $this->mydb->getOne('SELECT * FROM ?n WHERE `login`=?s ORDER BY `id` LIMIT 0,1', $table_name, $login);
	}
	
	private function get_user_id($personal_account){
		$table_name = $this->db_prefix . 'mc_user';
		$user_id = $this->mydb->getOne('SELECT `id` FROM ?n WHERE `login`=?s', $table_name, $personal_account['login']);
		
		if(isset($user_id)){
			return $user_id;
		} else {
			return $this->add_user($personal_account);
		}
	}
	
	private function add_user($personal_account){
		$user_data = array(
			'login' => $personal_account['login'],
			'password' => $this->set_password($personal_account['password']),
			'xml_name' => $personal_account['name'],
			'email' => $personal_account['email'],
			'date_register' => date('Y-m-d H:i:s'),
			'street' => $personal_account['AddressStreet'],
			'city' => $personal_account['AddressCity'],
			'state' => $personal_account['AddressRegion'],
			'country' => 'Российская Федерация',
			'xml_id' => $personal_account['name_ls'],
			'active' => $this->is_personal_account_active($personal_account['password'])
		);
		
		$sql = 'INSERT INTO ?n SET ?u';
		$table_name = $this->db_prefix . 'mc_user';
		
		$this->mydb->query($sql, $table_name, $user_data);
		
		return $this->mydb->insertId();
	}
	
	private function set_password($code){
		if(isset($code) && $code != ''){
			$pass = new PasswordHash(8, true);
			$password = $pass->HashPassword(trim($code));
		} else {
			$password = null;
		}
		
		return $password;
	}
	
	private function is_personal_account_active($code = false){
		if(isset($code) && $code != ''){
			return 1;
		} else {
			return 0;
		}
	}
	
	private function import_receipt($object_id, $period_id, $option = 1){
		$receipt = array();
		$receipts = array();
		$kod_ls = array();
		$logins = array();
		$table_name = $this->db_prefix . 'mc_receipts';
		
		if($option == 1){
			foreach($this->xml->PersAcc as $personal_account){
				$receipt[trim($personal_account['login'])] = array(
					'object_id' => $object_id,
					'period_id' => $period_id,
					'kod_ls' => $personal_account['kod_ls']
				);
				
				$kod_ls[] = $personal_account['kod_ls'];
				$logins[] = $personal_account['login'];
			}
			
			//Аккаунты уже есть поэтому просто берем ID
			$table_user = $this->db_prefix . 'mc_user';
			$user_ids = $this->mydb->getAll('SELECT `id`, `login` FROM ?n WHERE `login` IN (?a)', $table_user, $logins);
			
			foreach($user_ids as $user){
				$receipt[$user['login']]['user_id'] = $user['id'];
			}
			
			$insert_receipt = array();
			foreach($receipt as $rec){
				$insert_receipt[] = $this->mydb->parse("(null,?i,?i,?i,?s)", $rec['object_id'], $rec['period_id'], $rec['user_id'], $rec['kod_ls']);
			}
			
			$instr = implode(",",$insert_receipt);
			
			$this->mydb->query('INSERT INTO ?n VALUES ?p', $table_name, $instr);
		} else {
			foreach($this->xml->PersAcc as $personal_account){
				$kod_ls[] = $personal_account['kod_ls'];
			}
		}
		
		$ind = 'kod_ls';
		$select = 'SELECT `id`, `kod_ls` FROM ?n WHERE `object_id`=?i AND `period_id`=?i AND `kod_ls` IN (?a)';
		$receipts = $this->mydb->getInd($ind, $select, $table_name, $object_id, $period_id, $kod_ls);
		
		$kd = array();
		foreach($receipts as $k => $v){
			$kd[] = $k;
		}
		$cd = implode(",",$kd);
		
		return $receipts;
	}
	
	private function import_receipts_items($object_id, $period_id, $receipts, $option = 1){
		$items = array();
		$receipts_id = array();
		$table_name = $this->db_prefix . 'mc_receipts_items';
		
		foreach($this->xml->PersAcc as $personal_account){
			foreach($personal_account->item as $item){
				$items[] = array(
					'receipts_id' => $receipts[(int)$personal_account['kod_ls']]['id'],
					'ammount' => $this->price_replace($item['ammount']),
					'correction' => $this->price_replace($item['correction']),
					'edizm' => trim($item['edizm']),
					'kod' => trim($item['kod']),
					'name' => $item['name'],
					'norm' => $this->price_replace($item['norm']),
					'peni' => $this->price_replace($item['peni']),
					'sum' => $this->price_replace($item['sum']),
					'sumpayed' => $this->price_replace($item['sumpayed']),
					'compensation' => $this->price_replace($item['compensation']),
					'sumtopay' => $this->price_replace($item['sumtopay']),
					'debtbeg' => $this->price_replace($item['debtbeg']),
					'debtend' => $this->price_replace($item['debtend']),
					'hmeter' => $this->isset_or_null($this->price_replace($item['hmeter'])),
					'hammount' => $this->isset_or_null($this->price_replace($item['hammount'])),
					'hnorm' => $this->isset_or_null($this->price_replace($item['hnorm'])),
					'hsum' => $this->isset_or_null($this->price_replace($item['hsum'])),
					'hsumtopay' => $this->isset_or_null($this->price_replace($item['hsumtopay'])),
					'volumeh' => $this->isset_or_null($this->price_replace($item['volumeh'])),
					'tarif1' => $this->price_replace($item['tarif1']),
					'tarif2' => $this->isset_or_null($this->price_replace($item['tarif2'])),
					'tarif3' => $this->isset_or_null($this->price_replace($item['tarif3']))
				);
			}
			
			if($option == 2){
				$receipts_id[] = $receipts[$personal_account['kod_ls']]['id'];
			}
		}
		
		if($option == 2){
			$delete = 'DELETE FROM ?n WHERE receipts_id IN (?a)';
			$this->mydb->query($delete, $table_name, $receipts_id);
		}
		
		$items_insert = array();
			
		foreach($items as $v_item){
			$items_insert[] = $this->mydb->parse("(null,?i,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s)", $v_item['receipts_id'], $v_item['ammount'], $v_item['correction'], $v_item['edizm'], $v_item['kod'], $v_item['name'], $v_item['norm'], $v_item['peni'], $v_item['sum'], $v_item['sumpayed'], $v_item['compensation'], $v_item['sumtopay'], $v_item['debtbeg'], $v_item['debtend'], $v_item['hmeter'], $v_item['hammount'], $v_item['hnorm'], $v_item['hsum'], $v_item['hsumtopay'], $v_item['volumeh'], $v_item['tarif1'], $v_item['tarif2'], $v_item['tarif3']);
		}
			
		$insert_items = implode(",",$items_insert);
		
		$this->mydb->query('INSERT INTO ?n VALUES ?p', $table_name, $insert_items);
		
		return true;
	}
	
	private function import_receipts_meters($object_id, $period_id, $receipts, $option = 1){
		$r_meters = array();
		$receipts_id = array();
		$table_name = $this->db_prefix . 'mc_receipts_meters';
		
		foreach($this->xml->PersAcc as $personal_account){
			foreach($personal_account->meter as $meter){
				$r_meters[] = array(
					'receipts_id' => $receipts[(int)$personal_account['kod_ls']]['id'],
					'value_count' => $meter['value_count'],
					'indicbef1' => (isset($meter['indicbef1'])) ? str_replace(" ","",$meter['indicbef1']) : null,
					'date_indicbef' => (isset($meter['date_indicbef'])) ? $this->get_date_ymd($meter['date_indicbef']) : null,
					'indiccur1' => (isset($meter['indiccur1'])) ? str_replace(" ","",$meter['indiccur1']) : null,
					'date_indiccur' => (isset($meter['date_indiccur'])) ? $this->get_date_ymd($meter['date_indiccur']) : null,
					'kod' => trim($meter['kod']),
					'name' => trim($meter['name']),
					'service' => trim($meter['service'])
				);
			}
			
			if($option == 2){
				$receipts_id[] = $receipts[(int)$personal_account['kod_ls']]['id'];
			}
		}
		
		if($option == 2){
			$delete = 'DELETE FROM ?n WHERE receipts_id IN (?a)';
			$this->mydb->query($delete, $table_name, $receipts_id);
		}
		
		$meters_insert = array();
			
		foreach($r_meters as $v_meters){
			$meters_insert[] = $this->mydb->parse("(null,?i,?i,?s,?s,?s,?s,?s,?s,?s,0,null)", (int)$v_meters['receipts_id'], (int)$v_meters['value_count'], (int)$v_meters['indicbef1'], $v_meters['date_indicbef'], (int)$v_meters['indiccur1'], $v_meters['date_indiccur'], $v_meters['kod'], trim($v_meters['name']), $v_meters['service']);
		}
			
		$insert_meters = implode(",", $meters_insert);
		
		$this->mydb->query('INSERT INTO ?n VALUES ?p', $table_name, $insert_meters);
		
		return true;
	}
	
	private function get_date_ymd($date){
		$newdate = explode(' ', $date);
		
		return $newdate[0];
	}
	
	private function import_receipts_contractors($object_id, $period_id, $receipts, $option = 1){
		$r_contractors = array();
		$receipts_id = array();
		$table_name = $this->db_prefix . 'mc_receipts_contractors';
		
		foreach($this->xml->PersAcc as $personal_account){
			foreach($personal_account->contractor as $contractor){
				$r_contractors[] = array(
					'receipts_id' => $receipts[(int)$personal_account['kod_ls']]['id'],
					'contractor_xml_id' => $contractor['id'],
					'debtbeg' => $this->price_replace($contractor['debtbeg']),
					'debtend' => $this->price_replace($contractor['debtend']),
					'debtprev' => $this->price_replace($contractor['debtprev']),
					'peni' => (isset($contractor['peni'])) ? $this->price_replace($contractor['peni']) : null,
					'sumtopay' => $this->price_replace($contractor['sumtopay']),
					'last_payment_date' => $contractor['lastPaymentDate']
				);
			}
			
			if($option == 2){
				$receipts_id[] = $receipts[(int)$personal_account['kod_ls']]['id'];
			}
		}
		
		if($option == 2){
			$delete = 'DELETE FROM ?n WHERE receipts_id IN (?a)';
			$this->mydb->query($delete, $table_name, $receipts_id);
		}
		
		$contractors_insert = array();
			
		foreach($r_contractors as $v_contractors){
			$contractors_insert[] = $this->mydb->parse("(null,?i,?s,?s,?s,?s,?s,?s,?s,0,null)", (int)$v_contractors['receipts_id'], $v_contractors['contractor_xml_id'], $v_contractors['debtbeg'], $v_contractors['debtend'], $v_contractors['debtprev'], $v_contractors['peni'], $v_contractors['sumtopay'], $v_contractors['last_payment_date']);
		}
			
		$insert_contractors = implode(",", $contractors_insert);
		
		$this->mydb->query('INSERT INTO ?n VALUES ?p', $table_name, $insert_contractors);
		
		return true;
	}
	
	private function isset_or_null($data = false){
		if(empty($data)){
			return null;
		} else {
			return $data;
		}
	}
	
	private function price_replace($price){		
		$price = str_replace(" ","",$price);
		$price = str_replace(",",".",$price);
		
		return $price;
	}
}
