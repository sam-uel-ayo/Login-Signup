<?php
require ('../../assets/header.php');
require ('../../vendor/autoload.php');

use GAuth\GAuth;

// Start the Google OAuth process
GAuth::initiateGoogleLogin();

