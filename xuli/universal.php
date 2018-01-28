<?php
	ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

	$registry = new Registry;
	$router = new Router($registry);

	$router->setPath('core/controllers');
    $registry['router'] = $router;
	$registry['json'] = new Jsonloader;
    $registry['uid'] =  intval($_POST['uid']);

	$registry['functions'][intval($_POST['method'])]();


	function getToken(){
		echo '{"token":"'.md5(uniqid(rand(), true)).'"}';
	}
	function getGT(){
        echo $GLOBALS['registry']['json']->get('top', false, 'cache');
    }
	function getLI(){
        echo $GLOBALS['registry']['json']->get('toploc', false, 'cache');
    }
    function getTop20(){
        echo $GLOBALS['registry']['json']->get('top20', false, 'cache');
    }
    function getLT(){
        echo $GLOBALS['registry']['json']->get('levels');   
    }
    function getLcsI(){
        echo $GLOBALS['registry']['json']->get('quests');   
    }
    function getW(){
        echo $GLOBALS['registry']['json']->get('work'); 
    }
    function getML(){ 
        echo $GLOBALS['registry']['json']->get('minigame');
    }
    function getBS(){
        echo $GLOBALS['registry']['json']->get('bosses');
    }
    function getWeap(){
        echo $GLOBALS['registry']['json']->get('weapons');  
    }
    function getDon(){
        echo $GLOBALS['registry']['json']->get('donuts');   
    }
    function getBon(){
        echo $GLOBALS['registry']['json']->get('bonus');  
    }
    function getWrs(){
        echo $GLOBALS['registry']['json']->get('wears');  
    }
    function getAch(){
        echo $GLOBALS['registry']['json']->get('achives');
    }
    function getMandat(){
        echo $GLOBALS['registry']['json']->get('mandats');
    }
    function getToys(){
        echo $GLOBALS['registry']['json']->get('new_year', false);
    }

    function getLinks(){ 
        $arr = json_decode(file_get_contents('json/links.json'), true);
        $start = json_encode($arr);

        for($i = 0; $i < count($arr); $i++)if(intval($arr[$i]['size']) < 1)$arr[$i]['size'] = getSize($arr[$i]['link']);

        $end = json_encode($arr);

        if($start !== $end){
            $f = fopen('json/links.json', 'w');
            fputs($f, json_encode($arr));
            fclose($f);  
        }                

        print_r(json_encode($arr));                            
    }

    function getFT(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);

        
        $GLOBALS['registry']['friends'] = json_decode(base64_decode(base64_decode($_POST['params'])), true)['users'];

        $GLOBALS['registry']['router']->exec('User', 'setFriends');
    }
    function getUI(){
        
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);

        $GLOBALS['registry']['router']->exec('User', 'getUser');
    }
    function gtP(){
    	$GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
    	$GLOBALS['registry']['fid'] = json_decode(base64_decode(base64_decode($_POST['params'])), true)['id'];
    	
    	$GLOBALS['registry']['router']->exec('User', 'getCustomUser');
    }
    function goQuest(){
    	$params = json_decode(base64_decode(base64_decode($_POST['params'])), true);

    	$GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
    	$GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
    	
    	$GLOBALS['registry']['quests'] = $GLOBALS['registry']['json']->get('quests', true);
    	$GLOBALS['registry']['levels'] = $GLOBALS['registry']['json']->get('levels', true);
    	$GLOBALS['registry']['locID'] = intval($params['loc']);
    	$GLOBALS['registry']['qID'] = intval($params['id']);

    	$GLOBALS['registry']['router']->exec('City', 'startQuest');
    }

    function getBonus(){
    	$GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
    	$GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
    	
    	$GLOBALS['registry']['bonus'] = $GLOBALS['registry']['json']->get('bonus', true);

    	$GLOBALS['registry']['router']->exec('User', 'getBonus');
    }

    function updateWork(){
    	$GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
    	$GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
    	
    	$GLOBALS['registry']['work'] = $GLOBALS['registry']['json']->get('work', true);

    	$GLOBALS['registry']['router']->exec('City', 'workUpdate');
    }
    function takeWork(){
    	$GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
    	$GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
    	
    	$GLOBALS['registry']['work'] = $GLOBALS['registry']['json']->get('work', true);

    	$GLOBALS['registry']['router']->exec('City', 'workTake');
    }

    function buyE(){
    	$GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
    	$GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
    	
    	$GLOBALS['registry']['eid'] =  intval(json_decode(base64_decode(base64_decode($_POST['params'])), true)['id']);
    	$GLOBALS['registry']['doping'] = $GLOBALS['registry']['json']->get('doping', true);
    	
    	$GLOBALS['registry']['router']->exec('Larek', 'buyEnergy');
    }

    function snatWear(){
    	$GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
    	$GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
    	
    	$GLOBALS['registry']['wID'] =  intval(json_decode(base64_decode(base64_decode($_POST['params'])), true)['id']);
    	$GLOBALS['registry']['wears'] = $GLOBALS['registry']['json']->get('wears', true);

    	$GLOBALS['registry']['router']->exec('Shop', 'wearUp');
    }
    function odetWear(){
    	$GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
    	$GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
    	
    	$GLOBALS['registry']['wID'] =  intval(json_decode(base64_decode(base64_decode($_POST['params'])), true)['id']);
    	$GLOBALS['registry']['wears'] = $GLOBALS['registry']['json']->get('wears', true);

    	$GLOBALS['registry']['router']->exec('Shop', 'wearDown');
    }
    function buyWear(){
    	$GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
    	$GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
    	
    	$GLOBALS['registry']['wID'] =  intval(json_decode(base64_decode(base64_decode($_POST['params'])), true)['id']);
    	$GLOBALS['registry']['wears'] = $GLOBALS['registry']['json']->get('wears', true);
    	$GLOBALS['registry']['levels'] = $GLOBALS['registry']['json']->get('levels', true);

    	$GLOBALS['registry']['router']->exec('Shop', 'wearBuy');
    }
    function getSB(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
        
        $GLOBALS['registry']['bosses'] = $GLOBALS['registry']['json']->get('bosses', true);

        $GLOBALS['registry']['router']->exec('Bosses', 'bossInfo');
    }

    function goFight(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
        
        $GLOBALS['registry']['bid'] =  intval(json_decode(base64_decode(base64_decode($_POST['params'])), true)['id']);
        $GLOBALS['registry']['bosses'] = $GLOBALS['registry']['json']->get('bosses', true);
        $GLOBALS['registry']['levels'] = $GLOBALS['registry']['json']->get('levels', true);

        $GLOBALS['registry']['router']->exec('Bosses', 'bossStart');
    }

    function udarBoss(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
        
        $GLOBALS['registry']['wid'] =  intval(json_decode(base64_decode(base64_decode($_POST['params'])), true)['id']);
        $GLOBALS['registry']['mandats'] = $GLOBALS['registry']['json']->get('mandats', true);
        $GLOBALS['registry']['bosses'] = $GLOBALS['registry']['json']->get('bosses', true);

        $GLOBALS['registry']['router']->exec('Bosses', 'bossDamage');
    }

    function exitFight(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);

        $GLOBALS['registry']['router']->exec('Bosses', 'bossExit');
    }

    function buyWeap(){
        $params = json_decode(base64_decode(base64_decode($_POST['params'])), true);

        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
        
        $GLOBALS['registry']['weapons'] = $GLOBALS['registry']['json']->get('weapons', true);
        $GLOBALS['registry']['wID'] = intval($params['id']);
        $GLOBALS['registry']['wNum'] = abs(intval($params['num']));

        $GLOBALS['registry']['router']->exec('Shron', 'buyWeapon');
    }

    function upMandat(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
        
        $GLOBALS['registry']['mID'] =  intval(json_decode(base64_decode(base64_decode($_POST['params'])), true)['id']);
        $GLOBALS['registry']['mandats'] = $GLOBALS['registry']['json']->get('mandats', true);

        $GLOBALS['registry']['router']->exec('Mandats', 'upMandat');
    }

    function nullMandat(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
        
        $GLOBALS['registry']['mandats'] = $GLOBALS['registry']['json']->get('mandats', true);

        $GLOBALS['registry']['router']->exec('Mandats', 'sbrosMandat');
    }

    function getHobo(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
        

        $GLOBALS['registry']['router']->exec('User', 'getHobo');
    }

    function obmenVal(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
        
        $GLOBALS['registry']['val'] =  abs(intval(json_decode(base64_decode(base64_decode($_POST['params'])), true)['id']));

        $GLOBALS['registry']['router']->exec('User', 'obmenValut');
    }

    function startMG(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
        
        $GLOBALS['registry']['val'] =  abs(intval(json_decode(base64_decode(base64_decode($_POST['params'])), true)['val']));
        $GLOBALS['registry']['mode'] =  json_decode(base64_decode(base64_decode($_POST['params'])), true)['mode'];

        $GLOBALS['registry']['router']->exec('Naperstki', 'registerGame');
    }

    function setMG(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
        
        $GLOBALS['registry']['minigame'] = $GLOBALS['registry']['json']->get('minigame', true);
        $GLOBALS['registry']['stakan'] =  abs(intval(json_decode(base64_decode(base64_decode($_POST['params'])), true)['stakan']));

        $GLOBALS['registry']['router']->exec('Naperstki', 'endGame');
    }

    function shmon(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);
        
        $GLOBALS['registry']['id'] =  abs(intval(json_decode(base64_decode(base64_decode($_POST['params'])), true)['id']));

        $GLOBALS['registry']['router']->exec('User', 'shmon');
    }

    function newName(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        
        $GLOBALS['registry']['name'] =  json_decode(base64_decode(base64_decode($_POST['params'])), true)['name'];

        $GLOBALS['registry']['router']->exec('User', 'newName');
    }

    function buySnow(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);

        $GLOBALS['registry']['num'] =  intval(json_decode(base64_decode(base64_decode($_POST['params'])), true)['num']);

        $GLOBALS['registry']['router']->exec('NewYear', 'buySnow');
    }

    function buyToy(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);

        $GLOBALS['registry']['ny'] = $GLOBALS['registry']['json']->get('new_year', true);
        
        $GLOBALS['registry']['tid'] =  abs(intval(json_decode(base64_decode(base64_decode($_POST['params'])), true)['id']));

        $GLOBALS['registry']['router']->exec('NewYear', 'buyToy');
    }

    function sPod(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);

        $GLOBALS['registry']['router']->exec('NewYear', 'showPodarok');
    }

    function gPod(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);

        $GLOBALS['registry']['router']->exec('NewYear', 'getPodarok');
    }

    function getSnow(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);

        $GLOBALS['registry']['router']->exec('NewYear', 'getSnow');
    }

    function setNY(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);

        $GLOBALS['registry']['qid'] =  abs(intval(json_decode(base64_decode(base64_decode($_POST['params'])), true)['id']));
        $GLOBALS['registry']['qstatus'] =  abs(intval(json_decode(base64_decode(base64_decode($_POST['params'])), true)['status']));

        $GLOBALS['registry']['router']->exec('NewYear', 'setQuests');
    }

    function updTree(){
        $GLOBALS['registry']['udb'] = new Database($GLOBALS['registry']);
        $GLOBALS['registry']['tools'] = new Tools($GLOBALS['registry']);

        $GLOBALS['registry']['params'] =  json_decode(base64_decode(base64_decode($_POST['params'])), true);

        $GLOBALS['registry']['router']->exec('NewYear', 'updateTree');
    }

    function __autoload($class_name) {
        $filename = strtolower($class_name).'.php';

        $file = 'core/models/'.$filename;

        if (!file_exists($file)) return false;

        include ($file);
    }

    function getSize($link){
        $ch = curl_init($link);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);

        $data = curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        curl_close($ch);
        return $size;
    }
?>