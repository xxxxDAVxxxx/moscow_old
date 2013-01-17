<?php
/**
 * @name Articles_Instance
 * 
 * @desc Articles manager  
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

class Articles_Instance extends AbstractModel {
	
	/**
	 * Read articles for article list
	 * 
	 * @return array|int
	 */
	public function readArticles($col = null, $rubric = null, $page = null) {
	
		try {
			
		
		if ($rubric) {
			$condition = "articles.rubric = $rubric";
		}
		else {
			$condition = "articles.id > 0";
		}
		if ($page) {
			$count = Zend_Registry::get('config')->article->perPage;
			$data = $this->oDb->fetchAll($this->oDb->select()
				->from('articles') 
				->join('rubrics', 'articles.rubric = rubrics.id', array('name'))
				->limitPage($page, $count++)
				->where($condition)
				->order(array('id ASC'))
			);
		} else {
			$data = $this->oDb->fetchAll($this->oDb->select()
				->from('articles') 
				->join('rubrics', 'articles.rubric = rubrics.id', array('rubricName' => 'name')) 
				->limit($col)
				->where($condition)
			);	
			$count = $col;
		}
		if ((count($data)) > $count) {
			$data['next'] = true;
		}
		if ($page > 1) {
			$data['prev'] = true;
		}		
		return $data;
		
		} catch (Exception $e) {
			print_r($e->getMessage()); exit();			
		}
		
	}	
	/**
	 * Read articles of one rubric
	 * 
	 * @return array|int
	 */
	public function readRubricArticles($slug) {
		$rubricId = $this->oDb->fetchRow($this->oDb->select()
			->from('rubrics') 
			->where('slug = ?', $slug)
		);	
		$data = $this->oDb->fetchAll($this->oDb->select()
			->from('articles', array('slug', 'title')) 
			->where('rubric = ?', $rubricId['id'])
		);		
		$data['rubricname'] = $rubricId['name'];
		return $data;
	}	
	/**
	 * Seach articles
	 * 
	 * @return array|int
	 */
	public function seachArticles($word, $page) {
		$word = mb_strtolower($word);
		$dataAll = $this->oDb->fetchAll($this->oDb->select()
			->from('articles', 'id') 
			->where("lower(title) regexp '".$word."' || lower(content) regexp '".$word."'")
			->order(array('id ASC'))
		);	
		$count = Zend_Registry::get('config')->article->perPage;
		$data = $this->oDb->fetchAll($this->oDb->select()
			->from('articles') 
			->limitPage($page, $count++)
			->join('rubrics', 'articles.rubric = rubrics.id', array('name'))
			->where("lower(title) regexp '".$word."' || lower(content) regexp '".$word."'")
			->order(array('id ASC'))
		);	
		$data['countPage'] = ceil(count($dataAll)/$count);
		if ($page < $data['countPage']) {
			$data['next'] = true;
		}
		if ($page > 1) {
			$data['prev'] = true;
		}	
		return $data;
	}
	
	/**
	 * Read articles
	 * 
	 * @return array|int
	 */
	public function read($id = null, $limit = null) {
		if (!$id) {
			if (!$limit) {
				$data = $this->oDb->fetchAll($this->oDb->select()
					->from('articles') 
					->joinLeft('rubrics', 'articles.rubric = rubrics.id', array('rubricName' => 'name')) 
				);
			}
			else {
				$data = $this->oDb->fetchAll($this->oDb->select()
					->from('articles') 
					->joinLeft('rubrics', 'articles.rubric = rubrics.id', array('rubricName' => 'name')) 
					->limit($limit)
					->order(array('id DESC'))
				);
			}	
		}
		else {  			
			$data = $this->oDb->fetchRow($this->oDb->select()
				->from('articles')				
				->joinLeft('rubrics', 'articles.rubric = rubrics.id', array('rubricName' => 'name')) 
				->where('articles.id = ?', $id)
			);		
		}	
		return $data;
	}	
	
	/**
	 * Delete article
	 * 
	 * @param int $id
	 * @return void
	 */
	public function delete($id) {
		$where = $this->oDb->quoteInto('id = ?', $id);	
		$this->oDb->delete('articles', $where); 		
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
   	/**
   	 * Create article
   	 * 
   	 * @param array $data
   	 * @return void
   	 */
	public function edit($data) {		
		// Validate data
		if ((!isset($data['title'])) || (!isset($data['slug'])) || (!isset($data['rubric']))) {
			throw new Exception('Invalid data');
		}	
		// create edit array	
		$articleData = array(
			'title' => urldecode($data['title']),
			'slug' => urldecode($data['slug']),
			'rubric' => $data['rubric'],
			'status' => $data['status'],
			'content' => urldecode($data['content']),
			'meta_k' => urldecode($data['meta_k']),
			'meta_d' => urldecode($data['meta_d']),
			'meta_t' => urldecode($data['meta_t'])
		);		
		// get not utf-8 symbs in valid format
		$rusFields = array('title', 'slug', 'content', 'meta_k', 'meta_d', 'meta_t');
		foreach ($articleData as $key => $value) {		
			if (in_array($key, $rusFields)) {
			    $s = mb_detect_encoding($value);
				$articleData[$key] = iconv($s, 'CP1251//TRANSLIT', $value);
			}
		}
 
		if ($data['a_id']) {
	    	//update article
	    	$where = $this->oDb->quoteInto('id = ?', $data['a_id']);	
			$this->oDb->update('articles', $articleData, $where);
		}
	    else {
	    	// create article
	    	$this->oDb->insert('articles', $articleData);
	    }	
	}
	/**
	 * Get pages
	 * 
	 * @param int $id
	 * @param str $slug
	 * @param int $inner_id
	 * @return string
	 */
	public function get($slug) {	
		$page = $this->oDb->fetchRow($this->oDb->select()
			->from('articles')
			->where("slug = ?", $slug)
		);
  		return $page;
	}
	/**
	 * 
	 * Count pages in article navigstion
	 * 
	 * @param array|int
	 */
	public function countPages($slug = null){
		if ($slug) {
			$rubric = $this->oDb->fetchRow($this->oDb->select()
				->from('rubrics')
				->where('slug = ?', $slug)
			);
			$id = $rubric['id'];
			$conditionRubric = "rubric = $id" ;
		} else {
			$conditionRubric = "id > 0";
		}	
		$count = Zend_Registry::get('config')->article->perPage;
		$data = $this->oDb->fetchAll($this->oDb->select()
			->from('articles', 'id')
			->where($conditionRubric)
		);
		$count = ceil(count($data) / $count);
			
		return $count;		
	}
}