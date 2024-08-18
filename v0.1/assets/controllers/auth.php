<?php
namespace CAuth;

use CUtils\CUtils;
use MAuth\MAuth;
use PDOException; //Change later

class CAuth {

    // User login 
    public static function userLogin ($data) // Email and password
    {
        try {
            if ($data == null) {
                return CUtils::returnData(false, "No data found", $data, true);
            }

            $checkUser = json_decode(MAuth::checkUserMail($data->email)); // 
            if ($checkUser->status == false) {
                return json_decode(CUtils::returnData($checkUser->status, "Please Signup", $data, true));
            }

            if ($checkUser->status == true) {
                if (!password_verify($data->password, $checkUser->data->password)) {
                    return json_decode(CUtils::returnData(false, "Password Incorrect", $data, true));
                } else {
                    return json_decode(CUtils::returnData(true, "Logged In", $checkUser->data->id, true));
                }
            }
        } catch (PDOException $e) {
            return CUtils::returnData(false, $e->getMessage(), $data, true);
        }
    }


    // User Signup
    public static function userSignup ($data) // Email, Password, Confirm password, First name, Last name as an object for validation of correct input
    {
        try{
            if ($data == null) {
                return CUtils::returnData(false, "No data found", $data, true);
            }

            $checkMail = json_decode(MAuth::checkMail($data->email));
            if ($checkMail->status == false) {
                return json_decode(CUtils::returnData($checkMail->status, "This email is Registered", $data->email, true));
            }
            $validMail = json_decode(CUtils::validateEmail($data->email));
            if ($validMail->status == false) {
                return json_decode(CUtils::returnData($validMail->status, $validMail->message, $data, true));
            }
            $email = $data->email;

            if (strlen($data->password) < 8) {
                return json_decode(CUtils::returnData(false, "Password cannot be less than 8 characters", $data->password, true));
            }
            if (!preg_match('~[0-9]+~', $data->password)) {
                return json_decode(CUtils::returnData(false, "Password must contain a number", $data, true));
            }
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data->password)) {
                return json_decode(CUtils::returnData(false, "Password must contain character", $data, true));
            }
            if ($data->password != $data->cpassword) {
                return json_decode(CUtils::returnData(false, "Password does not match", $data, true));
            }
            $password = password_hash($data->password, PASSWORD_DEFAULT);

            $firstname = trim($data->first_name);
            $lastname = trim($data->last_name);
            if (!preg_match("/^[a-zA-Z'-]+$/", $firstname)) {
                return json_decode(CUtils::returnData(false, "Invalid First name format", $data, true));
            }
            if (!preg_match("/^[a-zA-Z'-]+$/", $lastname)) {
                return json_decode(CUtils::returnData(false, "Invalid Last name format", $data, true));
            }

            $signupUser = json_decode(MAuth::userSignup($email, $password, $firstname, $lastname));
            if ($signupUser->status == false) {
                return json_decode(CUtils::returnData(false, $signupUser->message, $signupUser->data, true));
            }
            
            $subject = 'Welcome to Our Shop!';
            $body = "<p> $firstname $lastname Thank you for registering with us. We're excited to have you on board!</p>";
            $mailer = json_decode(CUtils::sendEmail($data->email, $subject, $body));
            return json_decode(CUtils::returnData(true, "Registration successful", $data, true));

        } catch (PDOException $e) {

        }
    }
}
