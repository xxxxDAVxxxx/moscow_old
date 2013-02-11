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
//array('a'=>'articles'),array('*','content_preview'=>'LEFT(a.content,200)')
	//Читаем из таблицы записи всех полей + превью текста на странице $page_num
	//с количеством $arts_on_page статей на странице.
	public function readArticles($arts_on_page=null,$page_num = 1,$type=null) {
		try {
			$select=$this->oDb->select()
					->from('articles');
			
			if($type){
					$select=$select->where('type = ?', $type);
					//switch ($type) {
					//	case 'news':$select=$select->order(array('id DESC'));break;
					//	case 'services':$select=$select->order(array('id ASC'));break;
					//	default:
					//	    $select=$select->order(array('id DESC'));
					//}
			}
			if($arts_on_page){
				$select=$select->limitPage($page_num, $arts_on_page);
			}
			switch ($type) {
				case 'news': $select=$select->order(array('id DESC'));break;
				case 'services': $select=$select->order(array('id ASC'));break;
				default:
				$select=$select->order(array('id DESC'));
			}
			$data['articles'] = $this->oDb->fetchAll($select);
			if ($this->countPages($arts_on_page,$type) > $page_num) {
				$data['next'] = true;
			}
			if ($page_num > 1) {
				$data['prev'] = true;
			}	
			
			return $data;		
		} catch (Exception $e) {
			print_r($e->getMessage()); exit();			
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
	//Возвращаем количество страниц при при количестве статей на страницу $arts_on_page. 
	public function countPages($arts_on_page,$type='news'){
		
		$count = $arts_on_page;
		$select=$this->oDb->select()
			->from(
					array('a'=>'articles'),
					array('articles_count'=>'COUNT(a.ID)'));
		if($type){
			$select=$select->where('type = ?',$type);
		}
		$data = $this->oDb->fetchRow($select);
		
		if($count){
			$count = ceil($data['articles_count'] / $count);
		}
		return $count;		
	}
	
	public function edit($data) {		
		// Validate data
		if ((!isset($data['title'])) || (!isset($data['slug']))) {
			throw new Exception('Invalid data');
		}	
		// create edit array	
		$articleData = array(
			'title' => urldecode($data['title']),
			'slug' => urldecode($data['slug']),
			'type' => urldecode($data['status']),
			'content' => urldecode($data['content']),
			'image' => urldecode($data['image']),
			'meta_k' => urldecode($data['meta_k']),
			'meta_d' => urldecode($data['meta_d']),
			'meta_t' => urldecode($data['meta_t'])
		);		
		// get not utf-8 symbs in valid format
		$rusFields = array('title', 'slug', 'content','image', 'meta_k', 'meta_d', 'meta_t');
		foreach ($articleData as $key => $value) {		
			if (in_array($key, $rusFields)) {
			    $s = mb_detect_encoding($value);
				$articleData[$key] = iconv($s, 'CP1251//TRANSLIT', $value);
			}
		}
		//return $data['a_id']." ".$data['aid'];
 		//if ((!$data['a_id']) && ($data['aid'])) {
 		//	$data['a_id'] = $data['aid'];
 		//}
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
	
/////////////////////////////////////////////////////////////////////////////////////////////////////	
/////////////////////////////////////////////////////////////////////////////////////////////////////	
	
	
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
				);
			}
			else {
				$data = $this->oDb->fetchAll($this->oDb->select()
					->from('articles') 
					->limit($limit)
					->order(array('id DESC'))
				);
			}	
		}
		else {  			
			$data = $this->oDb->fetchRow($this->oDb->select()
				->from('articles')
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
		$where = $this->oDb->quoteInto('id = ?', $id);//Заключение в кавычки для предотвращения SQL-инъекций	
		$this->oDb->delete('articles', $where); 
		$file_list=glob(PUBLIC_PATH."/img_uploaded/articles/".$id.".*");
		foreach ($file_list as &$value){	
			unlink($value);
		}		
	}
	
	/**
	 * Create article image
	 * 
	 * @param array $data
	 * @return array
	 */
	public function getAutoIncrement(){
			$select="SHOW TABLE STATUS WHERE name=?;";
			$select=$this->oDb->fetchRow($select,'articles');
		return $select['Auto_increment'];
	}
	
	public function createImage($data) {
		
		$articleId = $data['a_id'];		
		if (!$articleId) {
			//$this->oDb->insert('articles', array());//???
			$articleId = $this->getAutoIncrement();
			$image = null;
		}
		else {
			$image = $this->oDb->fetchOne($this->oDb->select()
				->from('articles', array('image'))
				->where('id = ?', $articleId)
			);
		}	
		if ($image) {
			if (file_exists(PUBLIC_PATH.'/img_uploaded/articles/'.$image)) {	
				unlink(PUBLIC_PATH.'/img_uploaded/articles/'.$image);
			}
		}	
		
		move_uploaded_file($_FILES['artimage']['tmp_name'] , PUBLIC_PATH.'/img_uploaded/articles/'.$_FILES['artimage']['name']);
		$ext = strtolower(substr($_FILES['artimage']['name'], strpos($_FILES['artimage']['name'],'.'), strlen($_FILES['artimage']['name'])-1));
		rename(PUBLIC_PATH.'/img_uploaded/articles/'.$_FILES['artimage']['name'], PUBLIC_PATH.'/img_uploaded/articles/'.$articleId.$ext);	
			
		//$where = $this->oDb->quoteInto('id = ?', $articleId);
		//$this->oDb->update('articles', array('image' => $articleId.$ext), $where);

		$callback = array(
			'src' => URL . 'img_uploaded/articles/'.$articleId.$ext
			,'aid' => $articleId
		);
		
		return $callback;	
	}
	
}