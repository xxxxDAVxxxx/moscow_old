<?php
/**
 * @name AdminController
 * 
 * @desc Admin manager
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
		$this->_helper->layout->setLayout('layout-admin');	
		parent::init();
		$this->view->headScript()->appendFile(URL . 'js/admin.js');
		$this->view->headLink()->appendStylesheet(URL . 'css/admin.css');	
	}
	
	
	/**
	 * Admin page render
	 * 
	 * @return void
	 */
    public function indexAction() {
    	// check auth rights
    	if($this->auth) {  
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin/profile');
    	}
    }
    
	/**
	 * Admin login page
	 * 
	 * @return void
	 */
    public function loginAction() {
    	// check auth rights
    	if($this->auth) {  
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin/profile');
    	}
    	
    	/** @see User_Instance */
    	require_once APPLICATION_PATH . '/models/User/Instance.php';
    	$oUser = new User_Instance();
 	
    	$reqData = $this->_getAllParams();	 	
    	
    	if (isset($reqData['login'])) {
    		try {
	    		$userId = $oUser->check($reqData);
	    		if ($userId) {
	    			// set cookie
					setcookie('AT_AUTH', $userId, time() + (3600 * 24 * 30), '/');
					$this->_helper->getHelper('Redirector')
						->setCode(303)
						->setExit(false)
						->setGotoUrl(URL . 'admin/profile');
	    		}
    		} catch (Exception $e) {
    			print_r($e->getMessage()); exit();    			
    		}    			
    	} else {
    		die('User not found');
    	} 	
    }
    
	/**
	 * Admin profile page
	 * 
	 * @return void
	 */
    public function profileAction() { 
    	$userData = null;
    	/** @see User_Instance */
    	require_once APPLICATION_PATH . '/models/User/Instance.php';
    	$oUser = new User_Instance();
    	// check auth rights
    	if ($this->auth) {
    		
    		$userData = $oUser->get($_COOKIE['AT_AUTH']);    		
    		$this->view->page = $userData; 
    		
    	}	
    	if (!$userData) {    		
    		// delete auth cookie
    		setcookie('AT_AUTH', '', 0, '/', '');
    		
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin/login');	
    	}
    	$userData = $this->_getAllParams();
    	$oUser->settodb($userData);  
    }
    /**
     * SEO read static pages
     * 
     * @return void
     */
    public function readAction(){
		/** @see Seo_Instance */
    	require_once APPLICATION_PATH . '/models/Seo/Instance.php';
    	$oSeo = new Seo_Instance();
    	// check auth data
        if (!$this->auth) {
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin/login');	
    	}
    	$id = $this->_getParam('id');
    	if ($id){  		
    		$this->view->read = $oSeo->read($id);
      		$this->render('create');
    	}
    	else {
    		$this->view->read = $oSeo->read();
    	}
    }
    /**
     * Edit SEO Data
     * 
     * @return void
     */
    public function editAction() {    	 	
    	/** @see Seo_Instance */
    	require_once APPLICATION_PATH . '/models/Seo/Instance.php';
    	$oSeo = new Seo_Instance();   	
    	try {	   				
	    	// check auth rights
	    	if (!$this->auth) {
	    		throw new Exception('Access denied');
	    	}    	
			// edit seo data
			//die('Доходит до сюда');
	    	$oSeo->edit($this->_getAllParams());  	    	
    	} catch (Exception $e) {
    		print_r($e); exit();	
    	}	
    	$this->_helper->layout()->disableLayout(); 
    	$this->_helper->viewRenderer->setNoRender(true);	
    }
    
	/**
	 * Article add page
	 * 
	 * @return void
	 */
    public function addarticleAction() {   	
    	// check auth rights
    	if (!$this->auth) {
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin/login');	
    	}
    	$addArticle = null;    	
    	/** @see User_Instance */
    	require_once APPLICATION_PATH . '/models/User/Instance.php';
    	$oArticle = new User_Instance();
    	
    	$this->view->page = $oArticle->getrubric();
    	
    	
    	$addArticle = $this->_getAllParams();
    	$oArticle->arttodb($addArticle);    	
    }
	/**
	 * File biblio page
	 * 
	 * @return void
	 */
    public function mediafilelistAction() {
    	  	
    }
	/**
	 * Article biblio page
	 * 
	 * @return void
	 */
    public function editarticleAction() {
    	/** @see User_Instance */
    	require_once APPLICATION_PATH . '/models/User/Instance.php';
    	$oArticle = new User_Instance();
    	$addArticle = null;
    	if (!isset($_COOKIE['AT_AUTH'])) {
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin/login');	
    	}
    	$this->view->articles = $oArticle->getarticles();		
    }
	/**
	 * Article delete page
	 * 
	 * @return void
	 */
    public function delarticleAction() {
    	  	
    }
}
