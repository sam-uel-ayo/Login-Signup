<?php

require ('../../vendor/autoload.php');

use GuzzleHttp\Client;

$client = new Client();
$response = $client->post('https://oauth2.googleapis.com/token', [
    'form_params' => [
        'code' => '4/0AcvDMrCadGzhDDnr5YzvIAP4eGj6ifqFTn_0DKxlpNfABdviPXu1y312dJyrIssowWqTCA',
        'client_id' => '858280503101-prdmg0mqe0a5khn3hlskslms1i41c146.apps.googleusercontent.com',
        'client_secret' => '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',
        'redirect_uri' => 'http://localhost/PHP/sheda_mart/v0.1/api/auth/welcome.php',
        'grant_type' => 'authorization_code'
    ]
]);

$body = $response->getBody();
$data = json_decode($body, true);

print_r($data);

