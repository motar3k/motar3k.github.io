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
    $lvls = json_decode(file_get_contents('json/levels.json'));

    $result = $link->query('SELECT * FROM '.$table);

    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
        $cntr = 0;
    	if(time()-(int)$row['visit'] < 2592000 and time()-(int)$row['visit'] > 3600){
            $e = myEnergy((int)$row['id'], $link);
    		if($e[0] > 0 and $e[1]){
                $link->query('INSERT INTO `messages` SET `mid`=0, `time`='.time().', `id`='.(int)$row['id'].' ON DUPLICATE KEY UPDATE `mid`=0, `time`='.time());
                $cntr = 1;
    		}else{
    			$res = $link->query('SELECT * FROM '.$Btable.' WHERE `id`='.(int)$row['id']);
        		if((int)$res->num_rows > 0){
        			$bs = mysqli_fetch_array($res, MYSQLI_ASSOC);
        			if(time()-(int)$bs['time'] < 2600){
                        $link->query('INSERT INTO `messages` SET `mid`=1, `time`='.time().', `id`='.(int)$row['id'].' ON DUPLICATE KEY UPDATE `mid`=1, `time`='.time());
                        $cntr = 1;
                    }
        		}else{
        			if(time()-(int)$row['bonustime'] >= 86400 or time()-(int)$row['freetime'] >= 86400){
                        $link->query('INSERT INTO `messages` SET `mid`=2, `time`='.time().', `id`='.(int)$row['id'].' ON DUPLICATE KEY UPDATE `mid`=2, `time`='.time());
                        $cntr = 1;
        			}else{
        				if(time()-(int)$row['wtime'] >= 28800){
                            $link->query('INSERT INTO `messages` SET `mid`=3, `time`='.time().', `id`='.(int)$row['id'].' ON DUPLICATE KEY UPDATE `mid`=3, `time`='.time());
                            $cntr = 1;
        				}else{
        					if(time()-(int)$row['shmon']<86400){
                                $link->query('INSERT INTO `messages` SET `mid`=4, `time`='.time().', `id`='.(int)$row['id'].' ON DUPLICATE KEY UPDATE `mid`=4, `time`='.time());
                                $cntr = 1;
                            }
        				}
        			}
        		}
    		}
    	}else if(time()-(int)$row['visit'] < 2592000){
            $link->query('INSERT INTO `messages` SET `mid`=5, `time`='.time().', `id`='.(int)$row['id'].' ON DUPLICATE KEY UPDATE `mid`=5, `time`='.time());
            $cntr = 1;
    	}
        if($cntr != 1)$link->query('DELETE FROM `messages` WHERE `id` IN('.(int)$row['id'].')');

        $inv = json_decode($row['invites'], true);

        for($i = 0; $i < count($inv); $i++){
            $exp = (int)mysqli_fetch_array($link->query('SELECT `exp` FROM '.$table.' WHERE `id`='.(int)$inv[$i]), MYSQLI_ASSOC)['exp'];
            if($exp >= (int)$lvls[8]){
                $link->query('INSERT IGNORE INTO `bonuses` SET `id`='.(int)$inv[$i]);
                $l = (int)mysqli_fetch_array($link->query('SELECT `level` FROM `bonuses` WHERE `id`='.(int)$inv[$i]), MYSQLI_ASSOC)['level'];
                if($l < 1){
                    $link->query('UPDATE '.$table.' SET `gold`=`gold`+100 WHERE `id`='.(int)$row['id']);
                    $link->query('UPDATE `bonuses` SET `level`=1 WHERE `id`='.(int)$inv[$i]);
                }
            }
        }
    }

    $result = $link->query('SELECT `id`, `name`, `locsn` FROM '.$table);//получам юзеров с сортировкой по опыту (больше->меньше)

    $ids = array(); $names = array(); $locsn = array(); $photos = array();
        
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
        $locs = json_decode($row['locsn'], true);

        for($i = 0; $i < count($locs); $i++){
            if(!isset($locsn[$i]))$locsn[$i] = 0;
            if((int)$locsn[$i] <= (int)$locs[$i]){
                $locsn[$i] = $locs[$i];
                $ids[$i] = (int)$row['id'];
                $names[$i] = $row['name'];
            }
        }
    }

    for($i = 0; $i < count($locsn); $i++)$photos[$i] = $vk->api("users.get", array("user_ids"=>$ids[$i], "fields"=>"photo_100"))['response'][0]['photo_100'];        

    $li['time'] = time();
    $li['name'] = $names;
    $li['ids'] = $ids;
    $li['locsn'] = $locsn;
    $li['photos'] = $photos;
        
    $f = fopen('json/cache/toploc.json', 'w');
    fputs($f, json_encode($li));
    fclose($f);

    $result = $link->query('SELECT `id`, `exp` FROM '.$table.' ORDER BY `exp` DESC LIMIT 100');//получам юзеров с сортировкой по опыту (больше->меньше)
    
    $data = array(); $i = 0;
    $ids = [];

    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
        $data[$i] = $row;
        $ids[$i] = $row['id'];
        $data[$i]['photo'] = $vk->api("users.get", array("user_ids"=>$row['id'], "fields"=>"photo_50"))['response'][0]['photo_50'];
        $i++;
    }

    $ph = $vk->api("users.get", array("user_ids"=>implode(',', $ids), "fields"=>"photo_50"));

    for($i = 0; $i < count($ids); $i++)$data[$i]['photo'] = $ph['response'][$i]['photo_50'];
        
    $data['time'] = time();
        
    $f = fopen('json/cache/top.json', 'w');
    fputs($f, json_encode($data));
    fclose($f);

    top20();

    function top20(){
        global $link, $robot, $token, $vk, $table;
        $priceDMG = json_decode(file_get_contents('json/top20.json'), true);
        $thisTop = json_decode(file_get_contents('json/cache/top20.json'), true);

        $result = $link->query('SELECT `id`, `topDMG`, `name` FROM '.$table.' ORDER BY `topDMG` DESC LIMIT 20');
    
        $data = array(); $i = 0;
        if(!isset($result->num_rows))return;
        if($result->num_rows < 1)return;

        $ids = [];

        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC) and $row['topDMG']>0){
            $data['top'][$i] = $row;
            $data['top'][$i]['price'] = $priceDMG[$i];
            $ids[$i] = $row['id'];
            $i++;
        }
        $ph = $vk->api("users.get", array("user_ids"=>implode(',', $ids), "fields"=>"photo_50"));
        print_r($ph);

        for($i = 0; $i < count($ids); $i++)$data['top'][$i]['photo'] = $ph['response'][$i]['photo_50'];
        $data['time'] = $thisTop['time'];
            
        if((int)time()-(int)$thisTop['time'] >= 604800){
            echo "kek";
            $link->query('UPDATE '.$table.' SET `topDMG` = 0');
            for($i = 0; $i < count($thisTop['top']); $i++){
                $link->query('UPDATE '.$table.' SET `gold`=`gold`+'.$thisTop['top'][$i]['price'].' WHERE `id`='.$thisTop['top'][$i]['id']);
            }
            $data['time'] = time();
            $f = fopen('json/cache/top20.json', 'w');
            fputs($f, json_encode($data));
            fclose($f);
            top20();
        }

            
        $f = fopen('json/cache/top20.json', 'w');
        fputs($f, json_encode($data));
        fclose($f);
        
    }

    function myEnergy($uid, $link){
        global $table; 

        $arr = array(0,false);
                
        $row = mysqli_fetch_array($link->query('SELECT `energy`, `etime`, `emax`  FROM '.$table.' WHERE `id`='.(int)$uid), MYSQLI_ASSOC);//преобразуем в массив
                
        $dob = floor(((int)time() - (int)$row['etime'])/200);//считаем, сколько восстановили с последнего визита
        if($dob > (int)$row['emax']-(int)$row['energy'])$dob=(int)$row['emax']-(int)$row['energy'];
        $row['energy'] = (int)$row['energy'] + (int)$dob;//начисляем

        if((int)$row['energy'] >= (int)$row['emax'])$arr[1] = true;

        $arr[0] = $dob;

        return $arr;
    }
?>