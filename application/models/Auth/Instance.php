<?php
/**
 * @name User_Instance
 * 
 * @desc User manager
 * 
 * @uses	   Abstract
 * @category   Page
 * @copyright  Copyright (c) at-plus
 * @license    http://www.wm-software.com/wmsystem/license
 * @author     Rustam Guseynov
 * @filesource /application/models/User/Instance.php
 * @version    1.0
 */

/** @see Abstract */
require_once APPLICATION_PATH.'/models/Abstract.php';

class Auth_Instance extends AbstractModel {
	
	public function register($data){
		$passconfirm = urldecode($data['passconfirm']);
		$s = mb_detect_encoding($passconfirm);
		$passconfirm = iconv($s, 'CP1251//TRANSLIT', $passconfirm);
		
		if(isset($data['cid'])){
			$cid = $data['cid'];
		}else{
			$cid = 0;
		}
		
		$CompanyData = array(
			'name' => urldecode($data['name']),
			'password' => urldecode($data['password']),
			'email' => urldecode($data['email']), 
			'desc' => urldecode($data['desc']), 
			'person' => urldecode($data['person']),
			'site' => urldecode($data['site']),
			'phone' => urldecode($data['phone']),
			'skype' => urldecode($data['skype']),
			'icq' => urldecode($data['icq'])
		);
		$errorsData = array();
		$requiredFields = array('name', 'email', 'person');
		if(!($cid>0)){
			if($CompanyData['password'] == ""){
				$errorsData['password'] = iconv('CP1251', 'UTF-8', "Поле обязательно к заполнению!");
			}
			if($passconfirm==""){
				$errorsData['passconfirm'] = iconv('CP1251', 'UTF-8', "Поле обязательно к заполнению!");
			}
		}
		foreach ($CompanyData as $key => $value) {		
			if (in_array($key, $requiredFields)) {
				if($value==""){
					$errorsData[$key] = iconv('CP1251', 'UTF-8', "Поле обязательно к заполнению!");
				}
				//$s = mb_detect_encoding($value);
				//$CompanyData[$key] = iconv($s, 'CP1251//TRANSLIT', $value);
			}
		}
		if(count($errorsData)>0){return $errorsData;}
		
		if(!preg_match('|([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is', $CompanyData['email'])){
			$errorsData['email'] = iconv('CP1251', 'UTF-8', "Некорректный email адрес!");
		};
		
		$rusFields = array('name', 'password', 'email','desc', 'person', 'site', 'phone','skype','icq');
		foreach ($CompanyData as $key => $value) {		
			if (in_array($key, $rusFields)) {
			    $s = mb_detect_encoding($value);
				$CompanyData[$key] = iconv($s, 'CP1251//TRANSLIT', $value);
			}
		}
				
		if($CompanyData['password'] != $passconfirm){
			$errorsData['passconfirm'] = iconv('CP1251', 'UTF-8', "Пароли не совпадают!");
		}
		
		if(count($errorsData)>0){return $errorsData;}
		
		if(!($cid>0)){
			if($this->checkUniqEmail($CompanyData['email'])){
				$errorsData['email'] = iconv('CP1251', 'UTF-8', "Этот email уже зарегистрирован!");
			}
		}
		if($this->checkUniqCompany($CompanyData['name'],$cid)){
			$errorsData['name'] = iconv('CP1251', 'UTF-8', "Компания с таким названием уже зарегистрирована!");
		}
		
		if(count($errorsData)>0){return $errorsData;}
		if(!($cid>0)){
			$CompanyData['password'] = md5($CompanyData['password']);
		}else{
			if($CompanyData['password'] != ""){
				$CompanyData['password'] = md5($CompanyData['password']);
			}else{
				unset($CompanyData['password']);
			}
		}
		if(!($cid>0)){		
			if($this->oDb->insert('companies', $CompanyData)){
				$id = $this->oDb->lastinsertid();
				$activationData = array(
					'id' => $id,
					'person' => $CompanyData['person'],
					'email' => $CompanyData['email']
				);
				$this->sendActivation($activationData);
				$user = base64_encode($id."|".$CompanyData['person']);
				setcookie('AT_AUTH', $user, time() + (3600 * 24 * 30), '/');
			}
		}else{
			$email = $CompanyData['email'];
			if($this->oDb->update('companies', $CompanyData, "id = $cid AND email = '$email'")){
				$user = base64_encode($cid."|".$CompanyData['person']);
				setcookie('AT_AUTH', $user, time() + (3600 * 24 * 30), '/');
			}
		}
		
		// @see Mail_Instance 
		/*$activationData = array(
			'id' => $this->oDb->lastinsertid(),
			'code' => $CompanyData['code']
		);
		$mailData = array(
			'email' => $CompanyData['email'],
			'username' => $CompanyData['name'],
			'title' => 'Активация аккаунта на сайте nedvizhimost.com',
			'viewParams' => $activationData
		);
		require_once APPLICATION_PATH . '/models/Mail/Instance.php';	
		$oMail = new Mail_Instance();
		$oMail->sendMail("activation", $mailData, 'CP1251');
		*/		
	}
	
	public function checkUniqEmail($email){
		return $this->oDb->fetchOne($this->oDb->select()
			->from('companies')
			->where('email = ?', $email)
		);	
	}
	
	public function checkUniqCompany($name,$id = ''){
		//print_r($name." ".$id);exit();
		return $this->oDb->fetchOne($this->oDb->select()
			->from('companies')
			->where('name = ?', $name)
			->where('id <> ?', $id)
		);	
	}
	
	
	public function sendActivation($data){
		$company = $this->oDb->fetchRow($this->oDb->select()
				->forUpdate()
				->from('companies')
				->where("email = ?", $data['email'])
				->where("person = ?", $data['person'])
		);
		if(!$company){
			throw new Exception('User not found');
		}
		if($company['id'] != $data['id']){
			throw new Exception('Это не ваш email адрес!');
		}
		$code = uniqid();
		$company['code'] = $code;
		$this->oDb->update('companies', $company, array('email = ?' => $data['email'],'person = ?' => $data['person']));
		
		$activationData = array(
			'id' => $company['id'],
			'code' => $company['code']
		);
		$mailData = array(
			'email' => $company['email'],
			'username' => $company['name'],
			'title' => 'Активация аккаунта на сайте nedvizhimost.com',
			'viewParams' => $activationData
		);
		require_once APPLICATION_PATH . '/models/Mail/Instance.php';	
		$oMail = new Mail_Instance();
		$oMail->sendMail("activation", $mailData, 'CP1251');
	}
	
	public function login($data) {
		$LoginData = array(
				'email' => urldecode($data['email']),
				'password' => urldecode($data['password'])
		);
		$rusFields = array('email', 'password');
		foreach ($LoginData as $key => $value) {
			if (in_array($key, $rusFields)) {
				$s = mb_detect_encoding($value);
				$LoginData[$key] = iconv($s, 'CP1251//TRANSLIT', $value);
			}
		}
		$user = $this->oDb->fetchRow($this->oDb->select()
				->from('companies', array('id','person'))
				->where("email = ?", $LoginData['email'])
				->where("password = ?", md5($LoginData['password']))
		);
		return $user ? base64_encode($user['id']."|".$user['person']) : false;//$user['id']." ".$user['person']//)
	}
	
	public function logout(){
		setcookie('AT_AUTH', '', 0, '/', '');
	}
	
	public function activation($id, $code){
		$data = array(
		    'activated'      => '1',
			'code' => null
		);
		$where = "id = $id AND code='$code'"; 
		if($this->oDb->update('companies', $data, $where)){
			$result = $this->oDb->fetchRow($this->oDb->select()
						->from('companies', array('id','person'))
						->where("id = ?", $id)
					);
		}
		return $result;
	}
	
	public function checkUsername($data){
		$userId = $this->oDb->fetchRow($this->oDb->select()
			->from('companies',array('id' => 'id','person' => 'person','activated' => 'activated'))
			->where("id = ?", $data['id'])
			->where("person = ?", $data['person'])
		);
		return $userId ? $userId : false;
	}
	
	public function sendPassRecover($email) {
		
		$data = $this->oDb->fetchRow($this->oDb->select()
			->from('companies')
			->where('email = ?', $email)
			//->where('activated = ?', 1)
		);	
				
		if (!$data) {			
			throw new Exception('Invalid data');
		}
		
		// update database
		$number = uniqid();
		$where = $this->oDb->quoteInto('id = ?', $data['id']);
		$this->oDb->update('companies', array('code' => $number), $where);

    	$passRecoverData = array(
			'id' => $data['id'],
			'code' => $number
		);
		$mailData = array(
			'email' => $data['email'],
			'username' => $data['name'],
			'title' => 'Подтверждение пароля на сайте nedvizhimost.com',
			'viewParams' => $passRecoverData
		);
    	// sending letter
    	/** @see Mail_Instance */
		require_once APPLICATION_PATH . '/models/Mail/Instance.php';			
		$oMail = new Mail_Instance();					
		$oMail->sendMail('passrecover',$mailData,'CP1251');
	}	
	
	public function passConfirm($data) {
		$id = $data['id'];
		$code = $data['code'];
		$resultData = $this->oDb->fetchRow($this->oDb->select()
			->from('companies')
			->where('id = ?', $data['id'])
			->where('code = ?', $data['code'])
			//->where('activated = ?', 1)
		);
		if(isset($resultData)){
					$where = "id = $id AND code='$code'";
					$this->oDb->update('companies', array(
						'code' => null
					), $where);
		}
		return $resultData;
		//print_r($data);exit();
		/*$id = $data['id'];
		$code = $data['code'];
		$where = "id = $id AND code='$code'";
		$this->oDb->update('users', array(
			'password' => md5($data['password']),
			'code' => null
		), $where);		*/
		//setcookie('AT_AUTH', json_encode($this->get($data['user'])), time() + (3600 * 24 * 30), '/');
	}
	
	public function get($id){
		return $this->oDb->fetchRow($this->oDb->select()
			->from('companies')
			->where('id = ?', $id)
		);
	}
/*******************************************На удаление*************************************************/	
}