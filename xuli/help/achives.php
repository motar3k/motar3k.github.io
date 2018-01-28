<?php

	$object = array();

	newObj('maxdamage', array('n'=>array('Дрыщ','Доходяга','Качок','Бодибилдер','Терминатор','Доминатор'), 'v'=>array(10000,30000,50000,100000,500000,1000000), 't'=>array('нанести', 'урона в 1 бою')), array('t'=>'coins', 'p'=>array(1000,3000,5000,10000,50000,100000)));

	newObj('weapons 0', array('n'=>array('Хулиган','Лаптист','Бейсболист','Крикерист','Лесоруб'), 'v'=>array(10,50,100,500,1000), 't'=>array('собрать', 'бит')), array('t'=>'weapons', 'p'=>array(array(2,0,0),array(10,0,0),array(25,0,0),array(50,0,0),array(100,0,0))));

	newObj('weapons 1', array('n'=>array('Гражданин','Охранник','Бандит','Вояка','Спецназ'), 'v'=>array(10,70,100,500,3000), 't'=>array('собрать', 'травматов')), array('t'=>'weapons', 'p'=>array(array(0,2,0),array(0,10,0),array(0,25,0),array(0,50,0),array(0,100,0))));

	newObj('weapons 2', array('n'=>array('Бомж','Алкоголик','Шкет','Романтик','Стеклотарщик'), 'v'=>array(10,50,100,500,1000), 't'=>array('собрать', 'розочек')), array('t'=>'weapons', 'p'=>array(array(0,0,2),array(0,0,10),array(0,0,25),array(0,0,50),array(0,0,100))));

	newObj('naperstki games', array('n'=>array('Лошок','Шаровик','Игрок','Зритель','Лохотронщик'), 'v'=>array(100,500,1000,5000,10000), 't'=>array('сыграть в наперстки', 'раз')), array('t'=>'gold', 'p'=>array(10,50,150,250,500)));

	newObj('locs 0', array('n'=>array('Селюк','Шестёрка','Карманник','Разводила','Проводник'), 'v'=>array(5,15,30,50,100), 't'=>array('захватить вокзальный', 'раз')), array('t'=>'exp', 'p'=>array(100,200,400,600,1000)));

	newObj('locs 1', array('n'=>array('Проходящий','Свидетель','Зырящий','Шухер','Угонщик'), 'v'=>array(5,15,30,50,100), 't'=>array('захватить автобанский', 'раз')), array('t'=>'exp', 'p'=>array(120,240,480,720,1150)));

	newObj('locs 2', array('n'=>array('Шпана','Бродяга','Медвежатник','Хитрец','Вор'), 'v'=>array(5,15,30,50,100), 't'=>array('захватить лапинский', 'раз')), array('t'=>'exp', 'p'=>array(140,280,540,820,1300)));
    
    echo json_encode($object);
    
    function newObj($type, $levels, $price){
        global $object;

        for($i = 0; $i < count($levels['n']); $i++)$levels['n'][$i] = base64_encode($levels['n'][$i]);
        	for($i = 0; $i < count($levels['t']); $i++)$levels['t'][$i] = base64_encode($levels['t'][$i]);

        array_push($object, array('t'=>$type, 'l'=>$levels, 'p'=>$price));
    }

?>