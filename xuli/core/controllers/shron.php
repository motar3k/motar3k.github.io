<?php
/*Vlad Tolmachev 2017*/
 
	Class Shron {

		private $registry;
		
		function __construct($registry) {
			$this->registry = $registry;
		}

		function buyWeapon(){
			$result =$this->registry['udb']->getData($this->registry['utb'], array('id', 'weapons', 'gold', 'coins', 'achives_level', 'exp'), 'id='.$this->registry['uid']);
			$result['weapons'] = json_decode($result['weapons'], true);
	        
	        if($this->registry['wID'] < 3 and (int)$this->registry['weapons'][$this->registry['wID']+3]*(int)$this->registry['wNum'] > (int)$result['gold'])return $this->registry['tools']->error(7);
	        if($this->registry['wID'] >= 3 and (int)$this->registry['weapons'][$this->registry['wID']+3]*(int)$this->registry['wNum'] > (int)$result['coins'])return $this->registry['tools']->error(7);
	        
	        if($this->registry['wID'] < 3)$result['gold'] -= (int)$this->registry['weapons'][$this->registry['wID']+3]*(int)$this->registry['wNum'];
	        if($this->registry['wID'] >= 3)$result['coins'] -= (int)$this->registry['weapons'][$this->registry['wID']+3]*(int)$this->registry['wNum'];
	        $result['weapons'][$this->registry['wID']] += (int)$this->registry['wNum'];

	        $result['weapons'] = json_encode($result['weapons']);
	        
	        $this->registry['udb']->saveData($this->registry['utb'], $result);
	        $result = $this->registry['tools']->myAchives();
	        
	        echo json_encode($result);
	    }
	}
?>