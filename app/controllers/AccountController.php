<?php 
/**
 * Account Page Controller
 * @category  Controller
 */
class AccountController extends SecureController{
	/**
     * Index Action
     * @return View
     */
	function index(){
		$db = $this->GetModel();
		$rec_id = $this->rec_id = USER_ID;
		$db->where ("id", $rec_id);
		$tablename = $this->tablename = 'empattendance';
		$user = $db->getOne($tablename , '*');
		if(!empty($user)){
			$this->write_to_log('accountview', 'true');
			$this->view->render("account/view.php" ,$user,"main_layout.php");
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
			$this->view->render("account/view.php", null ,"main_layout.php");
		}
	}
	/**
     * Edit Record Action 
     * If Not $_POST Request, Display Edit Record Form View
     * @return View
     */
	function edit(){
		$db = $this->GetModel();
		$this->rec_id = USER_ID;
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
				$db->where('Username',$modeldata['Username'])->where('id',USER_ID,'!=');
				if($db->has($tablename)){
					$this->view->page_error[] = $modeldata['Username']." Already exist!";
				}
			}
			//Check if Duplicate Record Already Exit In The Database
			if(isset($modeldata['Contactnumber'])){
				$db->where('Contactnumber',$modeldata['Contactnumber'])->where('id',USER_ID,'!=');
				if($db->has($tablename)){
					$this->view->page_error[] = $modeldata['Contactnumber']." Already exist!";
				}
			} 
			if(empty($this->view->page_error)){
		$db->where("empattendance.Username" , get_active_user('Username') );
				$db->where('empattendance.id' , USER_ID);
				$bool = $db->update($tablename, $modeldata);
				$numRows = $db->getRowCount(); //number of affected rows. 0 = no record field updated
				if($bool && $numRows){
					$db->where ('id', USER_ID);
					$user = $db->getOne($tablename , '*');
					set_session('user_data',$user);
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
							redirect_to_page("account");
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
								redirect_to_page("account");
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
		$db->where("empattendance.Username" , get_active_user('Username') );$db->where('empattendance.id' , USER_ID);
		$data = $db->getOne($tablename, $fields);
		$this->view->page_title ="My Account";
		if(!empty($data)){
			$this->view->render('account/edit.php' , $data, 'main_layout.php');
		}
		else{
			if($db->getLastError()){
				$this->view->page_error[] = $db->getLastError();
			}
			else{
				$this->view->page_error[] = "No record found";
			}
			$this->view->render('account/edit.php' , $data , 'main_layout.php');
		}
	}
	/**
     * Change Email Action
     * @return View
     */
	function change_email(){
		if(is_post_request()){
			Csrf :: cross_check();
			$form_collection = $_POST;
			$email=trim($form_collection['Contactnumber']);
			$db = $this->GetModel();
			$rec_id = $this->rec_id = USER_ID;
			$tablename = $this->tablename = 'empattendance';
			$db->where ("id", $rec_id);
			$result = $db->update($tablename, array('Contactnumber' => $email ));
			if($result){
				$this->write_to_log('emailchange', 'true');
				set_flash_msg("Email address changed successfully",'success');
				redirect_to_page("account");
			}
			else{
				$page_error =  "Email not changed";
				$this->view->page_error = $page_error;
				$this->view->render("account/change_email.php" , null , "main_layout.php");
			}
		}
		else{
			$this->view->render("account/change_email.php" ,null,"main_layout.php");
		}
	}
}
