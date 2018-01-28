<?php
/*Vlad Tolmachev 2017*/
 
	Class Larek {

		private $registry;
		
		function __construct($registry) {
			$this->registry = $registry;
		}

		function buyEnergy(){
			$result = $this->registry['udb']->getData($this->registry['utb'], array('freetime', 'energy', 'gold', 'emax', 'id'), 'id='.$this->registry['uid']);
	        
	        if((int)$this->registry['doping'][$this->registry['eid']][0] > (int)$result['gold'])return $this->registry['tools']->error(7);
	        if((int)$this->registry['eid'] == 6 and time()-(int)$result['freetime'] < 86400)return $this->registry['tools']->error(10);

	        $result['energy'] = $this->registry['tools']->myEnergy()['energy'];

	        if((int)$result['energy'] >= (int)$result['emax'])return $this->registry['tools']->error(11);
	        
	        $result['gold'] -= (int)$this->registry['doping'][$this->registry['eid']][0];
	        $result['energy'] += (int)$this->registry['doping'][(int)$this->registry['eid']][1];
	        if((int)$this->registry['eid'] == 6)$result['freetime'] = time();

	        $this->registry['udb']->saveData($this->registry['utb'], $result);
	        
	        echo json_encode($result);   
	    }
	}
?>