<?php 

/**
 * SharedController Controller
 * @category  Controller / Model
 */
class SharedController extends BaseController{
	
	/**
     * empattendance_Username_value_exist Model Action
     * @return array
     */
	function empattendance_Username_value_exist($val){
		$db = $this->GetModel();
		$db->where('Username', $val);
		$exist = $db->has('empattendance');
		return $exist;
	}

	/**
     * empattendance_Contactnumber_value_exist Model Action
     * @return array
     */
	function empattendance_Contactnumber_value_exist($val){
		$db = $this->GetModel();
		$db->where('Contactnumber', $val);
		$exist = $db->has('empattendance');
		return $exist;
	}

	/**
     * getcount_empattendance Model Action
     * @return Value
     */
	function getcount_empattendance(){
		$db = $this->GetModel();
		$sqltext = "SELECT COUNT(*) AS num FROM empattendance";
		$queryparams = null;
		$val = $db->rawQueryValue($sqltext, $queryparams);
		
		if(is_array($val)){
			return $val[0];
		}
		return $val;
	}

	/**
     * getcount_checkin Model Action
     * @return Value
     */
	function getcount_checkin(){
		$db = $this->GetModel();
		$sqltext = "SELECT COUNT(*) AS num FROM checkin";
		$queryparams = null;
		$val = $db->rawQueryValue($sqltext, $queryparams);
		
		if(is_array($val)){
			return $val[0];
		}
		return $val;
	}

	/**
     * getcount_checkout Model Action
     * @return Value
     */
	function getcount_checkout(){
		$db = $this->GetModel();
		$sqltext = "SELECT COUNT(*) AS num FROM checkout";
		$queryparams = null;
		$val = $db->rawQueryValue($sqltext, $queryparams);
		
		if(is_array($val)){
			return $val[0];
		}
		return $val;
	}

}
