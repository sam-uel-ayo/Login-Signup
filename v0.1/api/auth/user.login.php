<?php
require '../../assets/header.php';
use CUtils\CUtils;
use CAuth\CAuth;


$requiredKeys = ['email', 'password'];
$optionalKeys = [];

CUtils::validatePayload($requiredKeys, $data);
$data = CUtils::arrayToObject($data);

//Logic here
$user = CAuth::userLogin($data->email, $data->password);
return CUtils::outputData($user->status, $user->message, $user->data, true);
