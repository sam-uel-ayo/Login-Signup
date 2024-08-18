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
    public static function userSignup($email, $password, $first_name, $last_name)
    {
        $conn = Database::getConnection();
        try {
            $conn->beginTransaction();
    
            $stmt1 = $conn->prepare("INSERT INTO users_info (email, password) VALUES (:email, :password)");
            $stmt1->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt1->bindParam(':password', $password);
            if (!$stmt1->execute()) {
                throw new PDOException("Failed to insert into users_info");
            }
    
            $user_id = $conn->lastInsertId();
    
            $stmt2 = $conn->prepare("INSERT INTO users_details (user_id, first_name, last_name) VALUES (:user_id, :first_name, :last_name)");
            $stmt2->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt2->bindParam(':first_name', $first_name, PDO::PARAM_STR);
            $stmt2->bindParam(':last_name', $last_name, PDO::PARAM_STR);
            if (!$stmt2->execute()) {
                throw new PDOException("Failed to insert into users_details");
            }
    
            $conn->commit();
            return CUtils::returnData(true, "Account created", [], true);
        } catch (PDOException $e) {
            // Rollback the transaction if something went wrong
            $conn->rollBack();
            return CUtils::returnData(false, "Something went wrong: " . $e->getMessage(), [], true);
        }
    }
      // End of method

}
