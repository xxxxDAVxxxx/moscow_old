<?php
/**
 * @name       Bootstrap
 * 
 * @desc       Application setup
 * 
 * @uses	   Zend_Application_Bootstrap_Bootstrap
 * @filesource /application/Bootstrap.php
 * @version    1.0
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
     * Stores a copy of the config object in the Registry for future references
     * !IMPORTANT: Must be runed before any other inits
     *
     * @return void
     */
	protected function _initConfig() {
    	Zend_Registry::set('config', new Zend_Config($this->getOptions())); 	
    	date_default_timezone_set('Europe/Oslo');
    }

	/**
     * Stores a copy database adapter in the Registry for future references
     *
     * @return void
     */
    protected function _initDatabase() {    	
		$this->bootstrap('multidb');
		$resource = $this->getPluginResource('multidb');
    	$databases = Zend_Registry::get('config')->resources->multidb;
	    foreach($databases as $name => $adapter) {
	    	$db_adapter = $resource->getDb($name);
	    	Zend_Registry::set($name, $db_adapter);
	    }		    
    }
    
	/**
     * Inits routes
     *
     * @return void
     */
    protected function _initRoutes() {
    	$this->bootstrap('frontController');
        //getting frontController object instance 
        $oFront = $this->getResource('frontController');    
        //setting config file with routes
        $oRouter = new Zend_Controller_Router_Rewrite();  
        $oRouteConfig = new Zend_Config_Ini(
        	APPLICATION_PATH . '/configs/routes.ini', 'routes'
        );
        $oRouter->addConfig($oRouteConfig, 'routes');
        $oFront->setRouter($oRouter);
    } 
    
}