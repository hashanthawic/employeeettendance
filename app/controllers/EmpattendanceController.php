<?php 
/**
 * Empattendance Page Controller
 * @category  Controller
 */
class EmpattendanceController extends SecureController{
	/**
     * Load Record Action 
     * $arg1 Field Name
     * $arg2 Field Value 
     * $param $arg1 string
     * $param $arg1 string
     * @return View
     */
	function index($fieldname = null , $fieldvalue = null){
		$db = $this->GetModel();
		$tablename = $this->tablename = 'empattendance';
		$fields = array('id', 
			'Name', 
			'Username', 
			'Registrationdate', 
			'Contactnumber');
		$limit = $this->get_page_limit(MAX_RECORD_COUNT); // return pagination from BaseModel Class e.g array(5,20)
		$getdata = $this->getdata; //array of sanitized values passed via $_GET;
		if(!empty($this->search)){
			$text = trim($this->search);
			$db->where("(id LIKE ? OR Name LIKE ? OR Username LIKE ? OR Password LIKE ? OR Registrationdate LIKE ? OR Contactnumber LIKE ?)", array("%$text%","%$text%","%$text%","%$text%","%$text%","%$text%"));
		}
		if(!empty($this->orderby)){ // when order by request fields (from $_GET param)
			$db->orderBy($this->orderby,$this->ordertype);
		}
		else{
			$db->orderBy('empattendance.id', ORDER_TYPE);
		}
		$db->where("empattendance.Username" , get_active_user('Username') );
		if( !empty($fieldname) ){
			$db->where($fieldname , $fieldvalue);
		}
		//page filter command
		$tc = $db->withTotalCount();
		$records = $db->get($tablename, $limit, $fields);
		$data = new stdClass;
		$data->records = $records;
		$data->record_count = count($records);
		$data->total_records = intval($tc->totalCount);
		//list of record id for audit trail
		$this->rec_id = array_column($records, 'id');
		$this->write_to_log('list', 'true');
		if($db->getLastError()){
			$page_error = $db->getLastError();
			$this->view->page_error = $page_error;
		}
		$this->view->page_title ="Emp Attendance";
		$this->view->render('empattendance/list.php' , $data ,'main_layout.php');
	}
	/**
     * View Record Action 
     * @return View
     */
	function view( $rec_id = null , $value = null){
		$db = $this->GetModel();
		$this->rec_id = $rec_id;
		$tablename = $this->tablename = 'empattendance';
		$fields = array('id', 
			'Name', 
			'Username', 
			'Password', 
			'Registrationdate', 
			'Contactnumber');
		$getdata = $this->getdata; //array of sanitized values passed via $_GET;
		$db->where("empattendance.Username" , get_active_user('Username') );
		if( !empty($value) ){
			$db->where($rec_id, urldecode($value));
		}
		else{
			$db->where('empattendance.id' , $rec_id);
		}
		$record = $db->getOne($tablename, $fields );
		if(!empty($record)){
			$this->write_to_log('view', 'true');
			$this->view->page_title ="View  Empattendance";
			$this->view->render('empattendance/view.php' , $record ,'main_layout.php');
		}
		else{
			$page_error = null;
			if($db->getLastError()){
				$page_error = $db->getLastError();
			}
			else{
				$page_error = "No record found";
			}
			$this->view->page_error = $page_error;
			$this->view->render('empattendance/view.php' , $record , 'main_layout.php');
		}
	}
	/**
     * Add New Record Action 
     * If Not $_POST Request, Display Add Record Form View
     * @return View
     */
	function add(){
		if(is_post_request()){
			Csrf :: cross_check();
			$db = $this->GetModel();
			$tablename = $this->tablename = 'empattendance';
			$fields = $this->fields = array('Name','Username','Password','Registrationdate','Contactnumber'); //insert fields
			$postdata = $this->transform_request_data($_POST);
			$cpassword = $postdata['confirm_password'];
			$password = $postdata['Password'];
			if($cpassword != $password){
				$this->view->page_error[] = "Your password confirmation is not consistent";
			}
			$this->rules_array = array(
				'Name' => 'required',
				'Username' => 'required',
				'Password' => 'required',
				'Registrationdate' => 'required',
				'Contactnumber' => 'required',
			);
			$this->sanitize_array = array(
				'Name' => 'sanitize_string',
				'Username' => 'sanitize_string',
				'Registrationdate' => 'sanitize_string',
				'Contactnumber' => 'sanitize_string',
			);
			$this->filter_vals = true; //set whether to remove empty fields
			$modeldata = $this -> modeldata = $this->validate_form($postdata);
			$password_text = $modeldata['Password'];
			$modeldata['Password'] = password_hash($password_text , PASSWORD_DEFAULT);
			//Check if Duplicate Record Already Exit In The Database
			$db->where('Username',$modeldata['Username']);
			if($db->has($tablename)){
				$this->view->page_error[] = $modeldata['Username']." Already exist!";
			}
			//Check if Duplicate Record Already Exit In The Database
			$db->where('Contactnumber',$modeldata['Contactnumber']);
			if($db->has($tablename)){
				$this->view->page_error[] = $modeldata['Contactnumber']." Already exist!";
			} 
			if(empty($this->view->page_error)){
				$rec_id = $this->rec_id = $db->insert($tablename, $modeldata);
				if(!empty($rec_id)){
					$this->write_to_log('add', 'true');
					if(is_ajax()){
						render_json("Record added successfully");
					}
					else{
						set_flash_msg('','');
						if(!empty($this->redirect)){ 
							redirect_to_page($this->redirect); //if redirect url is passed via $_GET
						}
						else{
							redirect_to_page("empattendance");
						}
					}
					return;
				}
				else{
					$page_error = null;
					if($db->getLastError()){
						$page_error = $db->getLastError();
					}
					else{
						$page_error = "Error inserting record";
					}
					if(is_ajax()){
						render_error($page_error); 
						return;
					}
					else{
						$this->view->page_error[] = $page_error;
					}
				}
			}
		}
		$this->view->page_title ="Add Record";
		$this->view->render('empattendance/add.php' ,null,'main_layout.php');
	}
	/**
     * Edit Record Action 
     * If Not $_POST Request, Display Edit Record Form View
     * @return View
     */
	function edit($rec_id = null){
		$db = $this->GetModel();
		$this->rec_id = $rec_id;
		$tablename = $this->tablename = 'empattendance';
		$fields = $this->fields = array('id','Name','Username','Password','Registrationdate','Contactnumber'); //editable fields
		if(is_post_request()){
			Csrf :: cross_check();
			$postdata = $this->transform_request_data($_POST);
			$cpassword = $postdata['confirm_password'];
			$password = $postdata['Password'];
			if($cpassword != $password){
				$this->view->page_error[] = "Your password confirmation is not consistent";
			}
			$this->rules_array = array(
				'Name' => 'required',
				'Username' => 'required',
				'Password' => 'required',
				'Registrationdate' => 'required',
				'Contactnumber' => 'required',
			);
			$this->sanitize_array = array(
				'Name' => 'sanitize_string',
				'Username' => 'sanitize_string',
				'Registrationdate' => 'sanitize_string',
				'Contactnumber' => 'sanitize_string',
			);
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			$password_text = $modeldata['Password'];
			$modeldata['Password'] = password_hash($password_text , PASSWORD_DEFAULT);
			//Check if Duplicate Record Already Exit In The Database
			if(isset($modeldata['Username'])){
				$db->where('Username',$modeldata['Username'])->where('id',$rec_id,'!=');
				if($db->has($tablename)){
					$this->view->page_error[] = $modeldata['Username']." Already exist!";
				}
			}
			//Check if Duplicate Record Already Exit In The Database
			if(isset($modeldata['Contactnumber'])){
				$db->where('Contactnumber',$modeldata['Contactnumber'])->where('id',$rec_id,'!=');
				if($db->has($tablename)){
					$this->view->page_error[] = $modeldata['Contactnumber']." Already exist!";
				}
			} 
			if(empty($this->view->page_error)){
		$db->where("empattendance.Username" , get_active_user('Username') );
				$db->where('empattendance.id' , $rec_id);
				$bool = $db->update($tablename, $modeldata);
				$numRows = $db->getRowCount(); //number of affected rows. 0 = no record field updated
				if($bool && $numRows){
					$this->write_to_log('edit', 'true');
					if(is_ajax()){
						render_json("Record updated successfully");
					}
					else{
						set_flash_msg('','');
						if(!empty($this->redirect)){ 
							redirect_to_page($this->redirect); //if redirect url is passed via $_GET
						}
						else{
							redirect_to_page("empattendance");
						}
					}
					return;
				}
				else{
					$page_error = null;
					if($db->getLastError()){
						$page_error = $db->getLastError();
					}
					elseif(!$numRows){
						$page_error = "No record updated";
						if(is_ajax()){
							render_error($page_error); //return http status error
						}
						else{
							//no changes made to the table record
							set_flash_msg($page_error, 'warning');
							if(!empty($this->redirect)){ 
								redirect_to_page($this->redirect); //if redirect url is passed via $_GET
							}
							else{
								redirect_to_page("empattendance");
							}
						}
						return;
					}
					else{
						$page_error = "No record found";
					}
					if(is_ajax()){
						render_error($page_error); //return http status error
						return;
					}
					//continue to display edit page with errors
					$this->view->page_error[] = $page_error;
				}
			}
		}
		$db->where("empattendance.Username" , get_active_user('Username') );$db->where('empattendance.id' , $rec_id);
		$data = $db->getOne($tablename, $fields);
		$this->view->page_title ="Edit  Empattendance";
		if(!empty($data)){
			$this->view->render('empattendance/edit.php' , $data, 'main_layout.php');
		}
		else{
			if($db->getLastError()){
				$this->view->page_error[] = $db->getLastError();
			}
			else{
				$this->view->page_error[] = "No record found";
			}
			$this->view->render('empattendance/edit.php' , $data , 'main_layout.php');
		}
	}
	/**
     * Edit single field Action 
     * Return record id
     * @return View
     */
	function editfield($rec_id = null){
		$db = $this->GetModel();
		$this->rec_id = $rec_id;
		$tablename = $this->tablename = 'empattendance';
		$fields = $this->fields = array('id','Name','Username','Password','Registrationdate','Contactnumber'); //editable fields
		if(is_post_request()){
			Csrf :: cross_check();
			$postdata = array();
			if(isset($_POST['name']) && isset($_POST['value'])){
				$fieldname = $_POST['name'];
				$fieldvalue = $_POST['value'];
				$postdata[$fieldname] = $fieldvalue;
				$postdata = $this->transform_request_data($postdata);
			}
			else{
				$this->view->page_error = "invalid post data";
			}
			$this->rules_array = array(
				'Name' => 'required',
				'Username' => 'required',
				'Password' => 'required',
				'Registrationdate' => 'required',
				'Contactnumber' => 'required',
			);
			$this->sanitize_array = array(
				'Name' => 'sanitize_string',
				'Username' => 'sanitize_string',
				'Registrationdate' => 'sanitize_string',
				'Contactnumber' => 'sanitize_string',
			);
			$this->filter_rules = true; //filter validation rules by excluding fields not in the POST Data
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			//Check if Duplicate Record Already Exit In The Database
			if(isset($modeldata['Username'])){
				$db->where('Username',$modeldata['Username'])->where('id',$rec_id,'!=');
				if($db->has($tablename)){
					$this->view->page_error[] = $modeldata['Username']." Already exist!";
				}
			}
			//Check if Duplicate Record Already Exit In The Database
			if(isset($modeldata['Contactnumber'])){
				$db->where('Contactnumber',$modeldata['Contactnumber'])->where('id',$rec_id,'!=');
				if($db->has($tablename)){
					$this->view->page_error[] = $modeldata['Contactnumber']." Already exist!";
				}
			} 
			if(empty($this->view->page_error)){
		$db->where("empattendance.Username" , get_active_user('Username') );
				$db->where('empattendance.id' , $rec_id);
				try{
					$bool = $db->update($tablename, $modeldata);
					$numRows = $db->getRowCount();
					if($bool && $numRows){
						$this->write_to_log('edit', 'true');
						render_json(
							array(
								'num_rows' =>$numRows,
								'rec_id' =>$rec_id,
							)
						);
					}
					else{
						$page_error = null;
						if($db->getLastError()){
							$page_error = $db->getLastError();
						}
						elseif(!$numRows){
							$page_error = "No record updated";
						}
						else{
							$page_error = "No record found";
						}
						render_error($page_error);
					}
				}
				catch(Exception $e){
					render_error($e->getMessage());
				}
			}
			else{
				render_error($this->view->page_error);
			}
		}
		else{
			render_error("Request type not accepted");
		}
	}
	/**
     * Delete Record Action 
     * @return View
     */
	function delete( $rec_ids = null ){
		Csrf :: cross_check();
		$db = $this->GetModel();
		$this->rec_id = $rec_ids;
		$tablename = $this->tablename = 'empattendance';
		//split record id separated by comma into array
		$arr_id = explode(',', $rec_ids);
		//set query conditions for all records that will be deleted
		foreach($arr_id as $rec_id){
			$db->where('empattendance.id' , $rec_id,"=",'OR');
		}
		$db->where("empattendance.Username" , get_active_user('Username') );
		$bool = $db->delete($tablename);
		if($bool){
			$this->write_to_log('delete', 'true');
			set_flash_msg('','');
		}
		else{
			$page_error = "";
			if($db->getLastError()){
				$page_error = $db->getLastError();
			}
			else{
				$page_error = "Error deleting the record. please make sure that the record exit";
			}
			set_flash_msg($page_error,'danger');
		}
		if(!empty($this->redirect)){ 
			redirect_to_page($this->redirect); //if redirect url is passed via $_GET
		}
		else{
			redirect_to_page("empattendance");
		}
	}
}
