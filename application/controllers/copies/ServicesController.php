<?php
/**
 * @name ServicesController
 * 
 * @desc Services manager
 * 
 * @uses       AbstractController
 * @copyright  Copyright (c) at-plus
 * @license    http://www.at-plus.com/license
 * @author     Rustam Guseynov
 * @filesource /application/controllers/ServicesController.php
 * @version    1.0
 */

/** @see AbstractController */
require_once APPLICATION_PATH . '/controllers/AbstractController.php';

class ServicesController extends AbstractController {
	
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
		parent::init();
		$this->_helper->layout->setLayout('layout-admin');
		$this->view->headScript()->appendFile(URL . 'js/admin.js');
	}
    public function indexAction() {  	
    	$this->_helper->layout->setLayout('layout'); 
		parent::init();
		$this->view->headScript()->appendFile(URL . 'js/user.js');
    	$this->view->headLink()->appendStylesheet(URL . 'css/main.css');
    	$this->view->headScript()->appendFile(URL . 'js/share42.js');
		//$page = $this->_getParam('page');
    	//$this->view->page = $page;
    	$this->view->data = $this->oArticles->readArticles(6,1,'services');
    	//$this->view->countPage = $this->oArticles->countPages(Zend_Registry::get('config')->article->perPage);	
    	
    }
    public function viewAction() {  	
    	$this->_helper->layout->setLayout('layout'); 
		parent::init();
		$this->view->headScript()->appendFile(URL . 'js/user.js');
    	$this->view->headLink()->appendStylesheet(URL . 'css/main.css');
    	$this->view->headScript()->appendFile(URL . 'js/share42.js');
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
    
    
}
