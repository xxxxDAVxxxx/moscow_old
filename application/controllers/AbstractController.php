<?php
/**
 * @name AbstractController
 * 
 * @desc Basic abstract for all controllers.
 * 
 * 
 * @copyright  Copyright (c) at-plus
 * @license    http://www.at-plus.com/license
 * @author     Rustam Guseynov
 * @filesource /application/controllers/AbstractController.php
 * @version    1.0
 */
abstract class AbstractController extends Zend_Controller_Action {
	/**
	 * Database object
	 * 
	 * @var Zend_Db
	 */
	public $oDb;
	
	/**
	 * Auth data
	 * 
	 * @var int
	 */
	public $auth;
	
	
	/**
	 * Setup of global variables for this class
	 * 
	 * @return void
	 */
	public function init() {	
		// auth data
		$this->auth = isset($_COOKIE['AT_AUTH']) ? $_COOKIE['AT_AUTH'] : null;
		
		//database instances
		$this->oDb = Zend_Registry::get('db');	
		//head settings
		$this->view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=windows-1251');
		$this->view->headScript()
			->appendFile('https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js')
			->appendFile('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js')
			//->appendFile(URL . 'js/jquery.carouFredSel-5.6.4-packed.js')
			;
		$this->view->headMeta()->setName('google-site-verification', '4ZjHlZFvV27sl-WNaEpsEyrCv7tdTvWgSaFoQzth0pA');
		$this->view->setEncoding('windows-1251');
	}
	
	/**
	 * Sending response wrapped for JSONP
	 * 
	 * @return void
	 */
	public function jsonp($data) {
		$content = Zend_Json::encode($data);
		$content = $this->_getParam('callback') . '(' . $content . ')';

		$this->getHelper('viewRenderer')->setNoRender();
		$response = $this->getResponse();
        $response
        	->setBody($content)
            ->setHeader('Content-Type', 'application/x-javascript; charset=utf-8', true)
            ->sendResponse();
       	exit();
	}
	
}