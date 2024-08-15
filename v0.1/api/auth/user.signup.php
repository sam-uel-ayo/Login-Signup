<?php
require '../../assets/header.php';
use CUtils\CUtils;
use CAuth\CAuth;


$requiredKeys = ['first_name', 'last_name', 'email', 'password', 'cpassword'];
$optionalKeys = [];

CUtils::validatePayload($requiredKeys, $data);
$data = CUtils::arrayToObject($data);

//Logic here
$user = CAuth::userSignup($data);
return CUtils::outputData($user->status, $user->message, $user->data, true);

