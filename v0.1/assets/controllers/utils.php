<?php
namespace CUtils;
require ('../../vendor/autoload.php');

class CUtils {

    // Verify payload
    public static function validatePayload(array $requiredKeys, $data, array $optionalKey = [])
    {
        $validKeys = array_merge($requiredKeys, $optionalKey);

        $invalidKeys = array_diff(array_keys($data), $validKeys);

        if (!empty($invalidKeys)) {
            foreach ($invalidKeys as $key) {
                $errors[] = "$key is not a valid input field";
            }
        }

        foreach ($requiredKeys as $key) {
            if (empty($data[$key])) {
                $errors[] = ucfirst($key) . ' is required';
            }
        }

        if (!empty($errors)) {
            self::outputData(false, "Payload Error", $errors, true);
        }
    }
    // End verify payload


    // Output data to user/ frontend
    public static function outputData ($status=false, $message=null, $data=null, $exit =false) 
    {
        if ($data == null) {
            $data = array();
        }
        $output = array(
            'status' => $status,
            'message' => $message,
            'data' => $data
        );
        header('Content-Type: application/json');
        echo json_encode($output);

        foreach (get_defined_vars() as $var) {
            unset($var);
        }

        if ($exit == true) {
            exit();
        }
    }


    // return data to be used in program
    public static function returnData ($status= false, $message=null, $data=array(), $exit = false, $httpStatus=200) 
    {
        $output = array(
            'status' => $status,
            'message' => $message,
            'data' => $data,
        );
        return json_encode($output);

        foreach (get_defined_vars() as $var) {
            unset($var);
        }

        if ($exit == true) {
            exit();
        }
    }

    public static function arrayToObject($data)
    {
    if (is_array($data)) {
        return json_decode(json_encode($data));
    }
    return null; // Return null if $data is not an array
    }

    public static function objectToArray($data)
    {
        if (is_object($data) || is_array($data)) {
            return json_decode(json_encode($data), true);
        }
        return null; // Return null if $data is not an object or array
    }


    // validate email
    public static function validateEmail($email=null)
    {
        if ($email==null) {
            return self::returnData(false, "Email data not found", $email, true);
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            list($user, $domain) = explode('@', $email);
            if (checkdnsrr($domain, "MX")) {
                // echo "The email address is valid and the domain has an MX record.";
                return self::returnData(true, "The email address is valid and the domain has an MX record.", $email, true);
            } else {
                // echo "The email address is valid, but the domain does not have an MX record.";
                return self::returnData(false, "The email address is valid, but the domain does not have an MX record.", $email, true);
            }
        } else {
            return self::returnData(false, "The email address is not valid.", $email, true);
        }
    }
    // end validate email


    // Send Email function with ReniMail
    public static function sendEmail($userEmail, $subject, $body) {
        $request = new \HTTP_Request2();
        $request->setUrl('https://sandbox.api.reni.tech/reni-mail/v1/sendSingleMail');
        $request->setMethod(\HTTP_Request2::METHOD_POST);
        $request->setHeader(array(
            'Authorization' => 'Bearer reni_test_DJiC55T0OEJsJZPS4L4PSK', // Replace with your actual token
            'Content-Type' => 'application/json'
        ));
        $request->setConfig(array(
            'follow_redirects' => TRUE,
            'ssl_verify_peer' => FALSE,  // Disable SSL verification
            'ssl_verify_host' => FALSE   // Disable host verification
        ));
        $request->setBody(json_encode(array(
            "email" => $userEmail,
            "subject" => $subject,
            "body" => $body,
            "html" => "true"
        )));

        try {
            $response = $request->send();
            if ($response->getStatus() == 200) {
                return self::returnData(true, 'Email sent successfully!');
            } else {
                return self::returnData(false, 'Unexpected HTTP status: ' . $response->getStatus() . ' ' . $response->getReasonPhrase());
            }
        } catch (\HTTP_Request2_Exception $e) {
            return self::returnData(false, 'Error: ' . $e->getMessage());
        }
    } // End send mail


}