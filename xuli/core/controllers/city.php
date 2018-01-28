<?php

	Class City {

                private $registry;

                private $endLocation = 0;
                private $weaponWin = 0;
                private $weaponWinID = 0;
                private $wearWin = 0;

                function __construct($registry) {
                        $this->registry = $registry;
                }

                function startQuest(){
                        $user = $this->registry['udb']->getData($this->registry['utb'], array('fwins', 'gold', 'achives_level', 'learn', 'buying', 'emax', 'etime', 'locs', 'coins', 'exp', 'energy', 'quests', 'locsn', 'weapons', 'id'), 'id='.$this->registry['uid']);
                        $user['quests'] = (array)json_decode($user['quests'], true);
                        $user['locsn'] = (array)json_decode($user['locsn'], true);
                        $user['locs'] = (array)json_decode($user['locs'], true);

                        $oldexp = $user['exp'];

                        if(!isset($user['locsn'][$this->registry['locID']]))$user['locsn'][$this->registry['locID']] = 0;
                        if(!isset($user['quests'][$this->registry['qID']]))$user['quests'][$this->registry['qID']] = 0;
                        if(!isset($user['locs'][$this->registry['locID']]))$user['locs'][$this->registry['locID']] = 0;

                        if($this->registry['quests']['q'][$this->registry['qID']]['want']['level'] > $this->registry['tools']->customLevel((int)$user['exp'], $this->registry['levels']))return $this->registry['tools']->error(18);
                        if($this->registry['quests']['q'][$this->registry['qID']]['want']['q'] > -1 and (int)$user['quests'][$this->registry['quests']['q'][$this->registry['qID']]['want']['q']] < $this->registry['quests']['q'][$this->registry['quests']['q'][$this->registry['qID']]['want']['q']]['rps'])return $this->registry['tools']->error(4);
                        if($user['quests'][$this->registry['qID']] == $this->registry['quests']['q'][$this->registry['qID']]['rps'])return $this->registry['tools']->error(6);

                        $user['energy'] = $this->registry['tools']->myEnergy()['energy'];

                        if((int)$user['energy'] < 0 || (int)$user['energy'] < $this->registry['quests']['q'][$this->registry['qID']]['energy'])return $this->registry['tools']->error(5);

                        $user = $this->randomItems($user);

                        if((int)$user['energy'] >= (int)$user['emax'])$user['etime'] = time();

                        $user['quests'][$this->registry['qID']] += 1;
                        $user['energy'] -= (int)$this->registry['quests']['q'][$this->registry['qID']]['energy'];
                        $user['exp'] += (int)$this->registry['quests']['q'][$this->registry['qID']]['exp'];
                        $user['locsn'][$this->registry['locID']] += (int)$this->registry['quests']['q'][$this->registry['qID']]['exp'];
                        $user['coins'] += (int)$this->registry['quests']['q'][$this->registry['qID']]['coins'];

                        $user = $this->endLocation($user);
                        $user['quests'] = json_encode($user['quests']);
                        $user['locsn'] = json_encode($user['locsn']);
                        $user['locs'] = json_encode($user['locs']);
                        if(!$this->registry['tools']->isComplete(256, $user['learn']))$user['learn'] = $this->registry['tools']->learnStatus($user);

                        $this->registry['udb']->saveData($this->registry['utb'], $user);

                        if($this->endLocation == 1)$user = $this->registry['tools']->myAchives();

                        $this->registry['tools']->newLevel($user['exp'], $oldexp);

                        $user['endLocation'] = $this->endLocation;
                        $user['weaponWin'] = $this->weaponWin;
                        $user['weaponWinID'] = $this->weaponWinID;
                        $user['wearWin'] = $this->wearWin;

                        echo json_encode($user);
                }

                function randomItems($user){                        
                        if($this->registry['tools']->winRandom(1,100)){
                            $r3 = rand(0,2);
                            $user['weapons'] = (array)json_decode($user['weapons'], true);
                            if(!isset($user['weapons'][$r3]))$user['weapons'][$r3] = 0;
                            $user['weapons'][$r3]++;
                            $user['weapons'] = json_encode($user['weapons']);
                            $this->weaponWin = 1;
                            $this->weaponWinID = $r3;
                        }else if($this->registry['tools']->winRandom(1,100) and $this->registry['quests']['l'][$this->registry['locID']]['price']['w'] !== -1){
                            $user['buying'] = (array)json_decode($user['buying'], true);
                            if(!in_array($this->registry['quests']['l'][$this->registry['locID']]['price']['w'], $user['buying'])){
                                array_push($user['buying'], $this->registry['quests']['l'][$this->registry['locID']]['price']['w']);
                                $this->wearWin = 1;
                            }
                            $user['buying'] = json_encode($user['buying']);
                        }

                        return $user;
                }

                function endLocation($user){
                        if($this->registry['qID'] == $this->registry['quests']['l'][$this->registry['locID']]['quests'][count($this->registry['quests']['l'][$this->registry['locID']]['quests'])-1] && $user['quests'][$this->registry['qID']] == $this->registry['quests']['q'][$this->registry['qID']]['rps']){
                                for($i = 0; $i < count($this->registry['quests']['l'][$this->registry['locID']]['quests']); $i++)$user['quests'][$this->registry['quests']['l'][$this->registry['locID']]['quests'][$i]] = 0;

                                $user['coins'] += intval($this->registry['quests']['l'][$this->registry['locID']]['price']['money']);
                                $user['exp'] += intval($this->registry['quests']['l'][$this->registry['locID']]['price']['exp']);
                                $user['locs'][$this->registry['locID']] += 1;

                                $this->registry['achives'] = json_decode(file_get_contents('json/achives.json'), true);
                                
                                $this->endLocation = 1;
                        }

                        return $user;
                }

                function workUpdate(){
                    $result = $this->registry['udb']->getData($this->registry['utb'], array('gold', 'wlvl', 'wtime', 'id'), 'id='.$this->registry['uid']);
                    
                    if((int)$result['gold'] < (int)$this->registry['work'][(int)$result['wlvl']][0])return $this->registry['tools']->error(7);
                    if((int)$result['wlvl'] >= count($this->registry['work']))return $this->registry['tools']->error(8);

                    $result['gold'] -= (int)$this->registry['work'][(int)$result['wlvl']][0];
                    $result['wlvl']++;
                    $result['wtime'] = time();

                    $this->registry['udb']->saveData($this->registry['utb'], $result);
                    
                    echo '{"result":"okay"}';   
                }
                
                function workTake(){
                    $result = $this->registry['udb']->getData($this->registry['utb'], array('coins', 'wlvl', 'wtime', 'id', 'exp'), 'id='.$this->registry['uid']);

                    if(time() - (int)$result['wtime'] < 28800)return $this->registry['tools']->error(9);

                    $oldexp = $result['exp'];

                    $result['exp'] += (int)$this->registry['work'][(int)$result['wlvl']-1][2];
                    $result['coins'] += (int)$this->registry['work'][(int)$result['wlvl']-1][1];
                    $result['wtime'] = time();

                    $this->registry['tools']->newLevel($result['exp'], $oldexp);
                            
                    $this->registry['udb']->saveData($this->registry['utb'], $result);
                    
                    echo '{"result":"okay"}';
                }

	}

?>