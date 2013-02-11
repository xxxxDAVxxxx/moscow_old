<?php

class Mail_Instance extends Zend_Mail
{
	
	public function __construct($charset='utf-8')
	{
		parent::__construct($charset);
		$site = Zend_Registry::get('config')->site;
		$this->setFrom($site->mail,$site->title);
	}
	
	public function setBodyView($script, $params=array(),$charget="UTF-8")
	{
		$layout=new Zend_Layout(array(
			'layoutPath'=>APPLICATION_PATH.'/layouts'
		));
		$layout->setLayout('layout-mail');
		$view = new Zend_View();
		$view->setScriptPath(APPLICATION_PATH . '/views/scripts/mail');
		
		foreach($params as $key=>$value){
			$view->assign($key,$value);
		}
		
		$layout->content = $view->render($script.'.phtml');
		$html = iconv($charget, "UTF-8",$layout->render());
		$this->setBodyHtml($html);
		return $this;
	}

	public function sendMail($script, $params=array(), $charget="UTF-8") {
		$smtpHost = 'smtp.gmail.com';
		$smtpConf=Zend_Registry::get('config')->smtp_options->toArray();
		$transport = new Zend_Mail_Transport_Smtp($smtpHost, $smtpConf);
		
		$this->addTo($params['email'],iconv($charget, "UTF-8", $params['username']));
		$this->setSubject(iconv($charget, "UTF-8",$params['title']));		
		$this->setBodyView($script, $params['viewParams'], $charget);
		$this->send($transport);
	}	
}