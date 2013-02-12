<?php

class App_ToilController extends Zend_Controller_Action
{
	protected $_mapper;
	
    public function init()
    {
        /* Initialize action controller here */
    	if(empty($this->_mapper)) {
    			
    		$this->_mapper = new Application_Model_ToilMapper();
    	}
    }

    public function indexAction()
    {
        //Get all toil entries for the current employee.    	 
        $employeeId = $this->getRequest()->getParam('employeeid');
        $this->view->toilList = $this->_mapper->fetchSummaries($employeeId);
    }

    public function postAction()
    {
    	$request = $this->getRequest();
    	$employeeId = $request->getParam('employeeid');
    	$toilAction = $request->getParam('toilaction');
    	
    	$form = new App_Form_Toil();
        $form->setMethod(Zend_Form::METHOD_POST);
        $form->setAction('/App/Toil/post/employeeid/' . $employeeId . '/toilaction/' . $toilAction);
        $form->getElement('save')->setLabel('Record');
        
        if($toilAction == 'accrue') {
        	$this->view->toil_action = 'accrued';
        }
        else {
        	$this->view->toil_action = 'used';
        }
        
        if($request->isPost()) {
        	
        	if($form->isValid($request->getPost())) {
        		
        		//Write to the database.
        		$formValues = $form->getValues();        		
        		$this->_mapper->insert($toilAction, $formValues);
        		        		
        		$redirector = $this->_helper->getHelper('Redirector');
        		$redirector->gotoRoute(
        			array(
        				'action' => 'index',
        				'controller' => 'Toil',
        				'module' => 'App',
        				'employeeid' => $employeeId
        			),
        			'module_full_path_employeeid',
        			true
        		);
          	}
        	else {
        		
        		//Zend to auto display form errors and inject user-specified values.
        	}
        }
        else {
        	
        	//Zend to auto display an empty form, with the date elements populated with todays date.
        	$now = Zend_Date::now();
        	$day = $now->get(Zend_Date::DAY);
        	$month = $now->get(Zend_Date::MONTH);
        	$year = $now->get(Zend_Date::YEAR_8601);
        	$form->populate(array(
        		'days' => $day,
        		'months' => $month,
        		'years' => $year
        	));
        }
        $this->view->employee_id = $employeeId;
        $this->view->form = $form;
    }

    public function putAction()
    {
    	$request = $this->getRequest();
    	
    	$form = new App_Form_Toil();
    	$form->setMethod(Zend_Form::METHOD_POST);
    	$form->setAction('/App/Toil/put/id/' . $request->getParam('id'));
    	$form->getElement('save')->setLabel('Update');
    	
    	if($request->isPost()) {
    		 
    		//Process the form.
    		if($form->isValid($request->getPost())) {
    			
    			$formValues = $form->getValues();
       			$this->_mapper->update($formValues);
    	       			
    			$redirector = $this->_helper->getHelper('Redirector');
    			$redirector->gotoRoute(
    				array(
    					'action' => 'index',
    					'controller' => 'Toil',
    					'module' => 'App',
    					'employeeid' => $formValues['employee_id']
    				),
    				'module_full_path_employeeid',
    				true
    			);
    		}
    		else {
    	
    			//Zend_Form to auto display issues with the form, and to inject
    			//user submitted content.
    			
    			//Ensure the employeeid is identified.
    			$formValues = $form->getValues();
    			$employeeId = $formValues['employee_id'];
    		}
    	}
    	else {
    		 
    		//Display the form populated with the values for the current toil record.    		
    		$values = $this->_mapper->getConfiguredFormContent($this->getRequest()->getParam('id'));
    		$form->populate($values);
    		$employeeId = $values['employee_id'];
    	}
    	$this->view->employee_id = $employeeId;
    	$this->view->form = $form;
    	$this->render('post');
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $employeeId = $this->getRequest()->getParam('employeeid');
      
        $this->_mapper->delete($id);
        
        $redirector = $this->_helper->getHelper('Redirector');
        $redirector->gotoRoute(
        	array(
        		'action' => 'index',
        		'controller' => 'Toil',
        		'module' => 'App',
        		'employeeid' => $employeeId
        	),
        	'module_full_path_employeeid',
        	true
        );
    }


}






