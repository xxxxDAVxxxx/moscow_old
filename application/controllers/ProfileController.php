<?php
/**
 * @name ProfileController
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

class ProfileController extends AbstractController {	

	/**
	 * Setup of global variables for this class
	 * 
	 * @return void
	 */
	public function init() {
		parent::init();	
		//require_once APPLICATION_PATH . '/models/Auth/Instance.php';	
		//$this->oAuth = new Auth_Instance();
		$this->_helper->layout->setLayout('layout');
		$this->view->headScript()->appendFile(URL . 'js/main.js');
    	$this->view->headLink()->appendStylesheet(URL . 'css/main.css');
    	
	}		
 
    public function indexAction(){
    	try{
	    	if($this->authData){
	    		$user=$this->authData;
	    		if($this->_getParam('id') == $user['id']){
	    			require_once APPLICATION_PATH . '/models/Auth/Instance.php';	
					$oAuth = new Auth_Instance();
	    			$userDb = $oAuth->checkUsername(array('id' => $user['id'],'person'=>$user['person']));
	    			if($userDb['activated']){
	    				
	    				$this->view->companyData = $oAuth->get($this->_getParam('id'));
	    				
	    				require_once APPLICATION_PATH . '/models/Objects/Instance.php';	
						$mObject = new Objects_Instance();
	    				$this->view->objects = $mObject->read(null,'id',$user['id']);
	    				if (isset($this->view->objects[0])) {
	    					$this->view->object = $mObject->get($this->view->objects[0]['id']);	
	    				}		
	    				else {
	    					$this->view->object = null;
	    				}	
	    				
	    				
	    			}else{
	    				$this->_redirect(URL);
	    			}
	    		}else{
	    			$this->_redirect(URL);
	    		}
	    	}else{
	    		$this->_redirect(URL);
	    	}
	    	
		} catch (Exception $e) {
    		$this->jsonp(array(
    			'success' => false,
    			'error' => iconv("CP1251", "UTF-8", $e->getMessage())	
    		));		
    	}
    }
}
