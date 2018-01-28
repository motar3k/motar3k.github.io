<?php
/*Vlad Tolmachev 2017*/
 
	Class NewYear {

		private $registry;
		
		function __construct($registry) {
			$this->registry = $registry;
		}

		function showPodarok(){
	        $my = $this->registry['udb']->getData($this->registry['utb'], array('id', 'nybuy', 'toyspos'), 'id='.$this->registry['uid']);

	        $lvls = array(-1,0,7,11,15,19,27);
	        $pod = array(array(0,0,0,0,0), array(100,30,1,1,1), array(300,70,5,5,5), array(400,100,5,5,5), array(400,150,5,5,5), array(800,200,1,1,20), array(2000,600,25,25,25));
	        $myt = json_decode($my['toyspos'], true);        

	        $res['gW'] = 0;$res['gE'] = 0;$exp = 0;

	        $arr0 = json_decode($my['nybuy'],true);
	        $arr = array_slice($arr0, 2, count($arr0)-2);

	        if(!in_array(0, $arr))$res['gE']=1;
	        if((int)$arr0[1] > 0)$res['gW']=1;
	        if((int)$arr0[0] > 0)$res['gW']=1;

	        for($i=0;$i<count($myt);$i++){
	            if($myt[$i][2] < 9)$exp+=1;
	            if($myt[$i][2] > 8 and $myt[$i][2] < 11)$exp+=2;
	            if($myt[$i][2] > 10 and $myt[$i][2] < 15)$exp+=4;
	        }
	        $lvl = $this->registry['tools']->customLevel($lvls, $exp);

	        $res['coins'] = $pod[$lvl][0];
	        $res['gold'] = $pod[$lvl][1];
	        $res['w1'] = $pod[$lvl][2];
	        $res['w2'] = $pod[$lvl][3];
	        $res['w3'] = $pod[$lvl][4];

	        echo json_encode($res); 
	    }

	    function getPodarok(){
	        $my = $this->registry['udb']->getData($this->registry['utb'], array('*'), 'id='.$this->registry['uid']);

	        if((int)$my['nybol'] == 1)return $this->registry['tools']->error(30);

	        $lvls = array(-1,0,7,11,15,19,27);
	        $pod = array(array(0,0,0,0,0), array(100,30,1,1,1), array(300,70,5,5,5), array(400,100,5,5,5), array(400,150,5,5,5), array(800,200,1,1,20), array(2000,600,25,25,25));
	        $myt = json_decode($my['toyspos'], true);        
	        $wps= json_decode($my['weapons'], true);        
	        $buy= json_decode($my['buying'], true);        

	        $res['gW'] = 0;$res['gE'] = 0;$exp = 0;

	        $arr0 = json_decode($my['nybuy'],true);
	        $arr = array_slice($arr0, 2, count($arr0)-2);

	        if(!in_array(0, $arr))$res['gE']=1;
	        if((int)$arr0[1] > 0)$res['gW']=1;
	        if((int)$arr0[0] > 0)$res['gW']=1;

	        for($i=0;$i<count($myt);$i++){
	            if($myt[$i][2] < 9)$exp+=1;
	            if($myt[$i][2] > 8 and $myt[$i][2] < 11)$exp+=2;
	            if($myt[$i][2] > 10 and $myt[$i][2] < 15)$exp+=4;
	        }
	        $lvl = $this->registry['tools']->customLevel($lvls, $exp);

	        $my['coins']+= $pod[$lvl][0];
	        $my['gold']+= $pod[$lvl][1];
	        $wps[0]+= $pod[$lvl][2];
	        $wps[1]+= $pod[$lvl][3];
	        $wps[2]+= $pod[$lvl][4];
	        $my['weapons'] = json_encode($wps);
	        if($res['gW']==1){
	            array_push($buy, 37);array_push($buy, 38);array_push($buy, 39);array_push($buy, 40);
	        }
	        if($res['gE']==1)$my['ebonus']+=50;

	        $my['buying'] = json_encode($buy);
	        $my['nybol'] = 1;

	        $this->registry['udb']->saveData($this->registry['utb'], $my);
	        $this->registry['tools']->mandatBonus();

	        echo json_encode($my); 
	    }

	    function buySnow(){
	        $my = $this->registry['udb']->getData($this->registry['utb'], array('*'), 'id='.$this->registry['uid']);

	        if(8*(int)$this->registry['num'] > (int)$my['gold'])return $this->registry['tools']->error(7);
	        $my['snow']+=(int)$this->registry['num'];
	        $my['gold']-=8*(int)$this->registry['num'];

	        $this->registry['udb']->saveData($this->registry['utb'], $my);

	        echo json_encode($my);
	    }

	    function setQuests(){
	    	$my = $this->registry['udb']->getData($this->registry['utb'], array('*'), 'id='.$this->registry['uid']);
	        
	        $qst = json_decode($my['nyquests'], true);
	        $qst[(int)$this->registry['qid']] = (int)$this->registry['qstatus'];
	        if(!isset($qst[0]))$qst[0] = 0;
	        if(!isset($qst[1]))$qst[1] = 0;
	        if(!isset($qst[2]))$qst[2] = 0;
	        if((int)$qst[1]==1 and (int)$qst[2]==1 and count(explode(',', $my['friends']))-1 >= 20)$qst[0] = 1;
	        
	        $my['nyquests'] = json_encode($qst);

	        $this->registry['udb']->saveData($this->registry['utb'], $my);
	        
	        echo json_encode($qst);
	    }

	    function updateTree(){
	        $params = $this->registry['params'];
	        
	        $row = $this->registry['udb']->getData($this->registry['utb'], array('*'), 'id='.$this->registry['uid']);

	        $buy = json_decode($row['nybuy'], true);
	        $minus = $buy; for($i=0;$i<count($minus);$i++)$minus[$i]=0;

	        for($i=0;$i<count($params);$i++)$minus[(int)$params[$i][2]]++;
	        for($i=0;$i<count($minus);$i++)$buy[$i] -= (int)$minus[$i];

	        if(in_array(-1, $buy))return $this->registry['tools']->error(28);

	        $row['toyspos'] = json_encode($params);

	        $this->registry['udb']->saveData($this->registry['utb'], $row);

	        echo json_encode($row); 
	    }

	    function buyToy(){
	        $row = $this->registry['udb']->getData($this->registry['utb'], array('*'), 'id='.$this->registry['uid']);

	        $buy = json_decode($row['nybuy'], true);

	        if(!isset($buy[(int)$this->registry['tid']]))$buy[(int)$this->registry['tid']] = 0;

	        if((int)$buy[(int)$this->registry['tid']] > 0 and (int)$this->registry['tid'] < 2)return $this->registry['tools']->error(26);
	        if((int)$row['snow'] < (int)$this->registry['ny'][(int)$this->registry['tid']])return $this->registry['tools']->error(27);

	        $buy[(int)$this->registry['tid']] = (int)$buy[(int)$this->registry['tid']]+1;
	        $row['nybuy'] = json_encode($buy);
	        $row['snow'] = (int)$row['snow'] - (int)$this->registry['ny'][(int)$this->registry['tid']];

	        $this->registry['udb']->saveData($this->registry['utb'], $row);

	        echo json_encode($row); 
	    }

	    function getSnow(){
	        $row = $this->registry['udb']->getData($this->registry['utb'], array('*'), 'id='.$this->registry['uid']);
	                     
	        if(time() - (int)$row['nytimer'] < 86400)return $this->registry['tools']->error(25);

	        $row['nytimer'] = time();
	        $row['snow'] = (int)$row['snow']+2;
	        
	        
	        $this->registry['udb']->saveData($this->registry['utb'], $row);
	        
	        echo json_encode($row);     
	    }
	}
?>