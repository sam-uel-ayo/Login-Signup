<?php
namespace GAuth;

use CUtils\CUtils;
use MAuth\MAuth;
use Google\Client as GoogleClient;
use Google\Service\Oauth2;
use PDOException;

class GAuth {

    private static $client;

    // Initialize the Google Client
    public static function init()
    {
        self::$client = new GoogleClient();
        self::$client->setClientId('858280503101-prdmg0mqe0a5khn3hlskslms1i41c146.apps.googleusercontent.com');
        self::$client->setClientSecret('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');
        self::$client->setRedirectUri('http://localhost/PHP/sheda_mart/v0.1/api/auth/welcome.php'); // e.g., http://yourdomain.com/google_callback.php
        self::$client->addScope('email');
        self::$client->addScope('profile');
    }

    // Initiate Google OAuth
    public static function initiateGoogleLogin()
    {
        self::init(); // Initialize the client
        $authUrl = self::$client->createAuthUrl();
        echo "Redirecting to Google...";
        header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
        exit();
    }

    // Handle Google OAuth callback
    public static function handleGoogleCallback()
    {
        self::init(); // Initialize the client
        try {
            if (isset($_GET['code'])) {
                $token = self::$client->fetchAccessTokenWithAuthCode($_GET['code']);
                self::$client->setAccessToken($token);

                // Get user info
                $oauth2 = new Oauth2(self::$client);
                $googleUser = $oauth2->userinfo->get();

                // Check if the user already exists in your database using their Google ID or email
                $checkUser = json_decode(MAuth::checkUserMail($googleUser->email));
                
                if ($checkUser->status == false) {
                    // User doesn't exist, create a new user
                    return self::registerGoogleUser($googleUser);
                } else {
                    // User exists, log them in
                    return json_decode(CUtils::returnData(true, "Logged In", $checkUser->data->id, true));
                }
            } else {
                return json_decode(CUtils::returnData(false, "Google Authentication failed", null, true));
            }
        } catch (PDOException $e) {
            return CUtils::returnData(false, $e->getMessage(), null, true);
        }
    }

    private static function registerGoogleUser($googleUser)
    {
        try {
            $email = $googleUser->email;
            $firstname = $googleUser->givenName;
            $lastname = $googleUser->familyName;

            // You may want to generate a random password for the user or leave it null
            $password = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);

            $signupUser = json_decode(MAuth::userSignup($email, $password, $firstname, $lastname));
            if ($signupUser->status == false) {
                return json_decode(CUtils::returnData(false, $signupUser->message, null, true));
            }

            return json_decode(CUtils::returnData(true, "Google User Registered", $signupUser->data->id, true));
        } catch (PDOException $e) {
            return CUtils::returnData(false, $e->getMessage(), null, true);
        }
    }

}
