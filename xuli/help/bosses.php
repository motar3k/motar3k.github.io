<?php    
    newObj(0, 'Барыга', 0, 1000, 1, 50, 100, array(25,26,27,28),0);
    newObj(1, 'Мажик', 0, 10000, 2, 100, 500, array(),1);
    newObj(2, 'Фома', 0, 50000, 5, 700, 1000, array(52,53,54,55),2);
    newObj(3, 'Очкарик', 0, 100000, 5, 1000, 3000, array(),2);
    newObj(4, 'Жмых', 0, 500000, 5, 5000, 15000, array(),2);
    
    echo json_encode($object);
    
    function newObj($id, $name, $blocked, $xp, $energy, $exp, $coins, $wears, $loc){
        global $object;
        
        $object[$id]->name = base64_encode($name);
        $object[$id]->blocked = $blocked;
        $object[$id]->xp = $xp;
        $object[$id]->energy = $energy;
        $object[$id]->exp = $exp;
        $object[$id]->coins = $coins;
        $object[$id]->wears = $wears;
        $object[$id]->loc = $loc;
    }
    
?>