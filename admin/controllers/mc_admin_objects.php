<?

class MC_Admin_Objects extends MC_Admin{
	public function view(){		
		$data = array();
		
		$data['title'] = 'Объекты управления';
		$data['link_add'] = $this->get_action_link($_GET['page']);
		$data['link_edit'] = $this->get_action_link($_GET['page'], 'edit', 'object_id');
		$data['link_trash'] = $this->get_action_link($_GET['page'], 'trash', 'object_id'); 
		$data['link_view'] = $this->get_action_link($_GET['page'], 'view', 'object_id');
		
		$data['org'] = array();
		
		$table_name = $this->prefix . 'mc_object';
		$limit = 20;
		
		if(isset($_GET['offset']) && $_GET['offset'] != 1){
			$offset = ($_GET['offset'] - 1) * $limit;
		} else {
			$offset = 0;
		}
		
		$data['org'] = $this->mydb->getAll('SELECT `id`, `name`, `active` FROM ?n ORDER BY `id` DESC LIMIT ?i,?i', $table_name, $offset, $limit);
		
		echo $this->render(DIR_TEMPLATE_ADMIN . 'page/objects/view.tpl', $data);
	}
	
	public function add(){
		$data = array();
		
		$data['title'] = 'Добавление объекта управления';
		
		$data['script'][] = array('src'=> MC_Admin::MC_ADMIN_JS . 'mc.js');
		$data['reload_url'] = $this->get_action_link($_GET['page'], 'edit', 'object_id');
		$data['action_url'] = $this->get_action_link($_GET['page'], 'save');
		
		echo $this->render(DIR_TEMPLATE_ADMIN . 'page/objects/add.tpl', $data);
	}
	
	public function edit(){
		$data = array();
		$table_name = $this->prefix . 'mc_object';
		
		$data['title'] = 'Редактирование объекта управления';
		
		$object_data = $this->mydb->getRow('SELECT * FROM ?n WHERE `id` = ?i', $table_name, $_GET['object_id']);
		
		foreach($object_data as $key => $value){
			$data['f_'.$key] = htmlspecialchars($value);
		}
		
		$data['reload_url'] = $this->get_action_link($_GET['page'], 'edit', 'object_id');
		
		echo $this->render(DIR_TEMPLATE_ADMIN . 'page/objects/edit.tpl', $data);
	}
	
	public function trash(){
		$data = array();
		
		$data['title'] = 'Удаление объекта управления';
		
		echo $this->render(DIR_TEMPLATE_ADMIN . 'page/objects/trash.tpl', $data);
	}
	
	public function save(){
		$data = array();
		$table_name = $this->prefix . 'mc_object';
		
		$reload_url = $_POST['reload_url'];
		unset($_POST['reload_url']);
		
		if(!empty($_GET['event'] = 'add')){
			$sql = 'INSERT INTO ?n SET ?u';
			
			foreach($_POST as $key => $value){
				$data[$key] = htmlspecialchars($value);
			}
			
			$this->mydb->query($sql, $table_name, $data);
			$object_id = $this->mydb->insertId();
		}
		
		if(!empty($_GET['event'] = 'edit')){
			$sql = 'UPDATE ?n SET ?u WHERE `id` = ?i';
			
			$object_id = $_POST['object_id'];
			unset($_POST['object_id']);
			
			foreach($_POST as $key => $value){
				$data[$key] = htmlspecialchars($value);
			}
			
			$this->mydb->query($sql, $table_name, $_POST, $object_id);
		}
		
		header('Location: ' . $reload_url . $object_id);
		
		return true;
	}
}