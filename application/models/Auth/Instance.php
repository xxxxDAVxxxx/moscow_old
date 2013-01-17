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
		$rusFields = array('name', 'password', 'email','desc', 'person', 'site', 'phone','skype','icq');
		foreach ($CompanyData as $key => $value) {		
			if (in_array($key, $rusFields)) {
			    $s = mb_detect_encoding($value);
				$CompanyData[$key] = iconv($s, 'CP1251//TRANSLIT', $value);
			}
		}
		$CompanyData['password']=md5($CompanyData['password']);
		$this->oDb->insert('companies', $CompanyData);
	}
	
	public function login($data) {
		$LoginData = array(
				'name' => urldecode($data['name']),
				'password' => urldecode($data['password'])
		);
		$rusFields = array('name', 'password');
		foreach ($LoginData as $key => $value) {
			if (in_array($key, $rusFields)) {
				$s = mb_detect_encoding($value);
				$LoginData[$key] = iconv($s, 'CP1251//TRANSLIT', $value);
			}
		}
		$userId = $this->oDb->fetchOne($this->oDb->select()
				->from('companies', array('id'))
				->where("name = ?", $LoginData['name'])
				->where("password = ?", md5($LoginData['password']))
		);
		return $userId ? $userId : false;
	}
	
	
	
	
/*******************************************На удаление*************************************************/	
	
	
	/**
	 * Check user data
	 * 
	 * @param array $data
	 * @return int|boolean
	 */
	public function check($data) {			
		$userId = $this->oDb->fetchOne($this->oDb->select()
			->from('users', array('id'))
			->where("email = ?", $data['login'])
			->where("pass = ?", md5($data['pass']))
		);	 	
  		return $userId ? $userId : false;
	}
	
	/**
	 * Get user data
	 * 
	 * @param int $id
	 * @return array
	 */
	public function get($id) {			
		$userData = $this->oDb->fetchRow($this->oDb->select()
			->from('users', array('id', 'email', 'acl', 'timestamp', 'username'))
			->where("id = ?", $id)
		);	 	
  		return $userData;
	}
	
	/**
	 * Change user data in db
	 * 
	 * @param int $id
	 * @return array
	 */
	public function settodb($userData) {	
		if (isset($userData['profile-pass'])) {
			$userdbData = $this->oDb->fetchRow($this->oDb->select()
				->from('users', array('pass'))
				->where("id = 2")
			);	
			if ($userData['profile-pass'] == $userData['profile-rep-pass']) {
				$userupdate =array (
					'email' => $userData['profile-login'],
					'username' => $userData['profile-name'],
					'pass' => MD5($userData['profile-pass'])
				);
				$userData = $this->oDb->update('users', $userupdate, "id = 2");/*where("id = ?", $id)*/
				echo "В базу занесен новый пароль!<br />";
			} elseif (($userData['profile-rep-pass'] == "") && (md5($userData['profile-pass']) == $userdbData['pass'])) {
				$userupdate =array (
					'email' => $userData['profile-login'],
					'username' => $userData['profile-name']
				);
				$userData = $this->oDb->update('users', $userupdate, "id = 2");
				echo "В базу занесены изменения полей!<br />";
			} elseif (($userData['profile-rep-pass'] == "") && (md5($userData['profile-pass']) != $userdbData['pass'])) {
				echo "Введен неверный пароль!";
			} else {
				echo "Пароли не совпадают!";
			}
		}	
	}
	/**
	 * Add article to db
	 * 
	 * @param int $id
	 * @return array
	 */
	public function arttodb ($addArticle) {
		if ($addArticle['h1'] != "" && $addArticle['name'] != "" && $addArticle['rubric'] != ""){
			$artupdate = array (
				'h1' => $addArticle['h1'],
				'public' => $addArticle['public'],
				'name' => $addArticle['name'],
				'rubric' => $addArticle['rubric'],
				'content' => $addArticle['content'],
				'keywords' => $addArticle['keywords']
			);
		$addArticle = $this->oDb->insert('articles', $artupdate);
		}
		else {
			echo "Не все поля введены!";
		}
	}
	/**
	 * Get rubric from db
	 * 
	 * @param int $id
	 * @return array
	 */
	public function getrubric () {
		$rubrics = $this->oDb->fetchAll($this->oDb->select()
			->from('rubric') 
		);
		$options = "";
		for ($i=0; $i<count($rubrics); $i++) {
			$options .= "<option value=\"".$rubrics[$i]['id']."\">".$rubrics[$i]['name']."</option>";
		}
		return $options;
	}
	/**
	 * Get articles from db
	 * 
	 * @param int $id
	 * @return array
	*/ 
	public function getarticles() {
		$articles = $this->oDb->fetchAll($this->oDb->select()
			->from('articles') 
			-> joinLeft('rubrics', 'articles.rubric = rubrics.id', array('rubricName' => 'name')) 
		);
		return $articles;
	}
	
}