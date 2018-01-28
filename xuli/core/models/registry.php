<?php

	Class Registry Implements ArrayAccess {

        private $vars = array('damages'=>array(7,13,20,30,50,300,20),
        		      'functions'=>array('getToken', 
        					 'getLinks', 
        					 'getUI', 
        					 'getGT', 
        					 'getFT', 
        					 'getLT', 
        					 'getLcsI', 
        					 'getW', 
        					 'getML', 
        					 'getBS', 
        					 'getWeap', 
        					 'getDon', 
        					 'getBon', 
        					 'getWrs', 
        					 'shmon', 
        					 'gtP', 
        					 'snatWear', 
        					 'odetWear', 
        					 'buyWear', 
        					 'getBonus', 
        					 'udarBoss', 
        					 'buyWeap', 
        					 'goFight', 
        					 'getSB', 
        					 'startMG', 
        					 'setMG', 
        					 'newName', 
        					 'updateWork', 
        					 'takeWork', 
        					 'buyE', 
        					 'goQuest', 
        					 'getLI', 
        					 'getAch', 
        					 'getMandat', 
                             'upMandat',
        					 'nullMandat',
                             'getHobo',
                             'obmenVal',
                             'getTop20',
                             'exitFight',
                             'buyToy',
                             'sPod',
                             'gPod',
                             'getSnow',
                             'setNY',
                             'updTree',
                             'buySnow',
                             'getToys'),
        		      'names'=>array('Чирий', 
        				     'Трэш', 
        				     'Тыр', 
        				     'Клямпер', 
        				     'Мальчик', 
        				     'Бэн', 
        				     'Силя', 
        				     'Батон', 
        				     'Сика', 
        				     'Шапито', 
        				     'Репа', 
        				     'Мозг', 
        				     'Лещ', 
        				     'Молодой', 
        				     'Панк', 
        				     'Пышный', 
        				     'Бух', 
        				     'Губастый', 
        				     'Клюй', 
        				     'Васёк', 
        				     'Нос', 
        				     'Фрол', 
        				     'Базай', 
        				     'Воробей', 
        				     'Питон', 
        				     'Люссак', 
        				     'Пидаль', 
        				     'Блин', 
        				     'Федот', 
        				     'Боб', 
        				     'Зевс', 
        				     'Вавилон', 
        				     'Гондурас', 
        				     'Кабан', 
        				     'Гас', 
        				     'Байс', 
        				     'Треск', 
        				     'Песняр', 
        				     'Мультик', 
        				     'Сметана', 
        				     'Робин', 
        				     'Пыр', 
        				     'Божинда', 
        				     'Пузо', 
        				     'Руль', 
        				     'Баур', 
        				     'Гоблин', 
        				     'Жид', 
        				     'Зверь', 
        				     'Тролль', 
        				     'Стакан', 
        				     'Леший', 
        				     'Пирда', 
        				     'Питон', 
        				     'Чужой', 
        				     'Бидон', 
        				     'Чинарик'),
        		      'api_service'=>'c3457611c3457611c345761116c312742dcc345c34576119a8351b6af776a89d42b9051',
        		      'api_secret'=>'GltnXaT67MYKdwXEFVpC',
        		      'api_id'=>5702204,
        		      'server'=>'localhost',
        		      'user'=>'xuli_user',
        		      'pass'=>'tolmasoft1',
        		      'db'=>'xuli',
                              'utb'=>'users',
        		      'emax'=>50,
        		      'btb'=>'fights');

        function set($key, $var) {
	        if (isset($this->vars[$key]) == true) return false;

	        $this->vars[$key] = $var;

	        return true;
		}

		function get($key) {
		   	if (isset($this->vars[$key]) == false) return null;

		    return $this->vars[$key];
		}

	function remove($var) {
		unset($this->vars[$key]);
	}

		//Standart functions about interface ArrayAcces

	function offsetExists($offset) {
        	return isset($this->vars[$offset]);
	}

	function offsetGet($offset) {
		    return $this->get($offset);
	}

	function offsetSet($offset, $value) {
		    $this->set($offset, $value);
	}

	function offsetUnset($offset) {
		    unset($this->vars[$offset]);
	}

	}

?>