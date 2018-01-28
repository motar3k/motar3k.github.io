<?php
/*Vlad Tolmachev 2017*/
 
	Class Naperstki {

		private $registry;
		
		function __construct($registry) {
			$this->registry = $registry;
		}
		function registerGame(){
	        $user = $this->registry['udb']->getData($this->registry['utb'], array('naperstki', 'coins', 'gold', 'weapons', 'achives_level', 'id', 'exp'), 'id='.$this->registry['uid']);
	        
	        if($user[$this->registry['mode']] < $this->registry['val'])return $this->registry['tools']->error(7);

	        $user['naperstki'] = (array)json_decode($user['naperstki'], true);
	        
	        if(!isset($user['naperstki']['games']))$user['naperstki']['games'] = 0;

	        $user['naperstki']['games']++;
	        $user[$this->registry['mode']] -= $this->registry['val'];

	        $user['naperstki']['mode'] = $this->registry['mode'];
	        $user['naperstki']['val'] = $this->registry['val'];

	        $user['naperstki'] = json_encode($user['naperstki']);
	        
	        $this->registry['udb']->saveData($this->registry['utb'], $user); 
	        $this->registry['tools']->myAchives();
	        
	        echo json_encode($user);    
	    }
	    function endGame(){
	        $user = $this->registry['udb']->getData($this->registry['utb'], array('naperstki', 'coins', 'gold', 'id', 'exp'), 'id='.$this->registry['uid']);

	        $user['naperstki'] = (array)json_decode($user['naperstki'], true);

	        if(!isset($user['naperstki']['endgame']))$user['naperstki']['endgame'] = 0;
	        
	        if($user['naperstki']['endgame'] > 0)return $this->registry['tools']->error(12);

	        $echo = array('win'=>0, 'stakan'=>0, 'type'=>'coins');
	        
	        if(!isset($user['naperstki']['exp']))$user['naperstki']['exp'] = 0;

	        $mylvl = 0;
	        for($i = 1; $i < count($this->registry['minigame']); $i++)if((int)$user['naperstki']['exp'] >= $this->registry['minigame'][$i])$mylvl++;
	        
	        if($this->registry['tools']->winRandom(0, $mylvl) or $user['naperstki']['exp'] < 5 or $user['id'] == 112354918){
	            $echo['win'] = 1;
	            $echo['type'] = $user['naperstki']['mode'];
	            $echo['stakan'] = $this->registry['stakan']; 

	            $user['naperstki']['mode'] == 'coins'?$user['naperstki']['exp'] += $user['naperstki']['val']/25:$user['naperstki']['exp'] += $user['naperstki']['val']/2;

	            $user[$user['naperstki']['mode']] += round($user['naperstki']['val']*(2+$mylvl/100));
	        }else{
	        	$user['naperstki']['mode'] == 'coins'?$user['naperstki']['exp'] += $user['naperstki']['val']/50:$user['naperstki']['exp'] += $user['naperstki']['val']/4;

	            while($echo['stakan'] == $this->registry['stakan'])$echo['stakan'] = rand(0,2);    
	        }
	        
	        $user['naperstki'] = json_encode($user['naperstki']);
	        
	        $this->registry['udb']->saveData($this->registry['utb'], $user); 

	        $user = array_merge($user, $echo);
	        
	        echo json_encode($user);    
	    }
	}
?>