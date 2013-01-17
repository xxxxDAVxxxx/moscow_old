<?php
/**
 * @name AuthController
 * 
 * @desc Index renderer
 * 
 * @uses       AbstractController
 * @copyright  Copyright (c) at-plus
 * @license    http://www.at-plus.com/license
 * @author     Rustam Guseynov
 * @filesource /application/controllers/IndexController.php
 * @version    1.0
 */

/** @see AbstractController */
require_once APPLICATION_PATH . '/controllers/AbstractController.php';

class AuthController extends AbstractController {	
	/**
	 * Setup of global variables for this class
	 * 
	 * @return void
	 */
	protected $oAuth;
	public function init() {
		parent::init();	
		require_once APPLICATION_PATH . '/models/Auth/Instance.php';	
		$this->oAuth = new Auth_Instance();
		$this->_helper->layout->setLayout('layout');
		$this->view->headScript()->appendFile(URL . 'js/main.js');
    	$this->view->headLink()->appendStylesheet(URL . 'css/main.css');
	}		
 
    public function indexAction() {	
    	if ($this->auth) {
    		$this->view->user=$this->auth;
    	}
    }   
    
 	public function loginAction() {	
 		try{
 			$userId=$this->oAuth->login($this->_getAllParams());
 			if($userId){
	 			//$this->jsonp(array(
	 			//		'success' => true
	 			//)); 					
 				setcookie('AT_AUTH', $userId, time() + (3600 * 24 * 30), '/');;
	 			//$action = $this->getRequest()->getActionName();
	 			//$controller = $this->getRequest()->getControllerName();
	 			//$this->_redirect("http://moscow.atplus.com.ua/");
 				//if($this->auth) {  
    			
    			//}
 				$this->jsonp(array(
 						'success' => true
 				));
 			}
 		} catch (Exception $e) {
 			$this->jsonp(array(
 					'success' => false,
 					'error' => $e->getMessage()
 			));
 		}
    }
    
    public function logoutAction() {	
    	
    }
    
    public function registerAction() {
    	try{	
    		$this->oAuth->register($this->_getAllParams());
    		$this->jsonp(array(
    			'success' => true	
    		));
    	} catch (Exception $e) {
    		$this->jsonp(array(
    			'success' => false,
    			'error' => $e->getMessage()	
    		));		
    	}
    	/*$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL);*/	
    }
    public function roomAction() {	

    }
    
    public function confirmAction() {	
    	
    }
}
