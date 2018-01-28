<?php
    include("configs.php");
    $prices = array(1,5,10,100,2);
    
    $link = mysqli_connect($server,$user,$password,$db); 
    header("Content-Type: application/json; encoding=utf-8");  

    $input = $_POST;
    $result = $link->query('SELECT * FROM '.$table.' WHERE `id`='.(int)$input['user_id']);//получаем инфу о юзере
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);//пихаем в массив
    
    $body = file_get_contents('json/donuts.json');
    $arr = json_decode($body);

    $numberItem = 0; 

    // Проверка подписи 
    $sig = $input['sig']; 
    unset($input['sig']); 
    ksort($input); 
    $str = ''; 
    foreach ($input as $k => $v)$str .= $k.'='.$v; 

    if($sig != md5($str.$secret)) { 
        $response['error'] = array('error_code' => 10, 'error_msg' => 'Несовпадение вычисленной и переданной подписи запроса.', 'critical' => true); 
    }else{ 
        // Подпись правильная 
        switch ($input['notification_type']) {
        case 'get_item': 
            // Получение информации о товаре 
            $item = intval(explode('item', $input['item'])[1]);
                  
            if($item < 4){
                $response['response'] = array('item_id' => $item, 'title' => $arr[getLevel((int)$row['donat'])][$item].' рублей', 'photo_url' => 'http://77.222.56.130/xuli/images/rubles.jpg', 'price' => $prices[$item]);
            }else if($item == 4){
                $response['response'] = array('item_id' => $item, 'title' => 'Помощь пацанов', 'photo_url' => 'http://77.222.56.130/xuli/images/gopniki.jpg', 'price' => $prices[$item]);
            }
        break;

        case 'get_item_test': 
            // Получение информации о товаре 
            $item = intval(explode('item', $input['item'])[1]);            
            
            if($item < 4){
                $response['response'] = array('item_id' => $item, 'title' => $arr[getLevel((int)$row['donat'])][$item].' рублей', 'photo_url' => 'http://77.222.56.130/xuli/images/rubles.jpg', 'price' => $prices[$item]);
            }else if($item == 4){
                $response['response'] = array('item_id' => $item, 'title' => 'Помощь пацанов', 'photo_url' => 'http://77.222.56.130/xuli/images/gopniki.jpg', 'price' => $prices[$item]);
            } 
        break; 

        case 'order_status_change': 
            // Изменение статуса заказа 
            if ($input['status'] == 'chargeable') { 
                $order_id = intval($input['order_id']); 
                $uid  = intval($input['user_id']);  
                $item = intval($input['item_id']);

                if($item < 4){
                    $link->query('UPDATE '.$table.' SET `gold`=`gold`+'.(int)$arr[getLevel((int)$row['donat'])][$item].', `donat`=`donat`+'.(int)$prices[$item].' WHERE `id`='.$uid);
                }else if($item == 4){
                    $link->query('UPDATE '.$table.' SET `donat`=`donat`+'.(int)$prices[$item].' WHERE `id`='.$uid);
                    $link->query('UPDATE '.$Btable.' SET `status`= 1 WHERE `id`='.$uid);
                } 
            
                
                $link->query('INSERT IGNORE INTO `trans` SET `uid`='.$uid.', `tid`='.$order_id.', `count`='.(int)$prices[$item]);

                //Вконтакте может несколько раз отправлять уведомления типа Изменения статуса заказа 
                //(с тем же order_id) и ответ должен в точности повторять ответ для исходного уведомления. 
                $response['response'] = array('order_id' => $order_id); 
            }else{ 
                $response['error'] = array('error_code' => 100, 'error_msg' => 'Передано непонятно что вместо chargeable.', 'critical' => true); 
            } 
        break; 

        case 'order_status_change_test': 
            // Изменение статуса заказа 
            if ($input['status'] == 'chargeable') { 
                $order_id = intval($input['order_id']); 
                $uid  = intval($input['user_id']);  
                $item = intval($input['item_id']);                
            
                if($item < 4){
                    $link->query('UPDATE '.$table.' SET `gold`=`gold`+'.(int)$arr[getLevel((int)$row['donat'])][$item].', `donat`=`donat`+'.(int)$prices[$item].' WHERE `id`='.$uid);
                }else if($item == 4){
                    $link->query('UPDATE '.$table.' SET `donat`=`donat`+'.(int)$prices[$item].' WHERE `id`='.$uid);
                    $link->query('UPDATE '.$Btable.' SET `status`= 1 WHERE `id`='.$uid);
                }                 
                $link->query('INSERT IGNORE INTO `trans` SET `uid`='.$uid.', `tid`='.$order_id.', `count`='.(int)$prices[$item]);
        
                //Вконтакте может несколько раз отправлять уведомления типа Изменения статуса заказа 
                //(с тем же order_id) и ответ должен в точности повторять ответ для исходного уведомления. 
                $response['response'] = array('order_id' => $order_id); 
            }else{ 
                $response['error'] = array('error_code' => 100, 'error_msg' => 'Передано непонятно что вместо chargeable.', 'critical' => true); 
            } 
        break; 
        } 
    } 
    
    function getLevel($don){
        if($don < 100){
            return 0;
        }else if($don < 500){
            return 1;
        }else{
            return 2;    
        }
    }

echo json_encode($response); 
?> 