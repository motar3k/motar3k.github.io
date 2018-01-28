<?php

	Class User {

                private $registry;

                function __construct($registry) {
                        $this->registry = $registry;
                }

                function setFriends() {
                        $result = $this->registry['udb']->getData($this->registry['utb'], array('id', 'exp'), 'id IN('.$this->registry['friends'].') ORDER BY exp DESC LIMIT 100');
                        $this->registry['udb']->saveData($this->registry['utb'], array('friends'=>$this->registry['friends'], 'id'=>$this->registry['uid']));

                        echo json_encode($result);
                }

                function getUser(){
                        $result = $this->registry['udb']->getData($this->registry['utb'], array('*'), 'id='.$this->registry['uid']);

                        if(!$result){
                                $this->registry['udb']->saveData($this->registry['utb'], array('etime'=>time(), 'bonustime'=>time(), 'name'=>base64_encode($this->registry['names'][array_rand($this->registry['names'])]), 'id'=>$this->registry['uid'], 'mywears'=>'[-1,-1,-1,-1,-1]', 'bonustime'=>time(), 'weapons'=>'[1,1,3,50]', 'buying'=>'[]', 'visit'=>time(), 'nybuy'=>'[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'));
                                $result = $this->registry['udb']->getData($this->registry['utb'], array('*'), 'id='.$this->registry['uid']);
                                $this->trackUser();
                        }else{
                                $this->registry['udb']->saveData($this->registry['utb'], array('id'=>$this->registry['uid'],'visit'=>time()));
                                $result = array_merge($result, $this->registry['tools']->myEnergy());
                                $result = array_merge($result, $this->registry['tools']->myHobo());
                        }

                        $result['wear_dmg_bonus'] = $this->registry['tools']->wearBonus();

                        echo json_encode($result);
                }

                function getCustomUser(){
                        $result = $this->registry['udb']->getData($this->registry['utb'], array('id','bg', 'name', 'mywears', 'achives_level', 'nyquests', 'toyspos', 'nybuy'), 'id='.$this->registry['fid']);

                        echo json_encode($result);
                }

                function trackUser(){

                }

                function getBonus(){
                        $result = $this->registry['udb']->getData($this->registry['utb'], array('id','bonus', 'bonustime', 'gold', 'coins'), 'id='.$this->registry['uid']);
                        
                        if(time() - (int)$result['bonustime'] < 86400)return $this->registry['tools']->error(17);
                        if(time() - (int)$result['bonustime'] > 172800 or (int)$result['bonus'] > 6)$result['bonus'] = 0;
                        
                        $result['gold'] += $this->registry['bonus'][(int)$result['bonus']][1];
                        $result['coins'] += $this->registry['bonus'][(int)$result['bonus']][0];
                        $result['bonustime'] = time();
                        $result['bonus']++;
                                
                        $this->registry['udb']->saveData($this->registry['utb'], $result);
                        
                        echo json_encode($result);  
                }

                function getHobo(){
                        $result = $this->registry['udb']->getData($this->registry['utb'], array('id','hobo', 'hobotime', 'gold', 'coins', 'exp'), 'id='.$this->registry['uid']);

                        $result = array_merge($result, $this->registry['tools']->myHobo());

                        if($result['hobo'] < 1)return $this->registry['tools']->error(33);

                        $end = array('win'=>'coins');

                        if($result['hobo'] == 5)$result['hobotime'] = time();
                        $result['hobo']--;

                        if($this->registry['tools']->winRandom(1, 10)){
                                $result['gold'] += 1;
                                $end['win'] = 'gold';

                        }else if($this->registry['tools']->winRandom(1, 2)){
                                $result['exp'] += 3;
                                $end['win'] = 'exp';
                        }else{
                                $result['coins'] += 10;
                        }

                        $this->registry['udb']->saveData($this->registry['utb'], $result);

                        $result = array_merge($result, $end);

                        echo json_encode($result);
                }

                function obmenValut(){
                        $result = $this->registry['udb']->getData($this->registry['utb'], array('id', 'gold', 'coins'), 'id='.$this->registry['uid']);

                        if($result['gold'] < $this->registry['val'])return $this->registry['tools']->error(7);

                        $result['gold'] -= $this->registry['val'];
                        $result['coins'] += $this->registry['val']*25;

                        $this->registry['udb']->saveData($this->registry['utb'], $result);

                        echo json_encode($result);
                }

                function shmon(){
                        $fr = $this->registry['udb']->getData($this->registry['utb'], array('id', 'shmon', 'coins'), 'id='.$this->registry['id']);
                        $my = $this->registry['udb']->getData($this->registry['utb'], array('id', 'coins'), 'id='.$this->registry['uid']);
                        
                        if(time()-(int)$fr['shmon'] < 86400)return $this->registry['tools']->error(22);
                        
                        $my['coins'] += rand(5,50);
                        $fr['shmon'] = time();

                        $this->registry['udb']->saveData($this->registry['utb'], $my);
                        $this->registry['udb']->saveData($this->registry['utb'], $fr);
                                                
                        echo json_encode($my);
                }

                function newName(){
                        $this->registry['udb']->saveData($this->registry['utb'], array('id'=>$this->registry['uid'], 'name'=>$this->registry['name']));
                        echo '{"result":"okay"}';     
                }
	}

?>