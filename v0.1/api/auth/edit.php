<?php
require '../../assets/header.php';

use cUtils\cUtils;
use CAuth\CAuth;

$requiredKeys = ['token', 'email', 'first_name', 'last_name', 'phone_number', 'dob'];
$optionalKeys = [];

CUtils::validatePayload($requiredKeys, $data);
$data = CUtils::arrayToObject($data);

//Logic here
// 
$user = CAuth::editProfile($data);
// var_dump($user);
return CUtils::outputData($user->status, $user->message, $user->data, true);
