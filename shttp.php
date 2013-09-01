<?php
/**
 * SHTTP  - Secure HTTP communication suite -
 *
 * Copyright 2013, sklab
 *
 * Licensed under New BSD License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2013 sklab 
 * @link          https://github.com/sklab/SHTTP SHTTP Project
 * @since         SHTTP v 1.0.0
 * @license       New BSD License
 */

class SHTTP
{
    /**
     * init
     * @param url URL of open SHTTP CA
     * @return json (key, url)
     * Get JSON encoded string from open SHTTP CA
     * and converts it into a PHP variable.
     */
    public static function init($url) {
        return json_decode(file_get_contents($url));
    }

    /**
     * encrypt
     * @param commonKey Common key
     * @param plaintext plaintext
     * @return iv(hex) + ciphertext(base64)
     * Creates a cipher text compatible with AES (Rijndael block size = 128 and key size = 256)
     * only suitable for encoded input that never ends with value 00h (because of default zero padding)
     * plaintext must be UTF-8.
     */
    public static function encrypt($commonKey, $plaintext) {
        $key = hex2bin($commonKey);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_DEV_RANDOM);
        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext, MCRYPT_MODE_CBC, $iv);
        $encodetext = base64_encode($ciphertext);
        $iv = bin2hex($iv);
        return $iv.$encodetext;
    }

    /**
     * decrypt
     * @param commonKey Common key
     * @param encrypted iv(hex) + ciphertext(base64)
     * @return plaintext
     * Decrypt a cipher text compatible with AES (Rijndael block size = 128 and key size = 256)
     */
    public static function decrypt($commonKey, $encrypted) {
        if (strlen($encrypted) == 0) return $encrypted;
        $key = hex2bin($commonKey);
        $iv = hex2bin(substr($encrypted, 0, 32));
        $ciphertext = hex2bin(substr($encrypted, 32));
        $plaintext = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext, MCRYPT_MODE_CBC, $iv);
        return trim($plaintext);
    }

    /**
     * getRandomString
     * @param length return string length
     * @param isPassword use password chars
     * @return ramdom string
     * Generate ramdom string.
     */
    public static function getRandomString($length = 32, $isPassword = false) {
        $chars = '0123456789abcdefghijklmnopqrstrvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($isPassword) $chars = '0123456789abcdefghijkmnpqrstrvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        $result = "";
        for ($i = 0; $i < $length; $i++) {
            $result .= substr($chars, rand(0, strlen($chars) - 1), 1);
        }
        return $result;
    }
}
