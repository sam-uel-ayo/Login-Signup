<?php
namespace CAuth;

use CUtils\CUtils;
use MAuth\MAuth;
use PDOException; //Change later

class CAuth {

    // User login 
    public static function userLogin (...$data) // Email and password
    {
        try {
            if (sizeof($data) < 2 ){
                return json_decode(CUtils::returnData(false, "Data is Missing", $data, true));
            }

            $checkUser = json_decode(MAuth::checkUserMail($data[0])); // $data[0] is email
            if ($checkUser->status == false) {
                return json_decode(CUtils::returnData($checkUser->status, "Please Signup", $data[0], true));
            }

            if ($checkUser->status == true) {
                if ($data[1] === $checkUser->data->password) {
                    return json_decode(CUtils::returnData(true, "Logged In", $checkUser->data->token, true));
                } else {
                    return json_decode(CUtils::returnData(false, "Password Incorrect", $data, true));
                }
            }
        } catch (PDOException $e) {
            return CUtils::returnData(false, $e->getMessage(), $data, true);
        }
    }
}
