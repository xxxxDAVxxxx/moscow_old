<?php
/**
 * @name SearchController
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

class SearchController extends AbstractController {	
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
 
    public function getAction() {	
    	
    }   
    
}
