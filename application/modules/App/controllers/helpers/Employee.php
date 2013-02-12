<?php

class App_Controllers_Helpers_Employee extends Zend_Controller_Action_Helper_Abstract {
	
	public function fetchSummaries() {
		
		$employeeMapper = new Application_Model_EmployeeMapper();
		return $employeeMapper->fetchSummaries();
	}
	
	public function get($employeeId) {
		
		$employeeMapper = new Application_Model_EmployeeMapper();
		$employee = $employeeMapper->get($employeeId);
		return array('id' => $employee->getId(), 'name' => $employee->getName(), 'email' => $employee->getEmail());
	}
	
	public function post($data) {
		
		$employeeMapper = new Application_Model_EmployeeMapper();
		return $employeeMapper->insert($data);
	}
	
	public function put($data) {
	
		$employeeMapper = new Application_Model_EmployeeMapper();
		$where = $employeeMapper->getAdapter()->quoteInto('id = ?', $data['id']);
		$employeeMapper->update($data, $where);
	}
	
	public function delete($id) {

		$employeeMapper = new Application_Model_EmployeeMapper();
		$employeeMapper->delete($id);
	}
}