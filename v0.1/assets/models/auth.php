<?php
namespace MAuth;

use Database\Database;
use CUtils\CUtils;
use PDO;
use PDOException; // Change later


class MAuth {

    //  Check and get details with email 
    public static function checkUserMail ($email)
    {
        try {
            $query = "SELECT token, password FROM users where email = :email";
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
    }
}
