<?php 
/**
 * App_logs Page Controller
 * @category  Controller
 */
class App_logsController extends SecureController{
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
		$tablename = $this->tablename = 'app_logs';
		$fields = array('log_id', 
			'Timestamp', 
			'Action', 
			'TableName', 
			'RecordID', 
			'SqlQuery', 
			'UserID', 
			'ServerIP', 
			'RequestUrl', 
			'RequestData', 
			'RequestCompleted', 
			'RequestMsg');
		$limit = $this->get_page_limit(MAX_RECORD_COUNT); // return pagination from BaseModel Class e.g array(5,20)
		$getdata = $this->getdata; //array of sanitized values passed via $_GET;
		if(!empty($this->search)){
			$text = trim($this->search);
			$db->where("(log_id LIKE ? OR Timestamp LIKE ? OR Action LIKE ? OR TableName LIKE ? OR RecordID LIKE ? OR SqlQuery LIKE ? OR UserID LIKE ? OR ServerIP LIKE ? OR RequestUrl LIKE ? OR RequestData LIKE ? OR RequestCompleted LIKE ? OR RequestMsg LIKE ?)", array("%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%"));
		}
		if(!empty($this->orderby)){ // when order by request fields (from $_GET param)
			$db->orderBy($this->orderby,$this->ordertype);
		}
		else{
			$db->orderBy('app_logs.log_id', ORDER_TYPE);
		}
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
		if($db->getLastError()){
			$page_error = $db->getLastError();
			$this->view->page_error = $page_error;
		}
		$this->view->page_title ="App Logs";
		$this->view->render('app_logs/list.php' , $data ,'main_layout.php');
	}
	/**
     * View Record Action 
     * @return View
     */
	function view( $rec_id = null , $value = null){
		$db = $this->GetModel();
		$this->rec_id = $rec_id;
		$tablename = $this->tablename = 'app_logs';
		$fields = array('log_id', 
			'Timestamp', 
			'Action', 
			'TableName', 
			'RecordID', 
			'SqlQuery', 
			'UserID', 
			'ServerIP', 
			'RequestUrl', 
			'RequestData', 
			'RequestCompleted', 
			'RequestMsg');
		$getdata = $this->getdata; //array of sanitized values passed via $_GET;
		if( !empty($value) ){
			$db->where($rec_id, urldecode($value));
		}
		else{
			$db->where('app_logs.log_id' , $rec_id);
		}
		$record = $db->getOne($tablename, $fields );
		if(!empty($record)){
			$this->view->page_title ="View  App Logs";
			$this->view->render('app_logs/view.php' , $record ,'main_layout.php');
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
			$this->view->render('app_logs/view.php' , $record , 'main_layout.php');
		}
	}
}
