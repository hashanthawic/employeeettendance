<?php 
defined('ROOT') OR exit('No direct script access allowed');

/**
* Application Base Controller.
* Other Controllers must extend to the class 
* Controllers which do not need page authentication and resource authorization can extend to this class
*/
class BaseController{
	/**
	 * Instance of the base view class. Use for rendering pages
	 * @var [BaseView class]
	 */
	public $view = null;


	/**
	 * Instance of the PDODB class
	 * Provide Data access laeyer to the database
	 * @var [PDODB class]
	 */
	public $db = null;


	/**
	 * Current page number from $_GET['page_num']
	 * @var [int]
	 */
	public $page_num = 1;


	/**
	 * Current page number from $_GET['limit_start']
	 * @var [int]
	 */
	public $limit_start = 1;


	/**
	 * Maximum number of item per page from $_GET['limit_count']
	 * @var [int]
	 */
	public $limit_count = MAX_RECORD_COUNT;


	/**
	 * order query based on field name from $_GET['orderby']
	 * @var [string]
	 */
	public $orderby = null;


	/**
	 * order query type desc|asc from $_GET['ordertype']
	 * @var [string]
	 */
	public $ordertype = ORDER_TYPE;


	/**
	 * current page record id from router url pagename/[rec_id]
	 * @var [string]
	 */
	public $rec_id = null;


	/**
	 * the search query for the page from $_GET['search]
	 * @var [string]
	 */
	public $search = null;


	/**
	 * the table name associated to the current page
	 * @var [string]
	 */
	public $tablename = null;

	/**
	 * GET Data after sanitization from $_GET
	 * @var [array]
	 */
	public $getdata = array();
	
	/**
	 * POST Data after sanitization and validation from $_POST
	 * @var [array]
	 */
	public $modeldata = array();
	

	/**
	 * File upload settings if their should be any upload on any page
	 * @var [array]
	 */
	public $file_upload_settings = array();


	/**
	 * Table fields associated with the current page
	 * @var [array]
	 */
	public $fields = array();

	/**
	 *  set whether to filter POST data by removing empty fields
	 * @var bool
	 */
	public $filter_vals = false;
	
	/**
	 *  set whether to filter validation rules by excluding fields not in the POST Data
	 * @var bool
	 */
	public $filter_rules = false;

	function __construct(){
		$this->view = new BaseView;
		//by default filter all values of the GET Request
		$this->getdata = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
		if(!empty($this->getdata)){
			foreach($this->getdata as $obj => $val){
				$this->$obj = $val;//pass each request data to the current page as class property
			}
		}
		
		if(empty($this -> limit_start)){
			$this -> limit_start = 1;
		}
		$this -> page_num = $this -> limit_start;
		$this -> limit_start = ($this -> limit_start-1) * $this -> limit_count;
		
		
		
	}
	/**
     * validate post array using gump library
     * sanitize input array based on the page sanitize rule
     * validate data based on the set of defined rules
     * @var $filter_rules = true: will validate post data only for posted array data if field name is not set in the postdata
     * @return  Array
     */
	function validate_form($modeldata){
		if(!empty($this->sanitize_array)){
			$modeldata = GUMP::filter_input($modeldata, $this->sanitize_array);
		}
		$rules = $this->rules_array;
		if($this->filter_rules == true){
			$rules = array(); //set new rules
			//set rules for only fields in the posted data
			foreach($modeldata as $key => $val){
				if(in_array($key, $this->rules_array)){
					$rules[$key] =  $this->rules_array[$key];
				}
			}
		}
		//accept posted fields if they are part of the page fields
		$fields = $this->fields;
		if(!empty($fields)){
			foreach($modeldata as $key => $val){
				if(!in_array($key, $fields)){
					unset($modeldata[$key]); //remove field if not part of the field list
				}
			}
		}
		$is_valid = GUMP::is_valid($modeldata, $rules);
		// remove empty field values
		if($this->filter_vals == true){
			$modeldata = array_filter($modeldata, function($val){
				if($val === "" || is_null($val)){
					return false;
				}
				else{
					return true;
				}
			});
		}
		if($is_valid !== true) {
			if(is_array($is_valid)){
				foreach($is_valid as  $error_msg){
					$this->view->page_error[] = strip_tags($error_msg);
				}
			}
			else{
				$this->view->page_error[] = $is_valid;
			}
		}
		return $modeldata;
	}
	
	/**
     * Concat Array  Values With Comma if REQUEST Value is a simple Array
     * Specific for this Framework Only
     * @arr $_POST || $_GET data
     * @return  Array
     */
	function transform_request_data($arr){
		foreach($arr as $key=>$val){
			if(is_array($val)){
				$arr[$key]=implode(',',$val);
			}
		}
		return $arr;
	}
	
	/**
     * Concat Array  Values With Comma for multiple post data
     * Specific for this Framework Only
     * @arr $_POST || $_GET data
     * @return  Array
     */
	function transform_multi_request_data($arr){
		$alldata = array();
		foreach($arr as $key=>$value){
			$combine_vals = recursive_implode($value, "");
			//merge all values of each input into one string and check if each post data contains value
			if(!empty($combine_vals)){
				$alldata[] = $this -> transform_request_data($value);
			}
		}
		return $alldata;
	}
	
	/**
     * Init DB Connection 
     * Which can be use to perform DB queries
     * @return  PDO Object
     */
	function GetModel(){
		//Initialse New Database Connection
		$this->db = new PDODb(DB_TYPE, DB_HOST , DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT , DB_CHARSET);
		return $this->db;
	}
	
	
	/**
     * Delete files
	 * split file path if they are separated by comma
     * @files Array
     * @return  nul
     */
	function delete_record_files($files, $field){
		foreach($files as $file_path){
			$comma_paths = explode( ',', $file_path[$field] );
			foreach($comma_paths as $file_url){
				try{
					$file_dir_path = str_ireplace( SITE_ADDR , "" , $file_url ) ;
					@unlink($file_dir_path);
				}
				catch(Exception $e) {
				  // error_log('Message: ' .$e->getMessage());
				}
			}
		}
	}
	
	
	/**
     * upload files and return file paths
	 * @var $fieldname File upload filed name
     * @return  nul
     */
	function get_uploaded_file_paths($fieldname){
		$uploaded_files = "";
		if(!empty($_FILES[$fieldname])){
			$uploader = new Uploader;
			$upload_settings = $this->file_upload_settings[$fieldname];
			$upload_data = $uploader->upload($_FILES[$fieldname], $upload_settings );
			if($upload_data['isComplete']){
				$arr_files = $upload_data['data']['files'];
				if(!empty($arr_files)){
					if(!empty($upload_settings['returnfullpath'])){
						$arr_files = array_map( "set_url", $arr_files ); // set files with complete url of the website
					}
					$uploaded_files = implode("," , $arr_files);
				}
			}
			if($upload_data['hasErrors']){
				$errors = $upload_data['errors'];
				foreach($errors as $key=>$val){
					$this->view->page_error[] = "$key : $val[$key]";
				}
			}
		}
		return $uploaded_files;
	}
	
	/**
     * For getting uploaded file as Blob type
     * can be use to insert blob data into the databased
	 * @var $fieldname File upload filed name
     * @return  Blob object
     */
	function get_uploaded_file_data($fieldname){
		if(!empty($_FILES[$fieldname])){
			$name = $_FILES[$fieldname]['name'];
			$extension = strtolower(substr($name, strpos($name, '.') + 1));
			$tmp_name = $_FILES[$fieldname]['tmp_name'];
			$type = $_FILES[$fieldname]['type'];
			$size = $_FILES[$fieldname]['size'];
			return file_get_contents($tmp_name);
		}
		return null;
	}
	
	/**
	 * Set Current Page Start and Page Count
	 * $page_count Set Max Record to retrive per page
	 * $fieldvalue Table Field Value 
	 * @return array(limit_start,limit_count)
	 */
	function get_page_limit($page_count = MAX_RECORD_COUNT){
		
		if(!empty($_GET['limit_count'])){ //Get page limit from query string request if available
		
			 /*Set limit to high number to get all records. starting from the current position */
			 
			if($_GET['limit_count'] == -1){
				$this->limit_count=1000000000;
			}
			else{
				$this->limit_count=$_GET['limit_count'];
			}
		}
		else{
			$this->limit_count=$page_count;
			//$_GET['limit_count']=$page_count;
		}
		
		return array($this->limit_start,$this->limit_count);
	}
	
	
	
	
	public $log_location = 'table';
	
	public function write_to_log($action, $req_status = 'true', $message = null){
		$rec_ids = $this->rec_id;
		$rec_id = (is_array($rec_ids) ? implode(',', $rec_ids) : $rec_ids);
		$table_name = $this->tablename;
		$modeldata = $this->modeldata;
		$timestamp = datetime_now();
		$user_id = get_active_user('id');
		$server_ip = get_user_ip();
		
		$sql_query = null;
		if(!empty($this->db)){
			$sql_query = $this->db->getLastQuery();
		}
		else{
			$this->db = $this->GetModel();
		}
		
		$request_url = Router :: $page_url;
		$request_data = json_encode($modeldata);
		
		$audit_data = array(
					'Timestamp' => $timestamp,
					'Action' => $action,
					'TableName' => $table_name,
					'RecordID' => $rec_id,
					'SqlQuery' => $sql_query,
					'UserID' => $user_id,
					'ServerIP' => get_user_ip(),
					'RequestUrl' => $request_url,
					'RequestData' => $request_data,
					'RequestCompleted' => $req_status,
					'RequestMsg' => $message
		);
		
		if($this->log_location == 'table'){
			try{
				$this->db->insert("app_logs", $audit_data);
			}
			catch(Exception $e){
				throw new Exception($e);
				//do something with the error
			}
		}
		else if($this->log_location == 'file'){
			try{
				$file_name = date('Y-m-d') . " - auditlog.log";
				$log_path = AUDIT_LOGS_DIR . $file_name;
				if(!file_exists($log_path)){
					$fs = fopen($log_path, "a");
					$arr_keys = array_keys($audit_data);
					$arr_values = array_values($audit_data);
					fputcsv($fs, $arr_keys);
					fputcsv($fs, $arr_values);
					fclose($fs);
				}
				else{
					$arr_values = array_values($audit_data);
					$fs = fopen($log_path, "a");
					fputcsv($fs, $arr_values);
					fclose($fs);
				}
			}
			catch(Exception $e){
				//do something with the error
			}
		}
		else if($this->log_location == 'email'){
			try{
				$mail_receiver = array();
				$mail_title = "New $table_name $action";
				$mail_body = "
        Action: $action
        TimeStamp: $timestamp
        TableName: $table_name
        RecordsID: $rec_id
        SQLQuery: $sql_query
        UserID: $user_id
        ServerIP: $server_ip
        RequestUrl: $request_url
        RequestData: $request_data
        RequestCompleted: $req_status
        RequestMsg: $message";
				$mailer = new Mailer;
				$mailer -> send_mail($mail_receiver, $mail_title, $mail_body);
			}
			catch(Exception $e){
				//do something with the error
			}
		}
	}
	
}

/**
* Extends to Application Base Controller.
* Page Controllers which need page authentication and authorization can extend to this class 
*/
class SecureController extends BaseController{
	function __construct(){
		parent::__construct();
		// Page actions which do not require authentication.
		$exclude_pages = array();
		$url = Router :: $page_url;
		$url = str_ireplace('/index','/list',$url);
		if(!empty($url)){
			$url_segment =$url_segment = explode("/" , rtrim($url , "/")) ;
			$controller = strtolower(!empty($url_segment[0]) ? $url_segment[0] : null);
			$action = strtolower((!empty($url_segment[1]) ? $url_segment[1] : 'list'));
			$page = "$controller/$action";
			if( !in_array($page , $exclude_pages )){
				authenticate_user(); // Authenticate user And Authorise User
			}
		}
	}
}