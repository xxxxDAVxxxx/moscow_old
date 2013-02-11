<?php
/**
 * @name AdminController
 * 
 * @desc Admin renderer
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
	 * Content object
	 * 
	 * @var Content_Instance
	 */
	protected $oContent;
	

		

    

    

    
	/**
	 * Render company list page
	 * 
	 * @return void
	 */
    public function companyAction() {	
    	if (!$this->admin) {
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin');	
    	}
    	$id = $this->_getParam('id');
    	/** @see User_Instance */
		require_once APPLICATION_PATH . '/models/User/Instance.php';	
		$this->oUser = new User_Instance(); 
		if(!$id) {
			$this->view->user = $this->oUser->read(array('type' => 'company'));
		} elseif($id == "mail") {
			$cid = $this->_getParam('cid');
			$this->view->user = $this->oUser->read(array('type' => 'company', 'id' => $cid));
			$this->render('mail');
		} elseif($id == "types") {
	    	/** @see Content_Instance */
			require_once APPLICATION_PATH . '/models/Content/Instance.php';	
			$this->oContent = new Content_Instance(); 
    		$this->view->companyTypes = $this->oContent->readList(array('type' => 4));
			$this->render('companytypes');
		} elseif($id == "block") {
			$cid = $this->_getParam('cid');
			$this->oUser->block($cid);
			$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin/company');	
		}
    }
    
	/**
	 * Render company list page
	 * 
	 * @return void
	 */
    public function userAction() {	
    	if (!$this->admin) {
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin');	
    	}
    	$id = $this->_getParam('id');
    	/** @see User_Instance */
		require_once APPLICATION_PATH . '/models/User/Instance.php';	
		$this->oUser = new User_Instance(); 
		if(!$id) {
			$this->view->user = $this->oUser->read(array('type' => 'user'));
		} elseif($id == "mail") {
			$cid = $this->_getParam('cid');
			$this->view->user = $this->oUser->read(array('type' => 'user', 'id' => $cid));
			$this->render('mail');
		} elseif($id == "block") {
			$cid = $this->_getParam('cid');
			$this->oUser->block($cid);
			$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin/user');	
		}
    }
    
	/**
	 * Render content and banner list
	 * 
	 * @return void
	 */
    public function contentAction() {	
    	if (!$this->admin) {
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin');	
    	}
    	$id = $this->_getParam('id');
    	/** @see Content_Instance */
		require_once APPLICATION_PATH . '/models/Content/Instance.php';	
		$this->oContent = new Content_Instance(); 
		/** @see Banner_Instance */
		require_once APPLICATION_PATH . '/models/Banner/Instance.php';	
		$this->oBanner = new Banner_Instance();
		if(!$id) {
			$this->view->content = $this->oContent->readContent();
			$this->view->banners = $this->oBanner->read(array('position' => 1));
		} else {
			$this->view->content = $this->oContent->readContent($id);
			$this->render('contentedit');
		}
    }
    
	/**
	 * Render banner
	 * 
	 * @return void
	 */
    public function bannerAction() {	
    	if (!$this->admin) {
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin');	
    	}
    	$this->view->headScript()->appendFile(URL . 'js/jquery.ajaxfileupload.js');
    	$id = $this->_getParam('id');
    	$position = $this->_getParam('pos');
    	/** @see Content_Instance */
		require_once APPLICATION_PATH . '/models/Content/Instance.php';	
		$this->oContent = new Content_Instance(); 
    	$this->view->promotionTypes = $this->oContent->readList(array('type'=>7));
		/** @see Banner_Instance */
		require_once APPLICATION_PATH . '/models/Banner/Instance.php';	
		$this->oBanner = new Banner_Instance();
		if($id) {
			$this->view->banners = $this->oBanner->read(array('id' => $id));
		};
   		if($position) {
			$this->view->position = 'top';
		} else {
			$this->view->brands = $this->oContent->readList(array('type'=>2));
			$this->view->menu = $this->oContent->readList(array('type'=>1));
			$this->view->position = '';
		};
    }

	/**
	 * Render content and banner list
	 * 
	 * @return void
	 */
    public function promotionAction() {	
    	if (!$this->admin) {
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin');	
    	}
    	$id = $this->_getParam('id');
    	/** @see Content_Instance */
		require_once APPLICATION_PATH . '/models/Content/Instance.php';	
		$this->oContent = new Content_Instance(); 
		/** @see Banner_Instance */
		require_once APPLICATION_PATH . '/models/Banner/Instance.php';	
		$this->oBanner = new Banner_Instance();
		$this->view->brands = $this->oContent->readList(array('type'=>2));
		$this->view->menu = $this->oContent->readList(array('type'=>1));
		$this->view->promotionTypes = $this->oContent->readList(array('type'=>7));
		if(!$id) {
			$this->view->promotion = $this->oContent->readPromotion(array());
			$this->view->banners = $this->oBanner->read(array('position' => 'all'));
		} elseif($id == 'add') {
			$this->view->promotion = '';
			$this->render('promotionedit');
		} elseif($id == 'delete') {
			
		} else {
			$this->view->promotion = $this->oContent->readPromotion(array('id' => $id));
			$this->render('promotionedit');
		} 
    }
    
    /**
	 * Send letter
	 * 
	 * @return void
	 */
    public function sendAction() {
    	$infoEmail = Zend_Registry::get('config')->info->email;	    	
    	$data = $this->_getParam('data');
    	$mailData = array(
    		'email' => $data['email'],
    		'info' => $data['content'],
    		'title' => $data['title'] 			
    	);    		
    	// sending letter
    	/** @see Mail_Instance */
		require_once APPLICATION_PATH . '/models/Mail/Instance.php';			
		$oMail = new Mail_Instance(array('mailLogin' => $infoEmail));					
		$oMail->sendMail($mailData);
		$this->jsonp(array(
			'success' => true
		));
     }	

     /**
	 * Edit content
	 * 
	 * @return void
	 */
    public function contenteditAction() {	
    	$data = $this->_getParam('data');
    	try {
    		$this->oContent->updateContent($data);	
    	} catch (Exception $e) {
    		$this->jsonp(array(
				'success' => false,
    			'error' => $e->getMessage()	
			));
    	}
		$this->jsonp(array(
			'success' => true
		));
     }	
     
     /**
	 * Create/edit banner image
	 * 
	 * @return void
	 */
    public function bannerimageAction() {	
    	try {
    		if(isset($_FILES['banner-image'])) {
    			/** @see Banner_Instance */
				require_once APPLICATION_PATH . '/models/Banner/Instance.php';	
				$this->oBanner = new Banner_Instance();
				$callback = $this->oBanner->updateImage($this->_getAllParams());
    		}
    	} catch (Exception $e) {
    		$this->_helper->json(array(
				'success' => false,
    			'error' => $e->getMessage()	
			));
    	}
		$this->_helper->json(array(
			'success' => true,
			'url' => $callback['url'],
			'iid' => $callback['iid'],
		));
     }	
     
	/**
	 * Create/edit banner image
	 * 
	 * @return void
	 */
    public function bannereditAction() {	
    	try {
    		/** @see Banner_Instance */
			require_once APPLICATION_PATH . '/models/Banner/Instance.php';	
			$this->oBanner = new Banner_Instance();
			$callback = $this->oBanner->update($this->_getParam('data'));
    	} catch (Exception $e) {
    		$this->jsonp(array(
				'success' => false,
    			'error' => $e->getMessage()	
			));
    	}
		$this->jsonp(array(
			'success' => true
		));
     }	
     
     /**
	 * Create/edit banner image
	 * 
	 * @return void
	 */
    public function bannerdeleteAction() {	
    	/** @see Banner_Instance */
		require_once APPLICATION_PATH . '/models/Banner/Instance.php';	
		$this->oBanner = new Banner_Instance();
		$callback = $this->oBanner->delete($this->_getParam('id'));
		$from = $this->_getParam('from');
		$this->_helper->getHelper('Redirector')
			->setCode(303)
			->setExit(false)
			->setGotoUrl(URL . 'admin/'. $from);	
     }	
     
     /**
	 * Create/edit banner image
	 * 
	 * @return void
	 */
    public function promotioneditAction() {	
    	try {
			$this->oContent->updatePromotion($this->_getParam('data'));
    	} catch (Exception $e) {
    		$this->jsonp(array(
				'success' => false,
    			'error' => $e->getMessage()	
			));
    	}
		$this->jsonp(array(
			'success' => true
		));
     }	
     
     /**
	 * Create/edit banner image
	 * 
	 * @return void
	 */
    public function promotiondeleteAction() {	
		$this->oContent->deletePromotion($this->_getParam('id'));
		$this->_helper->getHelper('Redirector')
			->setCode(303)
			->setExit(false)
			->setGotoUrl(URL . 'admin/promotion');	
     }	
     
     /**
	 * Create/edit banner image
	 * 
	 * @return void
	 */
    public function companytypeeditAction() {	
    	try {
			$this->oContent->updateCompanyTypes($this->_getParam('data'));
    	} catch (Exception $e) {
    		$this->jsonp(array(
				'success' => false,
    			'error' => $e->getMessage()	
			));
    	}
		$this->jsonp(array(
			'success' => true
		));
     }	
     
     /**
	 * Create/edit banner image
	 * 
	 * @return void
	 */
    public function companytypedeleteAction() {	
		$this->oContent->deleteCompanyType($this->_getParam('id'));
		$this->_helper->getHelper('Redirector')
			->setCode(303)
			->setExit(false)
			->setGotoUrl(URL . 'admin/company/types');	
     }	
     
     /**
	 * Create/edit offer
	 * 
	 * @return void
	 */
    public function createofferAction() {	
		try {
			/** @see Offer_Instance */
			require_once APPLICATION_PATH . '/models/Offer/Instance.php';	
			$this->oOffer = new Offer_Instance(); 
	    	$this->oOffer->create($this->_getParam('data'));
    	} catch (Exception $e) {
    		$this->jsonp(array(
				'success' => false,
    			'error' => $e->getMessage()	
			));
    	}
		$this->jsonp(array(
			'success' => true
		));
     }	
     
     /**
	 * Transfer brands/models or body type to this db
	 * 
	 * @return void*/	
    public function dbchangeAction() {	
		require_once APPLICATION_PATH . '/models/Admin/Instance.php';	
		$this->oAdmin = new Admin_Instance(); 
		//$this->oAdmin->deleteFromDb();
    	//$this->oAdmin->brandsToDb();
    	//$this->oAdmin->bodyTypesToDb();
    	//$this->oAdmin->gearBoxToDb();
    	//$this->oAdmin->createBanners();
    }
    
	/**
	 * Render main page
	 * 
	 * @return void
	 */
    public function brandsAction() {	
    	if (!$this->admin) {
    		$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL . 'admin');	
    	}
    	
    	$id = $this->_getParam('id');
		if(!$id) {
			$this->view->brands = $this->oContent->readList(array('type'=>2));
		} else {
			$act = $this->_getParam('act');
			if($act == 'delete') {
				$this->oContent->deleteBrand($id);
				$this->_helper->getHelper('Redirector')
					->setCode(303)
					->setExit(false)
					->setGotoUrl(URL . 'admin/brands');	
			} else {
				$this->view->brands = $this->oContent->readList(array('type'=>2, 'id'=>$id));
				$this->render('brandedit');
			}
		}
    }
    
     /**
	 * Create/edit banner image
	 * 
	 * @return void
	 */
    public function modeleditAction() {	
    	try {
			$mid = $this->oContent->updateModel($this->_getParam('data'));
    	} catch (Exception $e) {
    		$this->jsonp(array(
				'success' => false,
    			'error' => $e->getMessage()	
			));
    	}
		$this->jsonp(array(
			'success' => true,
			'mid' => $mid
		));
     }	
     
     /**
	 * Create/edit banner image
	 * 
	 * @return void
	 */
    public function modeldeleteAction() {	
    	$bid = $this->oContent->deleteModel($this->_getParam('id'));
		$this->_helper->getHelper('Redirector')
			->setCode(303)
			->setExit(false)
			->setGotoUrl(URL . 'admin/brands/' . $bid);	
    }

    /**
	 * Create/edit banner image
	 * 
	 * @return void
	 */
    public function brandeditAction() {	
    	try {
			$bid = $this->oContent->updateBrand($this->_getParam('data'));
    	} catch (Exception $e) {
    		$this->jsonp(array(
				'success' => false,
    			'error' => $e->getMessage()	
			));
    	}
		$this->jsonp(array(
			'success' => true,
			'bid' => $bid
		));
     }	
}