<?php
/**
 * @name Objects_Instance
 * 
 * @desc Objects manager  
 * 
 * @uses	   Abstract
 * @category   Articles
 * @copyright  Copyright (c) at-plus
 * @license    http://at-plus.com.ua/license
 * @author     Rustam Guseynov
 * @filesource /application/models/Articles/Instance.php
 * @version    1.0
 */

/** @see Abstract */
require_once APPLICATION_PATH.'/models/Abstract.php';

class Objects_Instance extends AbstractModel {
	
	public function read($class=null,$orderField='id',$company_id=null,$limit = null,$page=null){
		$select=$this->oDb->select()
					->from('objects');
		if(isset($class)){
			$select = $select->where('class=?',$class);
		}
		if(isset($company_id)){
			$select = $select->where('company_id=?',$company_id);
		}
		if(isset($orderField) && $orderField != ""){
			$select = $select->order($orderField.' DESC');	
		}else{
			$select = $select->order('id DESC');
		}
		if(isset($limit)){
			if(isset($page)){
				$select = $select->limitPage($page, $limit);
			}else{
				$select = $select->limit($limit);
			}
		}
		return $this->oDb->fetchAll($select);
	}	
	
	public function create($data, $userId = null){
		$objectData = array(
			'name' => urldecode($data['name']),
			'desc' => urldecode($data['desc']),
			'location' => urldecode($data['location']),
			'photo' => $data['photo'],
			'video' => $data['video'],
			'square' => $data['square'], 
			'floors' => $data['floors'],
			'class' => $data['class'],
			'year' => $data['year'],
			'arendators' => urldecode($data['arendators']),
			'metro_remote' => $data['remoteness'],
			'company_id' => $userId
		);
		/*
		$rusFields = array('name', 'buyer');
		foreach($objectData as $key => $value) {		
			if (in_array($key, $rusFields)) {
			    $s = mb_detect_encoding($value);
				$objectData[$key] = mb_convert_encoding($value, 'CP1251', $s);
			}
		}
		*/
		$this->oDb->insert('objects', $objectData);
	}
	
	public function update($data){
		$objectData = array(
			'name' => urldecode($data['name']),
			'area' => $data['area'], 
			'floors' => $data['floors'], 
			'class' => $data['class'],
			'year' => $data['year'],
			'buyer' => urldecode($data['buyer']),
			'company_id' => $data['company_id']
		);
		$rusFields = array('name', 'buyer');
		foreach($objectData as $key => $value) {		
			if (in_array($key, $rusFields)) {
			    $s = mb_detect_encoding($value);
				$objectData[$key] = mb_convert_encoding($value, 'CP1251', $s);
			}
		}
		$this->oDb->update('objects', $objectData, $where);
	}
	
	public function get($objectId, $companyId = null){
		$select=$this->oDb->select()
					->from('objects')
					->where('id=?',$objectId);;
		if(isset($companyId)){
			$select = $select->where('company_id=?',$companyId);
		}
		return $this->oDb->fetchRow($select);
	}	
	
}