<?php
/**
 * @name Index_Instance
 * 
 * @desc Index page manager  
 * 
 * @uses	   Abstract
 * @category   Index
 * @copyright  Copyright (c) at-plus
 * @license    http://at-plus.com.ua/license
 * @author     Rustam Guseynov
 * @filesource /application/models/Index/Instance.php
 * @version    1.0
 */

/** @see Abstract */
require_once APPLICATION_PATH.'/models/Abstract.php';

class Index_Instance extends AbstractModel {
	
	/**
	 * Read articles for article list
	 * 
	 * @return array|int
	 */
	public function readArticles($col = null, $rubric = null, $page = null) {

	}	

}