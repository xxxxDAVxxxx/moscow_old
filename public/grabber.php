<?php 

	$options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_FOLLOWLOCATION => true
    );

    $url = 'http://babyplus.od.ua/component/virtuemart/?category_id=&page=shop.browse&limit=100&start=0';
    
    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $content = curl_exec( $ch );
    print_r($content); exit();
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );


?>