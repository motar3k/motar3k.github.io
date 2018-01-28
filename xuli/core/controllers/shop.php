<?php
/*Vlad Tolmachev 2017*/
 
	Class Shop {

		private $registry;
		
		function __construct($registry) {
			$this->registry = $registry;
		}
		
		function wearUp(){
	        $response = $this->registry['udb']->getData($this->registry['utb'], array('mywears', 'buying', 'gold', 'exp', 'coins', 'id', 'learn'), 'id='.$this->registry['uid']);

	        $response['mywears'] = json_decode($response['mywears'], true); 
	        
	        $response['mywears'][(int)$this->registry['wears']['w'][$this->registry['wID']]['type']] = -1; 
	        
	        $response['mywears'] = json_encode($response['mywears']);
	        
	        $this->registry['udb']->saveData($this->registry['utb'], $response);

	        $response['wear_dmg_bonus'] = $this->registry['tools']->wearBonus();
	        
	        echo json_encode($response);  
	    }
	    
	    function wearDown(){
	        $response = $this->registry['udb']->getData($this->registry['utb'], array('mywears', 'buying', 'gold', 'exp', 'coins', 'id', 'learn'), 'id='.$this->registry['uid']);
	        
	        $response['mywears'] = (array)json_decode($response['mywears'], true);
	        $response['buying'] = (array)json_decode($response['buying'], true);
	        
	        if($response['mywears'][(int)$this->registry['wears']['w'][$this->registry['wID']]['type']] == $this->registry['wID'])return $this->registry['tools']->error(19);
	        if(!in_array($this->registry['wID'], $response['buying']))return $this->registry['tools']->error(20);
	        
	        $response['mywears'][(int)$this->registry['wears']['w'][$this->registry['wID']]['type']] = $this->registry['wID'];

	        $response['mywears'] = json_encode($response['mywears']);
	        $response['buying'] = json_encode($response['buying']);
	        
	        $this->registry['udb']->saveData($this->registry['utb'], $response);

	        $response['wear_dmg_bonus'] = $this->registry['tools']->wearBonus();
	        
	        echo json_encode($response);   
	    }
	    
	    function wearBuy(){
	        $response = $this->registry['udb']->getData($this->registry['utb'], array('fwins', 'locs', 'quests', 'mywears', 'buying', 'gold', 'exp', 'coins', 'id', 'learn'), 'id='.$this->registry['uid']);
	        
	        $oldexp = $response['exp'];
	        $response['mywears'] = (array)json_decode($response['mywears'], true);
	        $response['buying'] = (array)json_decode($response['buying'], true);
	        
	        if((int)$this->registry['wears']['w'][$this->registry['wID']]['price'] > (int)$response[$this->registry['wears']['w'][$this->registry['wID']]['valuta']])return $this->registry['tools']->error(7);
	        if((int)$this->registry['wears']['w'][$this->registry['wID']]['level'] > $this->registry['tools']->customLevel((int)$response['exp'], $this->registry['levels']))return $this->registry['tools']->error(18);
	            
	        $response['exp'] += (int)$this->registry['wears']['w'][$this->registry['wID']]['exp'];
	        $response[$this->registry['wears']['w'][$this->registry['wID']]['valuta']] -= (int)$this->registry['wears']['w'][$this->registry['wID']]['price'];

	        $response['mywears'][(int)$this->registry['wears']['w'][$this->registry['wID']]['type']] = $this->registry['wID'];

	        array_push($response['buying'], $this->registry['wID']);

	        $response['mywears'] = json_encode($response['mywears']);
	        $response['buying'] = json_encode($response['buying']);
	        
	        if(!$this->registry['tools']->isComplete(4096, $response['learn']))$response['learn'] = $this->registry['tools']->learnStatus($response);
	        
	        $this->registry['udb']->saveData($this->registry['utb'], $response);
	        $this->registry['tools']->newLevel($response['exp'], $oldexp);

	        if($this->registry['wID'] == 4)$response['learn'] -= 2;

	        $response['wear_dmg_bonus'] = $this->registry['tools']->wearBonus();

	        echo json_encode($response);   
	    }
	}
?>