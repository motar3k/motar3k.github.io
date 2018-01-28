<?php
/*Vlad Tolmachev 2017*/
 
Class Vkapi {
	var $api_secret;
	var $app_id;
	var $api_url = 'https://api.vk.com/method/';
	var $myCurl;
	
	function __construct($registry) {
		$this->app_id = $registry['app_id'];
		$this->api_secret = $registry['api_secret'];
		$this->myCurl = curl_init();
	}
	
	function api($method,$params) {
		$params = array_merge($params, array('api_id'=>$this->app_id, 'v'=>'5.63', 'lang'=>'ru', 'client_secret'=>$this->api_secret));
		ksort($params);
		$sig = '';
		foreach($params as $k=>$v)$sig .= $k.'='.$v;
		$sig .= $this->api_secret;
		$params['sig'] = md5($sig);

		return json_decode($this->request($method, $params), true);
	}

	function request($method, $params){
		return $this->realReq($method, $params);
	}
	function realReq($method, $params){
		curl_setopt_array($this->myCurl, array(CURLOPT_URL => $this->api_url.$method, CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $params));
		
		return curl_exec($this->myCurl);
	}

}
?>