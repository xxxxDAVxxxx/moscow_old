<?php
/**
 * @name Portfolio_Instance
 * 
 * @desc Portfolio manager  
 * 
 * @uses	   Abstract
 * @category   Portfolio
 * @copyright  Copyright (c) at-plus
 * @license    http://at-plus.com.ua/license
 * @author     Rustam Guseynov
 * @filesource /application/models/Portfolio/Instance.php
 * @version    1.0
 */

/** @see Abstract */
require_once APPLICATION_PATH.'/models/Abstract.php';

class Projects_Instance extends AbstractModel {
	
	public function getAutoIncrement(){
		$select=$this->oDb->quoteInto('SHOW TABLE STATUS WHERE name=?;','projects');
		$select=$this->oDb->fetchRow($select);
		return $select['Auto_increment'];
	}
	
	public function readProjects($pro_on_page=null,$page_num = 1) {
		try {
			$select=$this->oDb->select()
					->from('projects');
			if($pro_on_page)	{
				$select=$select->limitPage($page_num, $pro_on_page);
			}
			$select=$select->order(array('id DESC'));
			
			$data['projects'] = $this->oDb->fetchAll($select);
			if ($this->countPages($pro_on_page) > $page_num) {
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
	
	public function get($slug) {	
		$project = $this->oDb->fetchRow($this->oDb->select()
			->from('projects')
			->where("pslug = ?", $slug)
		);
		if($project['id']){
			$project['next']=$this->oDb->fetchRow($this->oDb->select()
				->from('projects',
					array('id','pslug','title'))
				->where("id < ?", $project['id'])
				->order(array('id DESC'))
			);
			$project['prev']=$this->oDb->fetchRow($this->oDb->select()
				->from('projects',
					array('id','pslug','title','pro_num'=>'COUNT(id)+1'))
				->where("id > ?", $project['id'])
				->order(array('id ASC'))
			);
			$project['page_of_pro']=ceil($project['prev']['pro_num']/Zend_Registry::get('config')->projects->perPage);
		}
		
  		return $project;
	}
	
	public function countPages($pro_on_page){
		$count = $pro_on_page;
		$data = $this->oDb->fetchRow($this->oDb->select()
			->from(
				array('p'=>'projects'),
							array('projects_count'=>'COUNT(p.ID)'))
		);
		if($count){
			$count = ceil($data['projects_count'] / $count);
		}
		return $count;		
	}
	

	/**
	 * 
	 * Render Read page
	 * 
	 * @param array|int
	 */
	public function read($id = null, $limit = null) {
		if (!$id) {
			if (!$limit) {
				$data = $this->oDb->fetchAll($this->oDb->select()
					->from('projects')->order(array('id DESC'))
				);
			}
			else {
				$data = $this->oDb->fetchAll($this->oDb->select()
					->from('projects') 
					->order(array('id DESC'))
					->limit($limit)
				);
			}	
		}
		else {  			
			$data = $this->oDb->fetchRow($this->oDb->select()
				->from('projects')
				->where('projects.id = ?', $id)
			);		
		}	
		return $data;
	}	
	
	public function edit($data) {		
		// Validate data
		if ((!isset($data['title'])) || (!isset($data['pslug']))) {
			throw new Exception('Invalid data');
		}	
		
		// create edit array	
		$projectData = array(
			'title' => urldecode($data['title']),
			'pslug' => urldecode($data['pslug']),
			'order_date' => $data['order_date'],
			'status' => urldecode($data['status']),
			'content' => urldecode($data['content']),
			'image' => urldecode($data['image']),
			'meta_k' => urldecode($data['meta_k']),
			'meta_d' => urldecode($data['meta_d']),
			'meta_t' => urldecode($data['meta_t'])
		);		
		// get not utf-8 symbs in valid format
		
		$rusFields = array('title', 'pslug', 'content','status', 'meta_k', 'meta_d', 'meta_t');
		foreach ($projectData as $key => $value) {		
			if (in_array($key, $rusFields)) {
			    $s = mb_detect_encoding($value);
				$projectData[$key] = iconv($s, 'CP1251//TRANSLIT', $value);
			}
		}
		
		$var_arr=json_decode($projectData['image']);//
		$list_of_images=glob(PUBLIC_PATH."/img_uploaded/projects/".$data['p_id']."_*.*");
		$image_list=array();
		foreach ($var_arr as $key => $value){	
			$image_list[]=array("file_name" => $value[0], "title" =>iconv('UTF-8','CP1251',$value[1])); 

			$array_key = array_search(PUBLIC_PATH."/img_uploaded/projects/".$value[0], $list_of_images);
			if ($array_key !== false)
			{		 
    			unset($list_of_images[$array_key]);
			}
		}	
		foreach ($list_of_images as &$value){	
			unlink($value);
		}
		$projectData['image']=serialize($image_list);
		//if ((!$data['p_id']) && ($data['pid'])) {
 		//	$data['p_id'] = $data['pid'];
 		//}

		if ($data['p_id']) {
	    	//update article
	    	$where = $this->oDb->quoteInto('id = ?', $data['p_id']);	
			$this->oDb->update('projects', $projectData, $where);
		}
	    else {
	    	// create article
	    	$this->oDb->insert('projects', $projectData);
	    }	
	}
	
	public function delete($id) {
		$where = $this->oDb->quoteInto('id = ?', $id);//Заключение в кавычки для предотвращения SQL-инъекций	
		$this->oDb->delete('projects', $where); 
		$file_list=glob(PUBLIC_PATH."/img_uploaded/projects/".$id."_*.*");
		foreach ($file_list as &$value){	
			unlink($value);
		}			
	}

	public function createImage($data) {
		
		$projectId = $data['p_id'];		
		if (!$projectId){
			$projectId = $this->getAutoIncrement();
			$image = null;
		}
		else {
			$image = $this->oDb->fetchOne($this->oDb->select()
				->from('projects', array('image'))
				->where('id = ?', $projectId)
			);
		}	
		
		$file_list = array_merge_recursive(glob(PUBLIC_PATH."/img_uploaded/projects/".$projectId."_*.jpg"), glob(PUBLIC_PATH."/img_uploaded/projects/".$projectId."_*.png"), glob(PUBLIC_PATH."/img_uploaded/projects/".$projectId."_*.jpeg"), glob(PUBLIC_PATH."/img_uploaded/projects/".$projectId."_*.gif"));
		$max_img_id=0;
		foreach ($file_list as &$value){ 
			$value=substr($value, strrpos($value, '/')+1);
			$value=substr($value, strrpos($value, '_')+1);
			$value=substr($value,0, strrpos($value, '.'));
			if($max_img_id<$value){$max_img_id=$value;}
		}
		$max_img_id+=1;								
		/*if ($image) {
			if (file_exists(PUBLIC_PATH.'/img_uploaded/projects/'.$image)) {	
				unlink(PUBLIC_PATH.'/img_uploaded/projects/'.$image);
			}
		}*/
		
		move_uploaded_file($_FILES['proimage']['tmp_name'] , PUBLIC_PATH.'/img_uploaded/projects/'.$_FILES['proimage']['name']);
		$ext = strtolower(substr($_FILES['proimage']['name'], strpos($_FILES['proimage']['name'],'.'), strlen($_FILES['proimage']['name'])-1));
		rename(PUBLIC_PATH.'/img_uploaded/projects/'.$_FILES['proimage']['name'], PUBLIC_PATH.'/img_uploaded/projects/'.$projectId."_".$max_img_id.$ext);	
			
		//$where = $this->oDb->quoteInto('id = ?', $projectId);
		//$this->oDb->update('projects', array('image' => $projectId.$ext), $where);

		$callback = array(
			'src' => URL . 'img_uploaded/projects/'.$projectId.'_'.$max_img_id.$ext
			,'pid' => $projectId
			,'name'=> $projectId.'_'.$max_img_id
		);
		
		return  $callback;	//print_r($data);
	}
	public function deleteImage($data) {
		$file_name=PUBLIC_PATH."/img_uploaded/projects/".urldecode($data['file_name']);
		if (file_exists($file_name)){
			unlink($file_name);	
		}	
	}
////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////	
	/**
	 * Delete portfolio
	 * 
	 * @param int $id
	 * @return void
	 */


 	/**
	 * 
	 * Read portfolio from db on slug
	 * 
	 * @param array|int
	 */
	public function readOnSlug($slug) {
		$data = $this->oDb->fetchRow($this->oDb->select()
			->from('portfolio')
			->join('images', 'images.url = portfolio.image', array('imgName' => 'name', 'imgAlt' => 'alt', 'imgTitle' => 'title')) 
			->where('pslug = ?', $slug)
		);	
		$data['images'] = $this->oDb->fetchAll($this->oDb->select()
							->from('imageslinks')
							->join('images', 'images.id = imageslinks.imgId', array('name', 'alt', 'title', 'url')) 
							->where("contentId = ?", $data['id'])
							->where("contentType = ?", "portfolio")
						  );
		$id = $data['id'] - 1;
		$data['prev'] = $this->oDb->fetchRow($this->oDb->select()
			->from('portfolio', array('pslug', 'title'))
			->where('id = ?', $id)
		);	
		$id = $data['id'] + 1;
		$data['post'] = $this->oDb->fetchRow($this->oDb->select()
			->from('portfolio', array('pslug', 'title'))
			->where('id = ?', $id)
		);	
		return $data;		
	}
}