<?php
/*Vlad Tolmachev 2017*/
 
	Class Database {
		private $link;//коннект к MySQL
		
		function __construct($registry) {
			$this->link = mysqli_connect($registry['server'], $registry['user'], $registry['pass'], $registry['db']);
		}

		function getData($table, $fields, $where=1, $asArray = false){
			$response = $this->link->query("SELECT ".implode(",", $fields)." FROM ".$table." WHERE ".$where);//спрашиваем у БД такие данные
			if($response->num_rows < 1){
				return false;//если не нашлось данных
			}else if($response->num_rows > 1 || $asArray){
				$data = array();
				while($row = mysqli_fetch_array($response, MYSQLI_ASSOC))array_push($data, $row);

				return $data;
			}

			return mysqli_fetch_array($response, MYSQLI_ASSOC);
		}

		function deleteData($table, $where){
			$this->link->query("DELETE FROM ".$table." WHERE ".$where);
		}

		function saveData($table, $data){
			$data = $this->toSQL($data);//делаем правильную SQL-строку
			$this->link->query("INSERT INTO ".$table." SET ".$data." ON DUPLICATE KEY UPDATE ".$data);//если id-а еще нет, то вставляем, а если есть - обновляем
		}

		function toSQL($array){
			$sql="";
	   		$keys  = array_keys($array);
	    	$values =  array_values($array);
	    
	    	for($i=0;$i<count($keys);$i++){
	        	$sql .= "`".implode('', (array)$keys[$i])."`='".implode('', (array)$values[$i])."'";
	        	if($i < count($keys)-1)$sql.=",";
	    	}

	   		return $sql;
		}

	}
?>