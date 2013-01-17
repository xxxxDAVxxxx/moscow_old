<?php
/**
 * @name AbstractModel
 * 
 * @desc Basic abstract for all models. 
 * 
 * @copyright  Copyright (c) at-plus
 * @license    http://at-plus.com/license
 * @author     Rustam Guseynov
 * @filesource /application/models/AbstractModel.php
 * @version    1.0
 */
abstract class AbstractModel {
	/**
	 * Database object with full access
	 * 
	 * @var Zend_Db
	 */
	public $oDb;
	
	
	/**
	 * Setup of global variables for this class
	 * 
	 * @return void
	 */
	public function __construct() {
		$this->oDb = Zend_Registry::get('db');
	}
}