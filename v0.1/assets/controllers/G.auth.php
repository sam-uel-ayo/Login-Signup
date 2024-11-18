<?php
namespace GoogleAuth;

use CUtils\CUtils;
use MAuth\MAuth;
use Google\Client as GoogleClient;
use Google\Service\Oauth2 as Google_Service_Oauth2;
use Exception;

class GoogleAuth {

    private static $client;

    // Initialize the Google Client
    public static function init()
    {
        self::$client = new GoogleClient();
        self::$client->setAuthConfig(__DIR__ . '/client_secret.json');
        self::$client->setRedirectUri("http://localhost/mart/v0.1/api/auth_g/callback.php");
        self::$client->addScope('email');
        self::$client->addScope('profile');

        $state = bin2hex(random_bytes(16)); // Generate a random 16-byte value
        $_SESSION['oauth2state'] = $state; // Store it in the cache later in the project
        self::$client->setState($state); // Set the state parameter for the auth request

    }

    // Initiate Google OAuth
    public static function initiateGoogleLogin()
    {
        self::init(); // Initialize the client
        $authUrl = self::$client->createAuthUrl();
        header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
        exit();
    }

    // Handle Google OAuth callback
    public static function handleGoogleCallback()
    {
        self::init(); // Initialize the client

        // // Check if the state parameter in the request matches the one stored in the session
        // if (!isset($_GET['state']) || !isset($_SESSION['oauth2state']) || $_GET['state'] !== $_SESSION['oauth2state']) { // State parameter mismatch or not set
        //     return json_decode(CUtils::returnData(false, "Invalid state parameter, Try authenticating again", null, true));
        // }
        // unset($_SESSION['oauth2state']);

        try {
            if (isset($_GET['code'])) {
                $token = self::$client->fetchAccessTokenWithAuthCode($_GET['code']); // $client->authenticate($_GET['code']); 
                // fetchAccessTokenWithAuthCode returns and associtive array like 
                // Array(
                //     [access_token] => ya29.A0ARrdaM8J2J3T1l7QWzK7VhJH3G5_n7V9FZfJ_c8hTRdJtY4qYfG8JkA2u5D-9_2N-YiCk
                //     [expires_in] => 3599
                //     [refresh_token] => 
                //     [scope] => https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile
                //     [token_type] => Bearer
                //     [id_token] => 
            
                self::$client->setAccessToken($token['access_token']); // Where $token['access_token'] is the access token string
                


                // Get user info
                $google_oauth = new Google_Service_Oauth2(self::$client); // $oauth2 = new Oauth2(self::$client); - old method
                $userInfo = $google_oauth->userinfo->get();
                $email =  $userInfo->email;
                $name =  $userInfo->name;

                // Check if the user already exists in your database using their ID or email
                $checkUser = json_decode(MAuth::checkUserMail($userInfo->email));
                if ($checkUser->status == false) {
                    // User doesn't exist, create a new user
                    return self::registerGoogleUser($userInfo);
                } else {
                    // User exists, log them in
                    return json_decode(CUtils::returnData(true, "Logged In", $checkUser->data->id, true));
                }
            } else {
                return json_decode(CUtils::returnData(false, "Google Authentication failed", null, true));
            }
        } catch (Exception $e) {
            return CUtils::returnData(false, $e->getMessage(), null, true);
        }
    }

    private static function registerGoogleUser($userInfo)
    {
        try {
            $email = $userInfo->email;
            $firstname = $userInfo->givenName;
            $lastname = $userInfo->familyName;

            // Generate a random password for the user
            $password = bin2hex(random_bytes(8));
            $hashpassword = password_hash($password, PASSWORD_DEFAULT);
            $signupUser = json_decode(MAuth::userSignup($email, $hashpassword, $firstname, $lastname, $auth='Google'));
            if ($signupUser->status == false) {
                return json_decode(CUtils::returnData(false, $signupUser->message, null, true));
            }

            // Send mail
            $subject = 'Welcome to Our Shop!';
            $body = "<p> $firstname $lastname Thank you for registering with us. We're excited to have you on board! Please check your mail to change the generated password: $password we send to you</p>";
            $mailer = json_decode(CUtils::sendEmail($email, $subject, $body));

            return json_decode(CUtils::returnData(true, "Google User Registered", $signupUser->data, true));
        } catch (Exception $e) {
            return CUtils::returnData(false, "Something went wrong try again", null, true);
        }
    }

}
