<?php
session_start();

require_once('connect.php');


$data = file_get_contents('php://input');
if(base64_encode(base64_decode($data)) == $data){
    $data = !empty($data) ? json_decode(base64_decode($data), true) : [];
}else{
    $data = !empty($data) ? json_decode($data, true) : [];
}
