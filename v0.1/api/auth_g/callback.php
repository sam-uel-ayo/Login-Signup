<?php
require ('../../assets/header.php');
require ('../../vendor/autoload.php');

use GoogleAuth\GoogleAuth;
use CUtils\CUtils;

// Handle the Google OAuth callback
$user = GoogleAuth::handleGoogleCallback();

// Output the result
echo CUtils::outputData($user->status, $user->message, $user->data, true);
