<?php
/**
 * @name IndexController
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

class IndexController extends AbstractController {	
	/**
	 * Setup of global variables for this class
	 * 
	 * @return void
	 */
	public function init() {
		parent::init();	
		$this->view->headScript()->appendFile(URL . 'js/main.js');
    	$this->view->headLink()->appendStylesheet(URL . 'css/main.css');
	}		
 
    public function indexAction() {	
    	try{
    		require_once APPLICATION_PATH . '/models/Objects/Instance.php';	
    		$mObjs = new Objects_Instance();
    		$this->view->newObjects = $mObjs->read(null,'id',null,5);
    		$this->view->popObjects = $mObjs->read(null,'rating',null,5);
    		$this->view->aObjects = $mObjs->read('A','id',null,5);
    		$this->view->bObjects = $mObjs->read('B','id',null,5);
    		$this->view->cObjects = $mObjs->read('C','id',null,5);
    	} catch (Exception $e) {
    		$this->jsonp(array(
    			'success' => false,
    			'error' => iconv("CP1251", "UTF-8", $e->getMessage())	
    		));		
    	}
    }   
 
    public function meAction() {	

    }   
 
	public function checkcaptchaAction() {
    	session_start();
    	$code = $this->_getParam('code');
	   	if(isset($code) && ($code == $_SESSION['random_number'])){
	   		$this->_helper->json('1'); 
	   	}
	   	else {
	   		$this->_helper->json('0'); 
	   	}	
    }
    
    public function testAction() {	
   		 try{
    		require_once APPLICATION_PATH . '/models/Objects/Instance.php';	
    		$mObjs = new Objects_Instance();
    		//print_r();exit();
   			$this->jsonp(array(
    			'success' => true,
   				'data' => $mObjs->read(null,null)	
    		));
    	} catch (Exception $e) {
    		$this->jsonp(array(
    			'success' => false,
    			'error' => iconv("CP1251", "UTF-8", $e->getMessage())	
    		));		
    	}
    }  
    
}
