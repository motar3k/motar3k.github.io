<?php
/*Vlad Tolmachev 2017*/
 
	Class Bosses {

		private $registry;
		
		function __construct($registry) {
			$this->registry = $registry;
		}
		function bossStart(){
			$boss = $this->registry['udb']->getData($this->registry['btb'], array('*'), 'id='.$this->registry['uid']);

			if($boss)return $this->registry['tools']->error(15);

			$user = $this->registry['udb']->getData($this->registry['utb'], array('*'), 'id='.$this->registry['uid']);

	        $user['flimits'] = (array)json_decode($user['flimits'],true);
	        $user['fwins'] = (array)json_decode($user['fwins'],true);

	        if(!isset($user['fwins'][0]))$user['fwins'][0] = 0;
	        if(!isset($user['flimits'][$this->registry['bid']]))$user['flimits'][$this->registry['bid']] = 0;
	        
	        if($this->registry['tools']->customLevel((int)$user['exp'], $this->registry['levels']) < 8 && (int)$user['fwins'][0] > 0)return $this->registry['tools']->error(13);
	        if($this->registry['tools']->customLevel((int)$user['exp'], $this->registry['levels']) < 5 && (int)$user['fwins'][0] == 0)return $this->registry['tools']->error(21);
	        if((int)$user['flimits'][$this->registry['bid']] >= 10 && time()-(int)$user['ftimer'] < 86400)return $this->registry['tools']->error(14);

	        $user['energy'] = $this->registry['tools']->myEnergy()['energy'];

	        if((int)$user['energy'] < (int)$this->registry['bosses'][$this->registry['bid']]['energy'])return $this->registry['tools']->error(5);

	        if(time()-(int)$user['ftimer'] >= 86400){
	        	$user['flimits'] = array($this->registry['bid']=>0);
	        	$user['ftimer'] = time();
	        }

	        $user['energy'] -= (int)$this->registry['bosses'][$this->registry['bid']]['energy'];
	        $user['flimits'][$this->registry['bid']]++;
	        
	        $user['flimits'] = json_encode($user['flimits']);

	        if((int)$user['energy'] >= (int)$user['emax'])$user['etime'] = time();
	        
	        $this->registry['udb']->saveData($this->registry['utb'], array('energy'=>$user['energy'], 'flimits'=>$user['flimits'], 'ftimer'=>$user['ftimer'], 'etime'=>$user['etime'], 'id'=>$user['id']));
	        $this->registry['udb']->saveData($this->registry['btb'], array('id'=>$user['id'], 'bid'=>$this->registry['bid'], 'xp'=> $this->registry['bosses'][$this->registry['bid']]['xp'], 'time'=>time(),'damagers'=>'{"'.$user['id'].'":"0"}'));

	        $user['fwins'] = json_encode($user['fwins']);
	        
	        $this->bossInfo($user);
		}
		
		function bossDamage(){
	        $user = array_merge($this->registry['udb']->getData($this->registry['utb'], array('*'), 'id='.$this->registry['uid']), $this->registry['udb']->getData($this->registry['btb'], array('*'), 'id='.$this->registry['uid']));
	        
	        if($this->registry['wid'] < 3){
	        	if((int)$user['timer'] != 9 && time()-(int)$user['ttime'] < (int)$this->registry['json']->get('weapons', true)[(int)$user['timer']])return $this->registry['tools']->error(24);

	        	$user['mandat_dmg_bonus'] = json_decode($user['mandat_dmg_bonus'], true);

	            $user['timer'] = $this->registry['wid'];
	            $user['ttime'] = time();

	            if(!isset($user['mandat_dmg_bonus'][$this->registry['wid']]))$user['mandat_dmg_bonus'][$this->registry['wid']] = 0;
	            $damage = $this->registry['damages'][$this->registry['wid']]+(int)$user['mandat_dmg_bonus'][$this->registry['wid']];

	        }else{
	        	$user['weapons'] = (array)json_decode($user['weapons'], true);
	        	if($user['weapons'][$this->registry['wid']-3] < 1)return $this->registry['tools']->error(16);

	        	$user['mandat_dmg_bonus'] = json_decode($user['mandat_dmg_bonus'], true);
	        	$user['wear_dmg_bonus'] = json_decode($user['wear_dmg_bonus'], true);

	            $user['weapons'][$this->registry['wid']-3]--;

	            $user['weapons'] = json_encode($user['weapons']);

	            if(!isset($user['mandat_dmg_bonus'][$this->registry['wid']]))$user['mandat_dmg_bonus'][$this->registry['wid']] = 0;
	            if(!isset($user['wear_dmg_bonus'][$this->registry['wid']-3]))$user['wear_dmg_bonus'][$this->registry['wid']-3] = 0;
	            $damage = $this->registry['damages'][$this->registry['wid']]+(int)$user['mandat_dmg_bonus'][$this->registry['wid']]+(int)$user['wear_dmg_bonus'][$this->registry['wid']-3];
	        }

	        $user['maxdamage'] += $damage;

	        //считаем мандаты
	        $mdamage = 0;
	        for($i = 0; $i < intval($user['mandat_lvl']); $i++)$mdamage += $this->registry['mandats']['l'][$i];
	        $nwmd = $this->mandatLevel($mdamage + $damage + intval($user['mandat_dmg']), $this->registry['mandats']['l']);

	    	$user['mandat_dmg'] = $nwmd[1];
	    	$user['mandat_lvl'] = $nwmd[0];

	        $this->registry['udb']->saveData($this->registry['utb'], array('id'=>$user['id'], 'mandat_dmg'=>$user['mandat_dmg'], 'mandat_lvl'=>$user['mandat_lvl']));

	        $user['fwins'] = (array)json_decode($user['fwins'], true);
	        if(!isset($user['fwins'][0]))$user['fwins'][0] = 0;

	        $result = $this->registry['udb']->getData($this->registry['btb'], array('*'), 'id IN('.$user['friends'].')', true);

	        for($i = 0; $i < count($result); $i++){
	        	if(($user['fwins'][0] == 0 && (int)$result[$i]['id'] !== $this->registry['uid']) || (int)$result[$i]['status'] !== 0)continue;

	        	$result[$i]['damagers'] = json_decode($result[$i]['damagers'], true);
	            if(!isset($result[$i]['damagers'][$this->registry['uid']]))$result[$i]['damagers'][$this->registry['uid']] = 0;
	            $result[$i]['damagers'][$this->registry['uid']] += $damage;

	            $result[$i]['xp'] -= $damage;

	            if((int)$result[$i]['xp'] <= 0)$result[$i]['status'] = 1;
	            if(time()-(int)$result[$i]['time'] >= 28800)$result[$i]['status'] = 2;

	            $result[$i]['damagers'] = json_encode($result[$i]['damagers']);

	            $this->registry['udb']->saveData($this->registry['btb'], $result[$i]);
	        }

	        $user['fwins'] = json_encode($user['fwins']);

	        $this->registry['udb']->saveData($this->registry['utb'], array('id'=>$this->registry['uid'], 'maxdamage'=>$user['maxdamage'], 'weapons'=>$user['weapons']));
	        $this->registry['udb']->saveData($this->registry['btb'], array('id'=>$this->registry['uid'], 'timer'=>$user['timer'], 'ttime'=>$user['ttime']));

	        $user = array_merge($user, $this->registry['tools']->myAchives());
	        $this->bossInfo($user);
		}

		function bossExit(){
			$user = $this->registry['udb']->getData($this->registry['utb'], array('gold','id'), 'id='.$this->registry['uid']);

			if($user['gold'] < 10)return $this->registry['tools']->error(7);

			$user['gold'] -= 10;

			$this->registry['udb']->saveData($this->registry['utb'], $user);
			$this->registry['udb']->saveData($this->registry['btb'], array('id'=>$this->registry['uid'], 'status'=>2));

			$this->bossInfo();
		}

		function bossInfo($user = false){
			$user?$result = $this->bossStatus($user):$result = $this->bossStatus();

			if($result['status'] != 'fight' && $result['maxdamage'] > 0){
	        	$this->registry['udb']->saveData($this->registry['utb'], array('maxdamage'=>0, 'mandat_dmg'=>0, 'id'=>$this->registry['uid']));
	            $result['maxdamage'] = 0;
	        }
	        if(!$user)$result['energy'] = $this->registry['tools']->myEnergy()['energy'];

	        if(!$this->registry['tools']->isComplete(16384, $result['learn'])){
	        	$result['learn'] = $this->registry['tools']->learnStatus($result);
	        	$this->registry['udb']->saveData($this->registry['utb'], array('id'=>$this->registry['uid'], 'learn'=>$result['learn']));
	        }

	        echo json_encode($result);
		}

		function bossStatus($user = false){
			$boss = $this->registry['udb']->getData($this->registry['btb'], array('*'), 'id='.$this->registry['uid']);
			if(!$user)$user = $this->registry['udb']->getData($this->registry['utb'], array('*'), 'id='.$this->registry['uid']);

			if(!$boss)return $this->bossNeutral($user);

			if(($boss['status'] == 2 || time()-$boss['time'] >= 28800) && $boss['status'] != 1)return $this->bossDefeat(array_merge($user, $boss));
			if($boss['status'] == 1)return $this->bossWin(array_merge($user, $boss));

			return $this->bossFight(array_merge($user, $boss));
		}

		function bossWin($user){
			$this->registry['udb']->deleteData($this->registry['btb'], 'id='.$this->registry['uid']);

			if(!isset($this->registry['bosses']))$this->registry['bosses'] = $this->registry['json']->get('bosses', true);

			$oldexp = $user['exp'];

			$user = $this->randomPrice($user);

			$user['status'] = 'win';
			$user['fwins'] = (array)json_decode($user['fwins'], true);

			if(!isset($user['fwins'][(int)$user['bid']]))$user['fwins'][(int)$user['bid']] = 0;

			$user['fwins'][(int)$user['bid']]++;
			$user['fwins'] = json_encode($user['fwins']);

			$user['topDMG'] += json_decode($user['damagers'], true)[$user['id']];

			$user['damagers'] = $this->getDamagers((array)json_decode($user['damagers'], true));

			$user['exp'] += $this->registry['bosses'][$user['bid']]['exp'];
			$user['coins'] += $this->registry['bosses'][$user['bid']]['coins'];

	        $this->registry['tools']->newLevel($user['exp'], $oldexp);

	        $this->registry['udb']->saveData($this->registry['utb'], array('craft'=>$user['craft'],'buying'=>$user['buying'], 'fwins'=>$user['fwins'], 'exp'=>$user['exp'], 'coins'=>$user['coins'], 'id'=>$user['id'], 'topDMG'=>$user['topDMG'], 'nybuy'=>$user['nybuy']));

	        return $user;
		}

		function bossDefeat($user){
			$this->registry['udb']->deleteData($this->registry['btb'], 'id='.$this->registry['uid']);

			$user['status'] = 'defeat';
			$user['damagers'] = $this->getDamagers((array)json_decode($user['damagers'], true));

			return $user;
		}

		function bossNeutral($user){
			$user['status'] = 'neutral';

			return $user;
		}

		function bossFight($user){
			$user['status'] = 'fight';
			$user['damagers'] = $this->getDamagers((array)json_decode($user['damagers'], true));

			return $user;
		}

		function getDamagers($damagers){
			arsort($damagers);

			if(sizeof($damagers) > 9)$damagers = array_slice($damagers, 0, sizeof($damagers)-(sizeof($damagers)-10), true);

	       	$idsArray = array_keys($damagers);
	        $ids = implode(',', $idsArray);

	        $result = $this->registry['udb']->getData($this->registry['utb'], array('id', 'name'), 'id  IN('.$ids.') ORDER BY FIELD(id, '.$ids.')', true);

	        for($i = 0; $i < count($result); $i++)$result[$i]['damage'] = $damagers[$result[$i]['id']];

	        return $result;
		}

		function randomPrice($user){
			if($this->registry['tools']->winRandom(0, 3)){
                $user['buying'] = (array)json_decode($user['buying'], true);  

                if(!isset($this->registry['bosses']))$this->registry['bosses'] = $this->registry['json']->get('bosses', true);    
                
                $workBoss = $this->registry['bosses'];   

                $workBoss[$user['bid']]['wears'] = array_diff($workBoss[$user['bid']]['wears'], $user['buying']);
                
                if(count($workBoss[$user['bid']]['wears']) > 0){
                    $user['wearID'] = $workBoss[$user['bid']]['wears'][array_rand($workBoss[$user['bid']]['wears'])]; 
                    $user['wearOK'] = 1;
                    array_push($user['buying'], $user['wearID']);    
                } 
                $user['buying'] = json_encode($user['buying']);
            }
            if($this->registry['tools']->winRandom(0, 3)){
                $user['buying'] = (array)json_decode($user['buying'], true);   
                $user['craft'] = (array)json_decode($user['craft'], true);   

                $wears = $this->registry['json']->get('wears', true);

                $wears['craft'] = array_diff($wears['craft'], $user['buying']);

                if(count($wears['craft']) > 0){
                    sort($wears['craft']);      

                    $winning = $wears['craft'][array_rand($wears['craft'])];

                    if(!isset($user['craft'][$winning]))$user['craft'][$winning] = 0;
                    $user['craft'][$winning]++;

                    if(intval($user['craft'][$winning]) == intval($wears['w'][$winning]['craft']))array_push($user['buying'], $winning);

                    $user['cftOK'] = 1;
                    $user['cftID'] = $winning;                    
                }

                $user['buying'] = json_encode($user['buying']);
                $user['craft'] = json_encode($user['craft']);
            }
            if($user['bid'] > 0){
            	$user['toyOK'] = 1;
	            $user['bid'] < 2?$user['toyID'] = rand(2,8):$user['toyID'] = rand(9,14);
	            $user['nybuy'] = json_decode($user['nybuy'], true);

	            if(!isset($user['nybuy'][$user['toyID']]))$user['nybuy'][$user['toyID']] = 0;
	            $user['nybuy'][$user['toyID']]++;

	            $user['nybuy'] = json_encode($user['nybuy']);
            }
            

            return $user;
		}

		function mandatLevel($exp, $levels){
	        $lvl = 0;
	        for($i = 0; $i < count($levels); $i++)if($exp >= (int)$levels[$i]){
	            $lvl++;
	            $exp -= $levels[$i];
	        }
	        return array($lvl, $exp);
	    }

	    
	}
?>