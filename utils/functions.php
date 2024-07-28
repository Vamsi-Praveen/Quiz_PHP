<?php
    $cypherMethod = 'AES-256-CBC';
    $key = "vamsiramyagunaganesh12345@#$%^&*";
    $iv = "!@#$%^&*()_+)(*&";

    function encrypt_data($data){
        global $cypherMethod,$key,$iv;
        $dataToEncrypt = $data;
        $encryptedData = openssl_encrypt($dataToEncrypt, $cypherMethod, $key, $options=0, $iv);
        return $encryptedData;
    }
    
    function decrypt_data($en_data){
        global $cypherMethod,$key,$iv;
        $dataToDecrypt = $en_data;
        $decryptedData = openssl_decrypt($dataToDecrypt, $cypherMethod, $key, $options=0, $iv);
        return $decryptedData;
    }

   
?>