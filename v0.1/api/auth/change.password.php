<?php
require '../../assets/header.php';

use cUtils\cUtils;
use CAuth\CAuth;

$requiredKeys = ['email', 'oldpassword', 'password', 'cpassword'];
$optionalKeys = [];

CUtils::validatePayload($requiredKeys, $data);
$data = CUtils::arrayToObject($data);

//Logic here
// 
$user = CAuth::changePassword($data);
// var_dump($user);
return CUtils::outputData($user->status, $user->message, $user->data, true);
