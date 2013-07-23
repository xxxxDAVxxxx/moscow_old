<?php
/**
 * @name ProjectsController
 * 
 * @desc Portfolio manager
 * 
 * @uses       AbstractController
 * @copyright  Copyright (c) at-plus
 * @license    http://www.at-plus.com/license
 * @author     Rustam Guseynov
 * @filesource /application/controllers/PortfolioController.php
 * @version    1.0
 */

/** @see AbstractController */
require_once APPLICATION_PATH . '/controllers/AbstractController.php';

class ProjectsController extends AbstractController {
	/**
	 * @var Portfolio_Instance
	 */
	protected $oProjects;
	
	/**
	 * Setup of global variables for this class
	 * 
	 * @return void
	 */
	public function init(){	
		require_once APPLICATION_PATH . '/models/Projects/Instance.php';	
		$this->oProjects = new Projects_Instance();
		$this->view->headScript()->appendFile(URL . 'js/share42.js');
		parent::init();
		$this->_helper->layout->setLayout('layout-admin');
		
	}
	/**
	 *  Index frontend page
	 *  
	 *  @return void
	 */
	public function indexAction(){		
		//print_r($this->_getAllParams()); exit();	
		//$this->_helper->layout->setLayout('layout');
    	$this->_helper->layout->setLayout('layout'); 
		parent::init();
    	$this->view->headScript()->appendFile(URL . 'js/grayscale.js');
    	$this->view->headScript()->appendFile(URL . 'js/atbuild.js');
    	$this->view->headLink()->appendStylesheet(URL . 'css/main.css');
		$page = $this->_getParam('page');
    	$this->view->page = $page;
    	$this->view->data = $this->oProjects->readProjects(Zend_Registry::get('config')->projects->perPage,$page);
    	$this->view->countPage = $this->oProjects->countPages(Zend_Registry::get('config')->projects->perPage);	
		
	}	
	public function viewAction(){		
		//print_r($this->_getAllParams()); exit();	
		//$this->_helper->layout->setLayout('layout');
		$this->_helper->layout->setLayout('layout'); 
		parent::init();
		//$this->view->headScript()->appendFile(URL . 'js/user.js');
		$this->view->headScript()->appendFile(URL . 'js/grayscale.js');
		$this->view->headScript()->appendFile(URL . 'js/atbuild.js');
    	$this->view->headLink()->appendStylesheet(URL . 'css/main.css');
    	//$this->view->headScript()->appendFile(URL . 'js/share42.js');
    	$slug = $this->_getParam('slug');

    	$project = $this->oProjects->get($slug);
    	$project['image']=unserialize($project['image']);
    	$this->view->content = $project;
  		// meta tags
  		$this->view->headTitle()->set($project['meta_t']);
		$this->view->headMeta()
			->setName('title', isset($project['meta_t']) ? $project['meta_t'] : '')
			->setName('description', isset($project['meta_d']) ? $project['meta_d'] : '')
			->setName('keywords', isset($project['meta_k']) ? $project['meta_k'] : '');	
    		
	}	
	
    public function readAction() {  
    	$this->view->headScript()->appendFile(URL . 'js/admin.js');
    	$this->view->headScript()->appendFile(URL . 'js/jquery.ajaxfileupload.js');
    	$this->view->headLink()->appendStylesheet(URL . 'css/admin.css');		
    	if (!isset($this->auth)) {
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin/login');	
    	}
    	$id = $this->_getParam('id');
 	
    	if ($id) {  		
    		$project = $this->oProjects->read($id);
    		if($project['image']){
    			$project['image']=unserialize($project['image']);
    		}
    		$this->view->project = $project;		
    		$this->render('create');	
    	}
    	else {
    		$page = $this->_getParam('apage');
    		$this->view->countPage = $this->oProjects->countPages(Zend_Registry::get('config')->admin_projects->perPage);
    		//print_r($this->oArticles->countPages(Zend_Registry::get('config')->admin_article->perPage,$type));exit();
    		if ($page) {
    			$this->view->page = $page;
    			$this->view->data = $this->oProjects->readProjects(Zend_Registry::get('config')->admin_projects->perPage,$page);
    		} else{
    			$this->view->data = $this->oProjects->readProjects(null,null);
    		}
    		//$this->view->projects = $this->oProjects->read(); 
    	}	
    }
    
/**
  * Create/update project image
  * 
  * @return void
  */
    public function imageAction() {       
    	$this->view->headScript()->appendFile(URL . 'js/jquery.ajaxfileupload.js');
    	$this->view->headScript()->appendFile(URL . 'js/admin.js');
    	try {        
		    // check auth rights
		    if (!$this->auth) {
		       throw new Exception('Access denied');
		    }    
		    
		    // edit article
		    $back = $this->oProjects->createImage($this->_getAllParams());
		    
	    } catch (Exception $e) {
		    $this->_helper->json(array(
		       'success' => false,
		       'error' => $e->getMessage()
		    ));   
	    } 
  		// send success response
     	$this->_helper->json(array(
				'success' => true,
				'src' => $back['src']."?rand=".rand(),
				'pid' => $back['pid']
		)); 
     
    }
    
    public function deleteimageAction() {       
    	$this->view->headScript()->appendFile(URL . 'js/jquery.ajaxfileupload.js');
    	$this->view->headScript()->appendFile(URL . 'js/admin.js');
    	try {        
		    // check auth rights
		    if (!$this->auth) {
		       throw new Exception('Access denied');
		    }    
		    
		    // edit article
		    $this->oProjects->deleteimage($this->_getAllParams());
		    
	    } catch (Exception $e) {
		    $this->_helper->json(array(
		       'success' => false,
		       'error' => $e->getMessage()
		    ));   
	    } 
  		// send success response
     	$this->_helper->json(array(
				'success' => true
		)); 
     
    }
    
    public function editAction() {  
    	$this->view->headScript()->appendFile(URL . 'js/admin.js');
    	$this->view->headScript()->appendFile(URL . 'js/jquery.ajaxfileupload.js');
    	$this->view->headLink()->appendStylesheet(URL . 'css/admin.css');	  		
    	try {	   				
	    	// check auth rights
	    	if (!$this->auth) {
	    		throw new Exception('Access denied');
	    	}   	
	    	
	    	// edit article
	    	
	    	$this->oProjects->edit($this->_getAllParams());
    	} catch (Exception $e) {
    		$this->jsonp(array(
    			'success' => false,
    			'error' => $e->getMessage()	
    		));		
    	}	
		// send success response
    	$this->jsonp(array(
    		'success' => true	
    	));	
    	
    }	
    
	public function createAction() {
		$this->view->headScript()->appendFile(URL . 'js/admin.js');
		$this->view->headScript()->appendFile(URL . 'js/jquery.ajaxfileupload.js');
    	// check auth rights
    	if (!$this->auth) {
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin/login');	
    	}
    	$this->view->headLink()->appendStylesheet(URL . 'css/admin.css');		
    }
	
        public function deleteAction() {   
        	$this->view->headScript()->appendFile(URL . 'js/admin.js');
    		$this->view->headLink()->appendStylesheet(URL . 'css/admin.css');	   		
	    	// validate rights
	   		if (!$this->auth) {
	   			throw new Exception('Access denied');
	    	}	    	
	    	// validate data
	    	$id = $this->_getParam('id');
	    	if (!$id) {
	    		throw new Exception('Invalid data');
	    	} 
	    	
	    	// delete article
	    	$this->oProjects->delete($id); 
	    	
	    	$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'projects/read');	 
    } 
}	