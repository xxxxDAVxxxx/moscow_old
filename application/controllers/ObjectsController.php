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
    		$oId = $this->_getParam('id');
    		if(isset($oId)){
    			$object = $this->mObjects->get($oId,$this->authData['id']);
    			if($object){
    				$this->view->object = $object;
    			}else{
    				$this->_redirect(URL.'objects/new');
    			}
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
    		 		
    		if (!isset($this->auth['id'])) {
    			throw new Exception('Access denied');
    		}    
    				
    		$object = $this->_getParam('object');   		
    		$this->mObjects->create($object, $this->authData['id']);
    		$this->jsonp(array(
    			'success' => true	
    		));
    	} catch (Exception $e) {
    		$this->jsonp(array(
    			'success' => false,
    			'error' => iconv("CP1251", "UTF-8", $e->getMessage())	
    		));		
    	}
    }  
    
    public function updateAction() {	
    	try{
    		$object = $this->_getParam('object');
    		$this->mObjects->update($object);
    		$this->jsonp(array(
    			'success' => true	
    		));
    	} catch (Exception $e) {
    		$this->jsonp(array(
    			'success' => false,
    			'error' => iconv("CP1251", "UTF-8", $e->getMessage())	
    		));		
    	}
    }  
 
}
