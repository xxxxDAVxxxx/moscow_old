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
    	/*try {
	    	require_once APPLICATION_PATH . '/models/Auth/Instance.php';	
			$a = new Auth_Instance();
			$data=$this->_getAllParams();
			if(isset($data['name'])){
				$a->register($data);
			}
		} catch (Exception $e) {
				$this->jsonp(array(
    				'success' => false,
    				'error' => $e->getMessage()
    			));		
		}*/
    }   
 
    public function meAction() {	
    	
    }   
 
    
}
