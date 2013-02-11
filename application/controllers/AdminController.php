<?php
/**
 * @name AdminController
 * 
 * @desc Admin renderer
 * 
 * @uses       AbstractController
 * @copyright  Copyright (c) at-plus
 * @license    http://www.at-plus.com/license
 * @author     Rustam Guseynov
 * @filesource /application/controllers/AdminController.php
 * @version    1.0
 */

/** @see AbstractController */
require_once APPLICATION_PATH . '/controllers/AbstractController.php';

class AdminController extends AbstractController {	
			
	/**
	 * Setup of global variables for this class
	 * 
	 * @return void
	 */
	
	public function init() {
		parent::init();
		$this->view->headLink()->appendStylesheet(URL . 'css/admin.css');
		$this->_helper->layout->setLayout('layout-admin');	
		$this->view->headScript()->appendFile(URL . 'js/admin.js');
	}	
	
	/**
	 * Render main page
	 * 
	 * @return void
	 */
    public function indexAction() {	
    	// check auth rights
    	if($this->admin) {  
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin/profile');
    	}
    	
    	/** @see User_Instance */
		require_once APPLICATION_PATH . '/models/User/Instance.php';	
		$this->oUser = new User_Instance(); 
    	
    	$reqData = $this->_getAllParams();	 	

    	if (isset($reqData['login'])) {
    		$userId = $this->oUser->read(array('email' => $reqData['login'], 'password' => $reqData['pass'], 'type' => 'admin'));
    		if ($userId) {
    			// set cookie
				setcookie('AVTO_ADMIN_AUTH', $userId[0]['id'], time() + (3600 * 24 * 30), '/');
				$this->_helper->getHelper('Redirector')
					->setCode(303)
					->setExit(false)
					->setGotoUrl(URL . 'admin/profile');
    		}
    		else {
    			die('User not found');
    		}
    	} 	
    }
    
	/**
	 * Render profile page
	 * 
	 * @return void
	 */
    public function profileAction() {	
    	$userData = null;   	
    	/** @see User_Instance */
		require_once APPLICATION_PATH . '/models/User/Instance.php';	
		$this->oUser = new User_Instance(); 
    	// check auth rights
    	if ($this->admin) {
    		$userData = $this->oUser->read(array('id' => $_COOKIE['AVTO_ADMIN_AUTH'], 'type' => 'admin')); 		
    		$this->view->user = $userData; 
    	}	
    	if (!$userData) {    		
    		// delete auth cookie
    		setcookie('AVTO_ADMIN_AUTH', '', 0, '/', '');
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin');	
    	}
    	$userGetData = $this->_getAllParams();
    	if (isset($userGetData['profile-id'])) {
    		$this->oUser->adminUpdate($userGetData);
    	}
    }
    
	/**
	 * Admin exit
	 * 
	 * @return void
	 */
    public function exitAction() {
		setcookie('AVTO_ADMIN_AUTH', '', 0, '/', '');
    	$this->_helper->getHelper('Redirector')
			->setCode(303)
			->setExit(false)
			->setGotoUrl(URL . 'admin');	
    }
}