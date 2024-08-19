<?php
require ('../../assets/header.php');
require ('../../vendor/autoload.php');

use GoogleAuth\GoogleAuth;

// Start the Google OAuth process
GoogleAuth::initiateGoogleLogin();
