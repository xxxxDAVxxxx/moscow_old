<?php
/**
 * @name Mail_Instance
 * 
 * @desc Mail manager  
 * 
 * @category   Mail
 * @copyright  Copyright (c) at-plus
 * @license    http://at-plus.com.ua/license
 * @author     Rustam Guseynov
 * @filesource /application/models/Mail/Instance.php
 * @version    1.0
 */

class Mail_Instance {
	/**
	 * Data from config
	 * 
	 * @var object
	 */
	public $regData;
	
	/**
	 * Setup of global variables for this class
	 * 
	 * @param object $regData
	 * @return void
	 */
	public function __construct($regData) {
		$this->regData = $regData;			
	}	
    
	
	/**
	 * Function sends confirmation letter to new user
	 * 
	 * @param array $data 
	 * @return void
	 */
	public function sendMail($data) {
		// Basic variables
		
			
	   	// Headers
		/*$headers= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";	
		$headers .= "From: " . $data['email'];	*/
		
		/*$headers = "Reply-To: Some One <".$to.">\r\n"; 
	    $headers .= "Return-Path: Some One <".$to.">\r\n"; 
	    $headers .= "From: Some One <".$data['email'].">\r\n"; 
	    $headers .= "Organization: My Organization\r\n"; 
	    $headers .= "Content-Type: text/html\r\n"; 
	    /*$header .= "From: Some One <".$data['email'].">\r\n"; 
	    $header .= "Organization: My Organization\r\n"; */
	    //$header .= "Content-Type: text/html\r\n"; 	

		// Send mail 
		//mail($to, iconv("CP1251", "UTF-8", "Письмо на сайт atcompany.com.ua"), $data['mess'].iconv("CP1251", "UTF-8", "<br><b>Имя: </b>").$data['name'].iconv("CP1251", "UTF-8", "<br><b>Email: </b>").$data['email'].iconv("CP1251", "UTF-8", "<br><b>Телефон: </b>").$data['phone'], $headers);
    	/*$options = array(
	        'auth'     => 'login',
	        'username' => 'xxxxdavxxxx@gmail.com',
	        'password' => 'Dbiytdsqcfl',
	        'ssl'      => 'tls',
	        'port' => 587
   		 );
   		$mailTransport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $options);
    	Zend_Mail::setDefaultTransport($mailTransport);
		//$tr = new Zend_Mail_Transport_Smtp($oConfig->mail->host,$oConfig->mail->toArray());
		//Zend_Mail::setDefaultTransport($tr);
		$mail = new Zend_Mail();
		$mail->setFrom('xxxxdavxxxx@gmail.com','Some Sender');
		$mail->addTo('dramaretskiysasha@gmail.com');
		$mail->setBodyText('This is the text of the mail.');
		$mail->setSubject('Using Gmail SMTP');
		$mail->send();*/
		/////////////
		$to = $this->regData['mailLogin'];	
		$smtpHost = 'smtp.gmail.com';
		/*$smtpConf = array(
		  'auth' => 'login',
		  'ssl' => 'ssl',
		  'port' => '465',
		  'username' => 'atcompanymail@gmail.com',
		  'password' => 'Rjvgfybz FN'
		); */
		$smtpConf=Zend_Registry::get('config')->smtp_options->toArray();
		$transport = new Zend_Mail_Transport_Smtp($smtpHost, $smtpConf);

		$mail = new Zend_Mail('utf-8');
		$mail->setFrom($data['email'],iconv("CP1251", "UTF-8", "Компания АТ"));
		$mail->addTo($to,'atcompany.com.ua');//
		$mail->setSubject(iconv("CP1251", "UTF-8", "Письмо на сайт atcompany.com.ua"));
		$mail->setBodyHtml($data['mess'].iconv("CP1251", "UTF-8", "<br><b>Имя: </b>").$data['name'].iconv("CP1251", "UTF-8", "<br><b>Email: </b>").$data['email'].iconv("CP1251", "UTF-8", "<br><b>Телефон: </b>").$data['phone']);
		$mail->send($transport);
	}
   	
}