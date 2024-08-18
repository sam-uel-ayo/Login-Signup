<?php 
// Client ID : 858280503101-prdmg0mqe0a5khn3hlskslms1i41c146.apps.googleusercontent.com
// Client secret : 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
// Redirect URL : http://localhost/PHP/sheda_mart/v0.1/api/auth/welcome.php
?>
<?php
require ('../../assets/header.php');
require ('../../vendor/autoload.php');

use GAuth\GAuth;
use CUtils\CUtils;

// Handle the Google OAuth callback
$user = GAuth::handleGoogleCallback();

// Output the result
return CUtils::outputData($user->status, $user->message, $user->data, true);
