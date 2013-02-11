<?php
/**
 * @name ArticlesController
 * 
 * @desc Articles manager
 * 
 * @uses       AbstractController
 * @copyright  Copyright (c) at-plus
 * @license    http://www.at-plus.com/license
 * @author     Rustam Guseynov
 * @filesource /application/controllers/ArticlesController.php
 * @version    1.0
 */

/** @see AbstractController */
require_once APPLICATION_PATH . '/controllers/AbstractController.php';

class ArticlesController extends AbstractController {
	
	/**
	 * @var Inner_Instance
	 */
	protected $oArticles;
	
	/**
	 * Setup of global variables for this class
	 * 
	 * @return void
	 */
	public function init() {
		/** @see Articles_Instance */
		require_once APPLICATION_PATH . '/models/Articles/Instance.php';	
		$this->oArticles = new Articles_Instance();
		$this->view->headScript()->appendFile(URL . 'js/share42.js');
		parent::init();
		$this->_helper->layout->setLayout('layout-admin');
	}
   /**
	 * Render articles page
	 * 
	 * @return void
	 */
    public function indexAction() { 
    	$this->_helper->layout->setLayout('layout'); 
		parent::init();
		//$this->view->headScript()->appendFile(URL . 'js/user.js');
		$this->view->headScript()->appendFile(URL . 'js/atbuild.js');
    	$this->view->headLink()->appendStylesheet(URL . 'css/main.css');
    	//$this->view->headScript()->appendFile(URL . 'js/share42.js');
		$page = $this->_getParam('page');
    	$this->view->page = $page;
    	$this->view->data = $this->oArticles->readArticles(Zend_Registry::get('config')->article->perPage,$page,'news');
    	$this->view->countPage = $this->oArticles->countPages(Zend_Registry::get('config')->article->perPage);	
    }
   /**
	 * Render articles page
	 * 
	 * @return void
	 */
    public function innerAction() { 
    	$this->_helper->layout->setLayout('layout'); 
		parent::init();
		//$this->view->headScript()->appendFile(URL . 'js/user.js');
		$this->view->headScript()->appendFile(URL . 'js/atbuild.js');
    	$this->view->headLink()->appendStylesheet(URL . 'css/main.css');
    	//$this->view->headScript()->appendFile(URL . 'js/share42.js');
    	$slug = $this->_getParam('slug');
		//print_r($this->oArticles->getAutoIncrement());exit();
    	$article = $this->oArticles->get($slug);
    	$other_articles=$this->oArticles->readArticles(3,1,'news');
    	$this->view->content = $article;
    	$this->view->other_articles = $other_articles;
    	
  		// meta tags
  		$this->view->headTitle()->set($article['meta_t']);
		$this->view->headMeta()
			->setName('title', isset($article['meta_t']) ? $article['meta_t'] : '')
			->setName('description', isset($article['meta_d']) ? $article['meta_d'] : '')
			->setName('keywords', isset($article['meta_k']) ? $article['meta_k'] : '');	
    }
    
    
	/**
	 * Create render page
	 * 
	 * @return void
	 */
    public function createAction() {
    	$this->view->headScript()->appendFile(URL . 'js/jquery.ajaxfileupload.js');
    	$this->view->headScript()->appendFile(URL . 'js/admin.js');
    	// check auth rights
    	if (!$this->auth) {
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin/login');	
    	}
    	$this->view->headLink()->appendStylesheet(URL . 'css/admin.css');		
    }
	
	/**
	 * Create/update article logic
	 * 
	 * @return void
	 */
    public function editAction() {    	
    	$this->view->headLink()->appendStylesheet(URL . 'css/admin.css');	
    	$this->view->headScript()->appendFile(URL . 'js/jquery.ajaxfileupload.js');
    	$this->view->headScript()->appendFile(URL . 'js/admin.js');
    	try {	   				
	    	// check auth rights
	    	if (!$this->auth) {
	    		throw new Exception('Access denied');
	    	}   	
	    	
	    	// edit article
	    	
	    	$this->oArticles->edit($this->_getAllParams());
	    	
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
	
/**
  * Create/update article image
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
     		 $back = $this->oArticles->createImage($this->_getAllParams());
     		 
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
      		'aid' => $back['aid']
     	));
	}
    
	public function servicesAction() {  	
    	$this->_helper->layout->setLayout('layout'); 
		parent::init();
		$this->view->headScript()->appendFile(URL . 'js/grayscale.js');
		$this->view->headScript()->appendFile(URL . 'js/atbuild.js');
    	$this->view->headLink()->appendStylesheet(URL . 'css/main.css');
		//$page = $this->_getParam('page');
    	//$this->view->page = $page;
    	$this->view->data = $this->oArticles->readArticles(6,1,'services');
    	//$this->view->countPage = $this->oArticles->countPages(Zend_Registry::get('config')->article->perPage);	
    }
    
	public function serviceAction() {  	
    	$this->_helper->layout->setLayout('layout'); 
		parent::init();
		$this->view->headScript()->appendFile(URL . 'js/atbuild.js');
    	$this->view->headLink()->appendStylesheet(URL . 'css/main.css');
    	$slug = $this->_getParam('slug');
		//print_r($slug);exit();
    	$article = $this->oArticles->get($slug);
    	$this->view->service = $article;
  		// meta tags
  		$this->view->headTitle()->set($article['meta_t']);
		$this->view->headMeta()
			->setName('title', isset($article['meta_t']) ? $article['meta_t'] : '')
			->setName('description', isset($article['meta_d']) ? $article['meta_d'] : '')
			->setName('keywords', isset($article['meta_k']) ? $article['meta_k'] : '');
    }
	
	/**
	 * Read articles
	 * 
	 * @return void
	 */
    public function readAction() {  
    	$this->view->headScript()->appendFile(URL . 'js/jquery.ajaxfileupload.js');
    	$this->view->headScript()->appendFile(URL . 'js/admin.js');
    	$this->view->headLink()->appendStylesheet(URL . 'css/admin.css');		
    	if (!isset($this->auth)) {
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin/login');	
    	}
    	$id = $this->_getParam('id');
 		//
    	if ($id) {  		
    		$this->view->article = $this->oArticles->read($id);	    		   			
    		$this->render('create');	
    	}
    	else {
    		$type = $this->_getParam('type');
    		if(!$type){
    			//print_r('yo');exit();
    			$type = '';
    		}
    		$page = $this->_getParam('apage');
    		$this->view->countPage = $this->oArticles->countPages(Zend_Registry::get('config')->admin_article->perPage,$type);
    		//print_r($this->oArticles->countPages(Zend_Registry::get('config')->admin_article->perPage,$type));exit();
    		if ($page) {
    			$this->view->type = $type;
    			$this->view->page = $page;
    			$this->view->data = $this->oArticles->readArticles(Zend_Registry::get('config')->admin_article->perPage,$page,$type);
    		} else{
    			$this->view->data = $this->oArticles->readArticles(null,null,$type);
    		}
    	}	
    }
    
	/**
	 * Delete article
	 * 
	 * @return void
	 */
    public function deleteAction() {   
	    	$this->view->headScript()->appendFile(URL . 'js/jquery.ajaxfileupload.js');
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
	    	$type = $this->_getParam('type');
    		if(!$type){
    			//print_r('yo');exit();
    			$type = '';
    		} else{
    			$type = "?type=".$type;
    		}
	    	// delete article
	    	$this->oArticles->delete($id); 
	    	
	    	$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'articles/read'.$type);	 
    } 


    
}