<?php
namespace CUtils;


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
    public static function returnData ($status= false,$message=null, $data=array(), $exit = false, $httpStatus=200) 
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
}