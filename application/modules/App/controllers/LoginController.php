<?php

class App_LoginController extends Zend_Controller_Action {

    public function init() {
    	
        /* Initialize action controller here */
    }

    public function indexAction() {
    	
    	if(Zend_Auth::getInstance()->hasIdentity()) {
    		
    		$helper = $this->_helper->getHelper('Redirector');
        	$helper->gotoRoute(
        		array(
        			'action' => 'already-identified', 
        			'controller' => 'Login', 
        			'module' => 'App'
        		),
        		'module_full_path',
        		true
        	);
        }
        
        $form = new App_Form_Login();
		$request = $this->getRequest();
		if($request->isPost()) {
			if($form->isValid($request->getPost())) {
				
				$userData = $form->getValues();
				$auth = Zend_Auth::getInstance();
				$mapper = new Application_Model_UserMapper();
				$authAdapter = new Zend_Auth_Adapter_DbTable($mapper->getDbAdapter());
				$authAdapter
					->setTableName('users')
					->setIdentityColumn('email')
					->setIdentity($userData['email'])
					->setCredentialColumn('password')
					->setCredentialTreatment('SHA2(CONCAT(?, salt), 256)')
					->setCredential($userData['password']);
				
				$result = $auth->authenticate($authAdapter);
				if($result->isValid()) {
				
					$stdClass = $authAdapter->getResultRowObject();
					$user = new Application_Model_User($stdClass);
					$storage = $auth->getStorage();
					$storage->write($user);
					
					$redirector = $this->_helper->getHelper('Redirector');
					$redirector->gotoRoute(
						array(
							'action' => 'index', 
							'controller' => 'Employee', 
							'module' => 'App'
						),
						'module_partial_path',
						true
					);
				}
				else {
					//Display custom error message to the user
					$form->addErrorMessage('Invalid credentials');
				}
			}
			else {
				//Zend_Form will display the form with errors and user
				//content injected into the elements.
			}
		}
		else {
			//Zend_Form will display a fresh form.
		}
		
        $this->view->form = $form;
    }

    public function alreadyIdentifiedAction() {
        // action body
    }

    public function logoutAction() {
    	
        // action body
        Zend_Auth::getInstance()->clearIdentity();
        $helper = $this->_helper->getHelper('Redirector');
        $helper->gotoRoute(
        	array(
        		'action'=> 'index', 
        		'controller' => 'Login', 
        		'module' => 'App'
        	),
        	'module_partial_path',
        	true
        );
    }

    public function changePasswordAction() {
        // action body
        $form = new App_Form_ChangePassword();
        $request = $this->getRequest();
        if($request->isPost()) {
        	
        	if($form->isValid($request->getPost())) {
        		
        		//Check to ensure that the password is correct.
        		$userData = $form->getValues();
        		
        		$auth = Zend_Auth::getInstance();
        		$user = $auth->getStorage()->read();
        		$email = $user->getEmail();
        		
        		$mapper = new Application_Model_UserMapper();
        		$authAdapter = new Zend_Auth_Adapter_DbTable($mapper->getDbAdapter());
        		$authAdapter
	        		->setTableName('users')
	        		->setIdentityColumn('email')
	        		->setIdentity($email)
	        		->setCredentialColumn('password')
	        		->setCredentialTreatment('SHA2(CONCAT(?, salt), 256)')
	        		->setCredential($userData['current_password']);
        		
        		$result = $auth->authenticate($authAdapter);
        		if($result->isValid()) {
        			
        			//Re-identify the user to persist the session.
        			$auth->getStorage()->write($user);
        			$mapper->updatePassword($userData['new_password'], $email);
        			$redirect = $this->_helper->getHelper('Redirector');
        			$redirect->gotoRoute(
        				array(
        					'action' => 'password-changed',
        					'controller' => 'Login',
        					'module' => 'Gateway'
        				),
        				'module_full_path',
        				true	
        			);
        		}
        		else {
        			
        			//Password is invalid. Re-identify the user to persist the session.
        			$auth->getStorage()->write($user);
        			$form->getElement('current_password')->addError('Password is incorrect');
        		}
        	}
        	else {
        		
        		//Zend_Form will display the form pre-populated with
        		//user-provided content, and with error messages.
        	}
        }
        else {
        	
        	//Zend_Form will display a blank form.
        }
        $this->view->form = $form;
    }

    public function passwordChangedAction() {
        // action body
    }
}










