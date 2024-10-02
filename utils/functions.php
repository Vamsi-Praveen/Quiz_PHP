<?php
    $cypherMethod = 'AES-256-CBC';
    $key = "vamsiramyagunaganesh12345@#$%^&*";
    $iv = "!@#$%^&*()_+)(*&";

    function encrypt_data($data){
        global $cypherMethod,$key,$iv;
        $dataToEncrypt = $data;
        $encryptedData = openssl_encrypt($dataToEncrypt, $cypherMethod, $key, $options=0, $iv);
        return base64_encode($encryptedData);
    }
    
    function decrypt_data($en_data){
        global $cypherMethod,$key,$iv;
        $dataToDecrypt = base64_decode($en_data);
        $decryptedData = openssl_decrypt($dataToDecrypt, $cypherMethod, $key, $options=0, $iv);
        return $decryptedData;
    } 

    function getCurrentUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];
        return urlencode($protocol . "://" . $host . $uri);
    }

   
?>