<?php

        Class Jsonloader {

                function get($name, $decode = false, $type = false){                
                        $type?($decode?$data = json_decode(file_get_contents('json/'.$type.'/'.$name.'.json'), true):$data = file_get_contents('json/'.$type.'/'.$name.'.json')):($decode?$data = json_decode(file_get_contents('json/'.$name.'.json'), true):$data = file_get_contents('json/'.$name.'.json'));
                        
                        return $data;
                } 
	}

?>