<?php
/**
 * @name ObjectsController
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

class ObjectsController extends AbstractController {	
	
	protected $mObjects;
	/**
	 * Setup of global variables for this class
	 * 
	 * @return void
	 */
	public function init() {
		parent::init();	
		$this->_helper->layout->setLayout('layout');
		$this->view->headScript()->appendFile(URL . 'js/main.js');
    	$this->view->headLink()->appendStylesheet(URL . 'css/main.css');
    	require_once APPLICATION_PATH . '/models/Objects/Instance.php';	
		$this->mObjects = new Objects_Instance();
	}	
 
    public function indexAction() {	
    	
    }   
 
    public function newAction() {	
    	try{
    		if (!isset($this->authData['id'])) {
    			throw new Exception('Access denied');
    		}else{ 
    			if(!$this->authData['activated']) {
	    			$this->_redirect(URL);
	    		}
	    		$oId = $this->_getParam('id');
	    		if(isset($oId)){
	    			$object = $this->mObjects->get($oId,$this->authData['id']);
	    			if($object){
	    				$this->view->object = $object;
	    			}else{
	    				$this->_redirect(URL.'objects/new');
	    			}
	    		}
	    		$this->view->objects = $this->mObjects->read(null,'id',$this->authData['id']);
    		}
    	} catch (Exception $e) {
    		$this->jsonp(array(
    			'success' => false,
    			'error' => iconv("CP1251", "UTF-8", $e->getMessage())	
    		));		
    	}
    }   
 
    public function createAction() {	
    	try{   
    		 		
    		if (!isset($this->authData['id'])) {
    			throw new Exception('Access denied');
    		}else{
	    		if (!$this->authData['activated']) {
	    			$this->_redirect(URL);
	    		}
    		    $object = $this->_getParam('object');   		
    			$errors = $this->mObjects->create($object, $this->authData['id']);
    			if(count($errors)==0){
	    			$this->jsonp(array(
		    			'success' => true	
		    		));
    			}else{
    				$this->jsonp(array(
    					'success' => false,
    					'error' => $errors	
    				));
    			}
    		}
    	} catch (Exception $e) {
    		$this->jsonp(array(
    			'success' => false,
    			'error' => iconv("CP1251", "UTF-8", $e->getMessage())	
    		));		
    	}
    }  
    
    public function updateAction() {	
    	try{
    		if (!isset($this->authData['id'])) {
    			throw new Exception('Access denied');
    		}else{
    			if(!$this->authData['activated']){
	    			$this->_redirect(URL);
	    		}
	    		$object = $this->_getParam('object');
	    		//$this->mObjects->update($object);
    			$errors = $this->mObjects->create($object, $this->authData['id']);
    			if(count($errors)==0){
	    			$this->jsonp(array(
		    			'success' => true	
		    		));
    			}else{
    				$this->jsonp(array(
    					'success' => false,
    					'error' => $errors	
    				));
    			}
    		}
    	} catch (Exception $e) {
    		$this->jsonp(array(
    			'success' => false,
    			'error' => iconv("CP1251", "UTF-8", $e->getMessage())	
    		));		
    	}
    }  
    
    public function getitemAction() {	
    	//header('Content-Type: text/html; charset=utf-8');
    	try{
    		if (!isset($this->authData['id'])) {
    			throw new Exception('Access denied');
    		}else{ 
    			if(!$this->authData['activated']) {
	    			$this->_redirect(URL);
	    		}
	    		
	    		$oId = $this->_getParam('oid');
	    		if(isset($oId)){
	    			$object = $this->mObjects->get($oId,$this->authData['id']);    			
	    			if($object){
//print_r($object); exit();	    				
//print_r(mb_detect_encoding($object['name'])); exit();	    				
	    				//$rusobject = array();
	    				//$rusFields = array('name', 'desc', 'location', 'arendators');
	    				//foreach($object as $key => $value){
	    				//	if (in_array($key, $rusFields)){
					   // 		$rusobject[$key] = $value;
						//	}
	    				//}
	    				$this->jsonp(array(
		    				'success' => true,
	    					'object' => $object
	    					
		    			));
	    			}
	    		}
    		}
    	} catch (Exception $e) {
    		$this->jsonp(array(
    			'success' => false,
    			'error' => iconv("CP1251", "UTF-8", $e->getMessage())	
    		));		
    	}
    }   
 
}
