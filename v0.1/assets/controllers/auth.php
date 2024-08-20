<?php
namespace CAuth;

use CUtils\CUtils;
use MAuth\MAuth;
use Exception; 

class CAuth {

    // User login 
    public static function userLogin ($data) // Email and password
    {
        try {
            if ($data == null) {
                return CUtils::returnData(false, "No input found", $data, true);
            }

            $checkUser = json_decode(MAuth::checkUserMail($data->email)); // 
            if ($checkUser->status == false) {
                return json_decode(CUtils::returnData(false, "Please Signup", $data, true));
            }

            if ($checkUser->status == true) {
                if (!password_verify($data->password, $checkUser->data->password)) {
                    return json_decode(CUtils::returnData(false, "Password Incorrect", $data, true));
                } else {
                    return json_decode(CUtils::returnData(true, "Logged In", $checkUser->data->id, true));
                }
            }
        } catch (Exception $e) {
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
            
            // Send Mail
            $subject = 'Welcome to Our Shop!';
            $body = "<p> $firstname $lastname Thank you for registering with us. We're excited to have you on board!</p>";
            $mailer = json_decode(CUtils::sendEmail($data->email, $subject, $body));
            
            return json_decode(CUtils::returnData(true, "Registration successful", $data, true));

        } catch (Exception $e) {

        }
    }
    // Method End


    // Get User Personal information
    public static function userProfile ($token)
    {
        try {
            if ($token == null) {
                return CUtils::returnData(false, $token, true);
            }

            $getProfile = json_decode(MAuth::userProfile($token)); // 
            if ($getProfile->status == false) {
                return json_decode(CUtils::returnData(false, $getProfile->message, $token, true));
            }

            return json_decode(CUtils::returnData(true, $getProfile->message, $getProfile->data, true));

        } catch (Exception $e) {
            return CUtils::returnData(false, $e->getMessage(), $token, true);
        }
    }
    // Method End


    // Edit/Add User infomation
    public static function editProfile ($data) // firstname , lastname, phonenumber, dob, token
    {
        try {
            if ($data == null) {
                return CUtils::returnData(false, "Input not found", $data, true);
            }

            $firstname = trim($data->first_name);
            $lastname = trim($data->last_name);
            $phonenumber = trim($data->phone_number);
            if (!preg_match("/^[a-zA-Z'-]+$/", $firstname)) {
                return json_decode(CUtils::returnData(false, "Invalid First name format", $data, true));
            }
            if (!preg_match("/^[a-zA-Z'-]+$/", $lastname)) {
                return json_decode(CUtils::returnData(false, "Invalid Last name format", $data, true));
            }
            if (!preg_match("/^\+?[\d\s\-\(\)]{10,}$/", $phonenumber)) {
                return json_decode(CUtils::returnData(false, "Invalid Phone number format", $data, true));
            }

            // Set date and time

            $editProfile = json_decode(MAuth::editProfile($firstname, $lastname, $data->dob, $phonenumber, $data->token)); 
            if ($editProfile->status == false) {
                return json_decode(CUtils::returnData(false, $editProfile->message, $editProfile->data, true));
            }

            return json_decode(CUtils::returnData(true, $editProfile->message, $editProfile->data, true));

        } catch (Exception $e) {
            return json_decode(CUtils::returnData(false, $e->getMessage(), $data, true));
        }
    }
    // Method End


    // Change password
    public static function changePassword ($data) // email, oldpassword, password, confirm password
    {
        try {
            if ($data == null) {
                return CUtils::returnData(false, "Input not complete", $data, true);
            }

            $checkUser = json_decode(MAuth::checkUserMail($data->email)); // 
            if ($checkUser->status == true) {
                // Verify if the old password is correct
                if (!password_verify($data->oldpassword, $checkUser->data->password)) {
                    return json_decode(CUtils::returnData(false, "Old Password Incorrect", $data, true));
                }
            
                // Check if the old password matches the new password
                if (password_verify($data->password, $checkUser->data->password)) {
                    return json_decode(CUtils::returnData(false, "New password cannot be the same as the old password", $data, true));
                }
            }

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
                return json_decode(CUtils::returnData(false, "Password do not match", $data, true));
            }
            $password = password_hash($data->password, PASSWORD_DEFAULT);

            $changePassword = json_decode(MAuth::changePassword ($data->email, $password));
            if ($changePassword->status == false) {
                return json_decode(CUtils::returnData(false, $changePassword->message, $changePassword->data, true));
            }
            
            return json_decode(CUtils::returnData(true, "Password changed", true));

        } catch (Exception $e) {
            return CUtils::returnData(false, $e->getMessage(), $data, true);
        }
    }
    // Method End
}
