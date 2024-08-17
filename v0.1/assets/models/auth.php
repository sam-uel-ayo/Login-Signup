<?php
namespace MAuth;

use Database\Database;
use CUtils\CUtils;
use PDO;
use PDOException; // Change later


class MAuth {

    //  Check and get details with email - Login 
    public static function checkUserMail ($email)
    {
        try {
            $query = "SELECT id,  password FROM users_info WHERE email = :email";
            $stmt = Database::getConnection()->prepare($query);

            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            if(!$stmt->execute()){
                exit;
            }

            if ($stmt->rowCount() < 1){
                return CUtils::returnData(false, $email, true); // Email doesn't exist
            }

            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            return CUtils::returnData(true,null, $results, true); // Results is user token and password

        } catch (PDOException $e) {
            return CUtils::returnData(false, $e->getMessage(), $email, true);
        }
    } // End of method 


    // Check if email doesn't exist and can be used
    public static function checkMail($email)
    {
        try {
            $query = "SELECT id FROM users_info WHERE email = :email";
            $stmt = Database::getConnection()->prepare($query);

            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            if(!$stmt->execute()){
                exit;
            }

            if ($stmt->rowCount() > 0){
                return CUtils::returnData(false, null, $email, false); // Email exist - can't use
            } else {
                return CUtils::returnData(true, null, $email, false); // Email doesn't exist - can use
            }

        } catch (PDOException $e) {
            return CUtils::returnData(false, $e->getMessage(), null, true);
        }
    } // End of method


    // User Signup
    public static function userSignup (...$data) // Email, Password, First name, Last name
    {
        try {
            $query ="BEGIN;
                    INSERT INTO users_info (email, password) 
                        VALUES (:email, :password); 
                    INSERT INTO users_details (user_id, first_name, last_name) 
                        VALUES (LAST_INSERT_ID(),:first_name,:last_name); 
                    COMMIT;";
            $stmt = Database::getConnection()->prepare($query);

            $stmt->bindParam(':email', $data[0], PDO::PARAM_STR);
            $stmt->bindParam(':password', $data[1]);
            $stmt->bindParam(':first_name', $data[2], PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $data[3], PDO::PARAM_STR);


            if(!$stmt->execute()){
                return CUtils::returnData(false, "Something went wrong, Try again", $data, true);
            } 
            return CUtils::returnData(true, "Account created", $data, true);

        } catch (PDOException $e) {
            return CUtils::returnData(false, "Something went wrong: " . $e->getMessage(), [], true);
        }
    }  // End of method

}
