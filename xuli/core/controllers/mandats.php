<?php
/*Vlad Tolmachev 2017*/
 
	Class Mandats {

		private $registry;
		
		function __construct($registry) {
			$this->registry = $registry;
		}

		function upMandat(){
	        $user = $this->registry['udb']->getData($this->registry['utb'], array('id', 'mandat_up', 'mandat_use', 'mandat_lvl'), 'id='.$this->registry['uid']);

	        $user['mandat_up'] = (array)json_decode($user['mandat_up'], true);

	        if(intval($user['mandat_lvl'])-intval($user['mandat_use']) == 0)return $this->registry['tools']->error(31);
	        if(!isset($user['mandat_up'][$this->registry['mID']]))$user['mandat_up'][$this->registry['mID']] = 0;
	        if(intval($user['mandat_up'][$this->registry['mID']]) == intval($this->registry['mandats']['m'][$this->registry['mID']]['lvls']))return $this->registry['tools']->error(32);

	        $user['mandat_up'][$this->registry['mID']]++;
	        $user['mandat_use']++;
	        $user['mandat_up'] = json_encode($user['mandat_up']);

	        $this->registry['udb']->saveData($this->registry['utb'], $user);

	        echo json_encode($this->registry['tools']->mandatBonus());
		}

		function sbrosMandat(){
			$user = $this->registry['udb']->getData($this->registry['utb'], array('id', 'mandat_up', 'mandat_use', 'gold'), 'id='.$this->registry['uid']);

			if((int)$user['gold'] < 4)return $this->registry['tools']->error(7);

			$user['gold'] -= 4;

			$user['mandat_use'] = 0;
			$user['mandat_up'] = '[]';

			$this->registry['udb']->saveData($this->registry['utb'], $user);

			echo json_encode($this->registry['tools']->mandatBonus());
		}

	}
?>