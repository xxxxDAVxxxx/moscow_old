<?php
/**
 * @name AuthController
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

class AuthController extends AbstractController {	
	
	protected $oAuth;
	
	/**
	 * Setup of global variables for this class
	 * 
	 * @return void
	 */
	public function init() {
		parent::init();	
		require_once APPLICATION_PATH . '/models/Auth/Instance.php';	
		$this->oAuth = new Auth_Instance();
		$this->_helper->layout->setLayout('layout');
		$this->view->headScript()->appendFile(URL . 'js/main.js');
    	$this->view->headLink()->appendStylesheet(URL . 'css/main.css');
	}		
 
    public function indexAction() {	
    	if ($this->authData) {
    		$this->view->user = $this->authData;
    	}
    }   
    
 	public function loginAction() {	
 		try{
 			
 			$user = $this->oAuth->login(array(
				'email' => $this->_getParam('email'),
 				'password' => $this->_getParam('password'), 			
 			));
 			if($user){
	 			//$this->jsonp(array(
	 			//		'success' => true
	 			//)); 					
 				setcookie('AT_AUTH', $user, time() + (3600 * 24 * 30), '/');
	 			//$action = $this->getRequest()->getActionName();
	 			//$controller = $this->getRequest()->getControllerName();
	 			//$this->_redirect("http://moscow.atplus.com.ua/");
 				//if($this->auth) {  
    			
    			//}
 				$this->jsonp(array(
 						'success' => true
 				));
 			}else{
 				throw new Exception('User not found');
 			}
 		} catch (Exception $e) {
 			$this->jsonp(array(
 					'success' => false,
 					'error' => $e->getMessage()
 			));
 		}
    }
    
    public function logoutAction() {	
    	try{
  			$this->oAuth->logout();

 			$this->jsonp(array(
 					'success' => true
 			));
    	}catch (Exception $e) {
 			$this->jsonp(array(
 					'success' => false,
 					'error' => $e->getMessage()
 			));
 		}
    }
    
    
	public function checkuniqemailAction() {
    	try{
    		if($this->oAuth->checkuniqemail($this->_getParam('email'))){
	    		throw new Exception('Этот email уже зарегистрирован!');
    		}else{
    			$this->jsonp(array(
	    			'success' => true	
	    		));
    		}	
    		
    	} catch (Exception $e) {
    		$this->jsonp(array(
    			'success' => false,
    			'error' => iconv("CP1251", "UTF-8", $e->getMessage())	
    		));		
    	}
    }
    
	public function checkuniqcompanyAction() {
    	try{
    		$name = urldecode($this->_getParam('name'));
    		$cid = $this->_getParam('cid');
    		if(!isset($cid)){
    			$cid = 0;
    		}
    		$s = mb_detect_encoding($name);
    		$name = iconv($s, 'CP1251//TRANSLIT', $name);
    		
    		if($this->oAuth->checkuniqcompany($name,$cid)){
	    		throw new Exception('Компания с таким названием уже зарегистрирована!');
    		}else{
    			$this->jsonp(array(
	    			'success' => true
	    		));
    		}	
    		
    	} catch (Exception $e) {
    		$this->jsonp(array(
    			'success' => false,
    			'error' => iconv("CP1251", "UTF-8", $e->getMessage())	
    		));		
    	}
    }
    
    public function registerAction() {
    	try{
    		$errors = $this->oAuth->register($this->_getAllParams());
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
    	} catch (Exception $e) {
    		$this->jsonp(array(
    			'success' => false,
    			'error' => iconv("CP1251", "UTF-8", $e->getMessage())	
    		));		
    	}
    	/*$this->_helper->getHelper('Redirector')
				->setCode(303)
				->setExit(false)
				->setGotoUrl(URL);*/	
    }
    
    public function profileAction() {
    	try{
	    	if($this->authData){
	    		//$user=explode("|",base64_decode($this->auth));
	    		if($this->_getParam('id') == $this->authData['id']){
	    			$userDb = $this->oAuth->checkUsername(array('id' => $this->authData['id'],'person'=>$this->authData['person']));
	    			if($userDb['activated']){
	    				$this->view->companyData = $this->oAuth->get($this->_getParam('id'));
	    			}else{
	    				$this->_redirect(URL);
	    			}
	    		}else{
	    			$this->_redirect(URL);
	    		}
	    	}else{
	    		$this->_redirect(URL);
	    	}
		} catch (Exception $e) {
    		$this->jsonp(array(
    			'success' => false,
    			'error' => iconv("CP1251", "UTF-8", $e->getMessage())	
    		));		
    	}
    }

	public function sendpassrecoverAction() {
		try{
			/** @see Auth_Instance */
			require_once APPLICATION_PATH . '/models/Auth/Instance.php';	
			$oAuth = new Auth_Instance();
			$oAuth->sendPassRecover($this->_getParam('email'));
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
    
	public function passconfirmAction() {
		try{
			$data = array(
				'id' => $this->_getParam('id'),
				'code' => $this->_getParam('code')
			);
			/** @see Auth_Instance */
			require_once APPLICATION_PATH . '/models/Auth/Instance.php';	
			$oAuth = new Auth_Instance();
			$user = $oAuth->passConfirm($data);
			if(isset($user['id'])){
				//setcookie('AT_AUTH', '', 0, '/', '');
				$result = base64_encode($user['id']."|".$user['person']);
				setcookie('AT_AUTH', $result, time() + (3600 * 24 * 30), '/');
				$this->_redirect(URL."profile/id/".$user['id']);
			}
		} catch (Exception $e) {
    		$this->jsonp(array(
    			'success' => false,
    			'error' => iconv("CP1251", "UTF-8", $e->getMessage())	
    		));		
    	}
    }
    
	public function sendactivationAction() {
		try{
			//$this->view->headLink()->appendStylesheet(URL . 'css/main.css');
	    	$email = $this->_getParam('email');
	    	$person = urldecode($this->_getParam('person'));
	    	$s = mb_detect_encoding($person);
			$person = iconv($s, 'CP1251//TRANSLIT', $person);
			$user=explode("|",base64_decode($_COOKIE['AT_AUTH']));
    		$this->oAuth->sendActivation(array('id'=>$user[0],'email'=>$email,'person'=>$person));
			//if($result){
			//}else{
				//throw new Exception('Ссылка не действительна');
			//}
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
    
    public function activationAction() {
		try{
			//$this->view->headLink()->appendStylesheet(URL . 'css/main.css');
	    	$id = $this->_getParam('id');
	    	$code = $this->_getParam('code');
	    	require_once APPLICATION_PATH . '/models/Auth/Instance.php';	
			$oAuth = new Auth_Instance();
			$user = $oAuth->activation($id,$code);
			//$this->view->result = $oAuth->activation($id,$code);  
			if(isset($user)){
				//require_once APPLICATION_PATH . '/models/Auth/Instance.php';	
				//$oAuth = new Auth_Instance();
				//setcookie('AT_AUTH', '', 0, '/', '');
				//setcookie('AT_AUTH', json_encode($oAuth->get($id)), time() + (3600 * 24 * 30), '/');
				//$this->view->getHelper('Redirector')->gotoSimpleAndExit('index', 'index');
				//$this->_forward('index','index');
				$result = base64_encode($user['id']."|".$user['person']);
				setcookie('AT_AUTH', $result, time() + (3600 * 24 * 30), '/');
				$this->_redirect(URL."profile/id/".$user['id']);
			}else{
				throw new Exception('Ссылка не действительна');
			}
		} catch (Exception $e) {
    		$this->jsonp(array(
    			'success' => false,
    			'error' => iconv("CP1251", "UTF-8", $e->getMessage())	
    		));		
    	}
    }
}
