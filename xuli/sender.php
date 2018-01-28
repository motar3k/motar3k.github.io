<?php
	/*Vlad Tolmachev 2017*/
    include("configs.php");
    require('VK/VK.php');
    include('VK/VKException.php');

    set_time_limit(0);

    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    $link = mysqli_connect($server,$user,$password,$db); //Коннектимся к БД
    $vk = new \VK\VK($api_id, $secret);
    $token = $service;

    $counter = 0;
    $users = [];

    $messages = ['Братуха, энергия восполнилась. Заходи, пацаны уже ждут!',
				'Ты куда пропал? Сейчас бой с боссом сольёшь! Давай заходи быро!',
				'Братва уже тебе подгон собрала, заходи, забирай',
				'Зарплата пришла, братан! Заходи в район и забирай!',
				'Братан! Как ты такое провтыкал? Пока ты залипал - тебя обшманали!',
				'Эй, браток! Ты куда это смылся? А ну давай возвращайся, пацаны ждут!'];

	for($i = 0; $i < count($messages); $i++){
		$result = $link->query('SELECT `id` FROM `messages` WHERE `mid`='.$i);

		while($row = mysqli_fetch_array($result, MYSQLI_NUM))array_push($users, $row[0]);

		$users = array_chunk($users, 100);

		for($j = 0; $j < count($users); $j++){
			sendMessage($users[$j], $i);
		}
		$users = [];
	}

    function sendMessage($users, $type){
    	global $link, $counter, $token, $vk, $messages;

    	if($counter == 8){
    		sleep(1);
    		$counter = 0;
    	}

    	$users_str = implode(",", $users);
    	
    	$valid_users = [];

    	$res = $vk->api("users.get", array('user_ids'=>$users_str, 'fields'=>'online'));
   
    	for($i = 0; $i < count($res['response']); $i++){
    		if(!isset($res['response'][$i]['online_mobile']))$res['response'][$i]['online_mobile'] = 0;
    		if((int)$res['response'][$i]['online'] == 1 and (int)$res['response'][$i]['online_mobile'] == 0)array_push($valid_users, $users[$i]);
    	}
        
        $users_str = implode(",", $valid_users);

    	$res = $vk->api("secure.sendNotification", array('user_ids'=>$users_str, 'message'=>$messages[$type], 'access_token'=>$token));

    	if(isset($res['response']) and $res['response'] != '')$link->query('DELETE FROM `messages` WHERE `id` IN('.$res['response'].')');

    	$counter++;
    }

?>