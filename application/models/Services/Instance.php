<?php
/**
 * @name Services_Instance
 * 
 * @desc Services manager  
 * 
 * @uses	   Abstract
 * @category   Services
 * @copyright  Copyright (c) at-plus
 * @license    http://at-plus.com.ua/license
 * @author     Rustam Guseynov
 * @filesource /application/models/Services/Instance.php
 * @version    1.0
 */

/** @see Abstract */
require_once APPLICATION_PATH.'/models/Abstract.php';

class Services_Instance extends AbstractModel {
	
	/**
	 * Read cervices
	 * 
	 * @return array|int
	 */
	public function readServices() {
		$data = $this->oDb->fetchAll($this->oDb->select()
			->from('articles') 
			->where('rubric = 4')
		);			
		return $data;
	}	
	/**
	 * Read articles
	 * 
	 * @return array|int
	 */
	public function read($slug) {
		$data = $this->oDb->fetchRow($this->oDb->select()
			->from('articles') 
			->where('slug = ?', $slug)
		);
		return $data;
	}	
	/**
	 * Get rubrics
	 * 
	 * @return array
	 */
	public function readRubrics() {
		$data = $this->oDb->fetchAll($this->oDb->select()
			->from('rubrics') 
		);		
		return $data;
	}
}