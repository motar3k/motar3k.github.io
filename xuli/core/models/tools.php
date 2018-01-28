<?php
/*Vlad Tolmachev 2017*/
  
    Class Tools {
 
        private $registry;
         
        function __construct($registry) {
            $this->registry = $registry;
        }
 
        function summ($value1, $value2){
            $value1 = (array)$value1;
            $value2 = (array)$value2;
            foreach($value1 as $k => $v)array_key_exists($k,$value2) ? $value2[$k] += $v : $value2[$k] = $v;
 
            if(count($value2) < 2)$value2 = $value2[0];
 
            return $value2;
        }
 
        function customLevel($exp, $levels){
            $lvl = 0;
            for($i = 0; $i < count($levels); $i++)if((int)$exp >= (int)$levels[$i])$lvl++;
            return $lvl;
        }
         
        function mySeason(){
            return array('winter', 'spring', 'summer', 'autumn')[floor(date('n') / 3) % 4];
        }   
 
        function winRandom($min, $max){
            $rand1 = rand($min, $max);
            $rand2 = rand($min, $max);
            if($rand1 == $rand2)return true;
                 
            return false;
        }
 
        function error($code){
            echo json_encode(array('status'=>'error', 'text'=>$this->registry['json']->get('errors', true)[$code], 'code'=>$code));   
        }
 
        function myEnergy(){      
            $row = $this->registry['udb']->getData($this->registry['utb'], array('energy', 'etime', 'emax', 'id'), 'id='.$this->registry['uid']);
 
            if((int)$row['energy'] >= (int)$row['emax'])return $row;
 
            $dob = floor(((int)time() - (int)$row['etime'])/200);//считаем, сколько восстановили с последнего визита
            if($dob > (int)$row['emax']-(int)$row['energy'])$dob=(int)$row['emax']-(int)$row['energy'];
            $row['energy'] = (int)$row['energy'] + (int)$dob;//начисляем
             
            if((int)$row['energy'] >= (int)$row['emax']){//если начислили много или достаточно
                $row['energy'] = $row['emax'];//ставим максимальный уровень
                $row['etime'] = time();//ставим текущее время
            }else{
                $timerSec = (($dob+1)*200)-(time() - (int)$row['etime']);//считаем сколько времени нужно для следующей энергии
                if($dob > 0)$row['etime'] = time()-(200-$timerSec);//ставим время, когда восстановилась последняя энергия
            }
             
            $this->registry['udb']->saveData($this->registry['utb'], $row);
 
            return $row;
        }
 
        function myHobo(){    
            $row = $this->registry['udb']->getData($this->registry['utb'], array('hobo', 'hobotime', 'id'), 'id='.$this->registry['uid']);
 
            if((int)$row['hobo'] == 5)return $row;
 
            $dob = floor(((int)time() - (int)$row['hobotime'])/300);//считаем, сколько восстановили с последнего визита
            if($dob > 5-(int)$row['hobo'])$dob=5-(int)$row['hobo'];
            $row['hobo'] += (int)$dob;//начисляем
             
            if((int)$row['hobo'] >= 5){//если начислили много или достаточно
                $row['hobo'] = 5;//ставим максимальный уровень
                $row['hobotime'] = time();//ставим текущее время
            }else{
                $timerSec = (($dob+1)*300)-(time() - (int)$row['hobotime']);//считаем сколько времени нужно для следующей энергии
                if($dob > 0)$row['hobotime'] = time()-(300-$timerSec);//ставим время, когда восстановилась последняя энергия
            }
             
            $this->registry['udb']->saveData($this->registry['utb'], $row);
 
            return $row;
        }
 
        function myAchives(){
            $row = $this->registry['udb']->getData($this->registry['utb'], array('*'), 'id='.$this->registry['uid']);
            $row['achives_level'] = (array)json_decode($row['achives_level'], true);
            $row['weapons'] = (array)json_decode($row['weapons'], true);
            $row['locs'] = (array)json_decode($row['locs'], true);
            $row['naperstki'] = (array)json_decode($row['naperstki'], true);
            $prices = $this->registry['json']->get('achives', true);
 
            for($i = 0; $i < count($prices); $i++){
                $level = 0;
                $type = explode(' ', $prices[$i]['t']);
                if(isset($type[1])){
                    if(!isset($row[$type[0]][$type[1]]))$row[$type[0]][$type[1]] = 0;
                    $level = $this->customLevel(intval($row[$type[0]][$type[1]]), $prices[$i]['l']['v']);
                }else{
                    $level = $this->customLevel(intval($row[$type[0]]), $prices[$i]['l']['v']);
                }
                if(!isset($row['achives_level'][$i]))$row['achives_level'][$i] = 0;
                for($a = 0; $a < (int)$level-intval($row['achives_level'][$i]); $a++){
                    $row[$prices[$i]['p']['t']] = $this->summ($row[$prices[$i]['p']['t']], $prices[$i]['p']['p'][intval($row['achives_level'][$i])+$a]);
                }
                if($row['achives_level'][$i] < (int)$level)$row['achives_level'][$i] = (int)$level;
            }
            $row['achives_level'] = json_encode($row['achives_level']);
            $row['locs'] = json_encode($row['locs']);
            $row['weapons'] = json_encode($row['weapons']);
            $row['naperstki'] = json_encode($row['naperstki']);
 
            $this->registry['udb']->saveData($this->registry['utb'], $row);
             
            return $row;
        }
 
        function learnStatus($user){
            $learn = (int)$user['learn'];
            $quests = (array)json_decode($user['quests'], true);
            $locs = (array)json_decode($user['locs'], true);
            $buy = (array)json_decode($user['buying'], true);
            $wins = (array)json_decode($user['fwins'], true);
 
            for($i = 0; $i < 6; $i++)if(!isset($quests[$i]))$quests[$i] = 0;
            if(!isset($locs[0]))$locs[0] = 0;
            if(!isset($wins[0]))$wins[0] = 0;
 
            if(!$this->isComplete(4, $learn) && $quests[0] >= 1)$learn += 4;
            if(!$this->isComplete(8, $learn) && $quests[0] >= 5)$learn += 8;
            if(!$this->isComplete(16, $learn) && $quests[1] >= 4)$learn += 16;
            if(!$this->isComplete(32, $learn) && $quests[2] >= 7)$learn += 32;
            if(!$this->isComplete(64, $learn) && $quests[3] >= 6)$learn += 64;
            if(!$this->isComplete(128, $learn) && $quests[4] >= 6)$learn += 128;
            if(!$this->isComplete(256, $learn) && $locs[0] >= 1)$learn += 256;
 
            if(!$this->isComplete(512, $learn) && in_array(0, $buy))$learn += 512;
            if(!$this->isComplete(1024, $learn) && in_array(1, $buy))$learn += 1024;
            if(!$this->isComplete(2048, $learn) && (in_array(2, $buy) || in_array(3, $buy)))$learn += 2048;
            if(!$this->isComplete(4096, $learn) && in_array(4, $buy))$learn += 4098;//+2 за конец магаза
 
            if(!$this->isComplete(8192, $learn) && $wins[0] == 1)$learn += 8192;//+2 за конец магаза
 
            return $learn;
        }
 
        function mandatBonus(){
            $my =  $this->registry['udb']->getData($this->registry['utb'], array('*'), 'id='.$this->registry['uid']);
            $my['mandat_up'] = (array)json_decode($my['mandat_up'], true);
            $my['mandat_dmg_bonus'] = [0,0,0,0,0,0];
            $my['emax'] = $this->registry['emax']+intval($my['ebonus']);
 
            if(!isset($this->registry['mandats']))$this->registry['mandats'] = $this->registry['json']->get('mandats', true);
 
            for($i = 0; $i < count($this->registry['mandats']['m']); $i++){
                if(!isset($my['mandat_up'][$i]))$my['mandat_up'][$i] = 0;
                for($a=0; $a < $my['mandat_up'][$i]; $a++)$my[$this->registry['mandats']['m'][$i]['fld']]= $this->summ($my[$this->registry['mandats']['m'][$i]['fld']], $this->registry['mandats']['m'][$i]['pls']);
            }
 
            $my['mandat_up'] = json_encode($my['mandat_up']);
            $my['mandat_dmg_bonus'] = json_encode($my['mandat_dmg_bonus']);
 
            $this->registry['udb']->saveData($this->registry['utb'], $my);
             
            return $my; 
        }
 
        function wearBonus(){
            $row = $this->registry['udb']->getData($this->registry['utb'], array('*'), 'id='.$this->registry['uid']);
            $row['buying'] = json_decode($row['buying'], true);
            $row['wear_dmg_bonus'] = (array)json_decode($row['wear_dmg_bonus'], true);
 
            $season = $this->mySeason();
            if(!isset($this->registry['wears']))$this->registry['wears'] = $this->registry['json']->get('wears', true);
 
            for($i = 0; $i < 3; $i++)$row['wear_dmg_bonus'][$i] = 0;
 
            for($i = 0;$i < count($row['buying']); $i++)if($row['buying'][$i] !== -1)for($n = 0; $n < 3; $n++)$row['wear_dmg_bonus'][$n] += $this->registry['wears']['w'][$row['buying'][$i]][$season][$n];
 
            $row['buying'] = json_encode($row['buying']);
            $row['wear_dmg_bonus'] = json_encode($row['wear_dmg_bonus']);
 
            $this->registry['udb']->saveData($this->registry['utb'], $row);
 
            return $row['wear_dmg_bonus'];
        }
 
        function newLevel($nexp, $exp){
            $levels = $this->registry['json']->get('levels', true);
            $lvl = $this->customLevel($exp, $levels);
            $nlvl = $this->customLevel($nexp, $levels);
                     
            if($lvl < $nlvl){
                $vk = new Vkapi($this->registry);
                $vk->api("secure.setUserLevel", array("user_id"=>$this->registry['uid'], "level"=>$nlvl, 'access_token'=>$this->registry['api_service']));
 
                $this->registry['udb']->saveData($this->registry['utb'], array('id'=>$this->registry['uid'], 'etime'=>0));
            }
        }
 
        function isComplete($code, $learn){
            $res = $learn & $code;
            if($res !== $code)return false;
            return true;
        }
 
        function __autoload($class_name) {
            $filename = strtolower($class_name).'.php';
 
            $file = 'core/models/'.$filename;
 
            if (!file_exists($file)) return false;
 
            include ($file);
        }
    }
?>