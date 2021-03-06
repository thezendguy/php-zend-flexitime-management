<?php

class Application_Model_User
{
	const GUEST = 1;
	const USER = 2;
	const SUPERVISOR = 3;
	const MASTER = 4;
	
	protected $_id;
	protected $_teamId;
	protected $_email;
	protected $_name;
	protected $_role;
	
	public function __construct($data = null) {
	
		if($data != null) {
			if($data instanceof stdClass) {
				$this->setId($data->id);
				$this->setTeamId($data->team_id);
				$this->setName($data->name);
				$this->setEmail($data->email);
				$this->setRole($data->role);
			}
			else if(is_array($data)) {
				$this->setId($data['id']);
				$this->setTeamId($data['team_id']);
				$this->setName($data['name']);
				$this->setEmail($data['email']);
				$this->setRole($data['role']);
			}
		}
	}
	
	public function getId() { return $this->_id; }
	public function getTeamId() { return $this->_teamId; }
	public function getEmail() { return $this->_email; }
	public function getName() { return $this->_name; }
	public function getRole() { return $this->_role; }
	
	public function setId($id) { $this->_id = $id; }
	public function setTeamId($teamId) { $this->_teamId = $teamId; }
	public function setEmail($email) { $this->_email = $email; }
	public function setName($name) { $this->_name = $name; }
	public function setRole($role) { $this->_role = $role; }

}

