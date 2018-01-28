<?php

$link = mysqli_connect('golosovv27', 'golosovv27', '12341234qwe', 'xuli');
$link1 = mysqli_connect('golosovv27', 'golosovv27', '12341234qwe', 'vkapi');


$response = $link1->query('SELECT * FROM `users`');

while($row = mysqli_fetch_array($response, MYSQLI_ASSOC)){
	$weap = (array)json_decode($row['fwins'], true);

	$res = mysqli_fetch_array($link->query('SELECT * FROM `users` WHERE `id`='.$row['id']), MYSQLI_ASSOC);

	$weap1 = (array)json_decode($res['fwins'], true);

	if(!isset($weap1[0]))$weap1[0] = 0;
	if(!isset($weap1[1]))$weap1[1] = 0;
	if(!isset($weap1[2]))$weap1[2] = 0;
	if(!isset($weap1[3]))$weap1[3] = 0;

	$weap1[1] += $weap[2];
	$weap1[2] += $weap[3]

	$weap1 = json_encode($weap1);


	$link->query("UPDATE `users` SET `fwins`='".$weap1."' WHERE `id`=".$row['id']);
}

?>

