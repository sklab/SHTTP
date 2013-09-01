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

define('CACHE_DIR_PATH', __DIR__.'/keycache/');
define('KEY_LENGTH', 32);    // byte
define('TOKEN_LENGTH', 20);  // byte

// Main procedure.
if (isset($_REQUEST['token'])) {
  getKey($_REQUEST['token']);
} else {
  start();
}
exit;

function getRandomHex($length) {
    return bin2hex(openssl_random_pseudo_bytes($length));
}

function start() {
    if (!file_exists(CACHE_DIR_PATH)) mkdir(CACHE_DIR_PATH);
    
    $requestUrl = 'https://'.$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"];
    $BASE_URL = dirname($requestUrl).'/';
    $token = getRandomHex(TOKEN_LENGTH);
    for (;;) {
        $filePath = CACHE_DIR_PATH.$token.'.txt';
        if (file_exists($filePath)) {
            $token = getRandomHex(TOKEN_LENGTH);
        } else {
            $key = getRandomHex(KEY_LENGTH);
            file_put_contents($filePath, $key);
            break;
        }
    }
    header('Content-type: text/plain');
    echo '{"key":"'.$key.'", "url":"'.$BASE_URL.'client.api?token='.$token.'"}';
}

function getKey($token) {
    if (strlen($token) != TOKEN_LENGTH*2) {
        throw new Exception('Bad request. Token required.');
    }
    $filePath = CACHE_DIR_PATH.$token.'.txt';
    if (file_exists($filePath)) {
        $key = file_get_contents($filePath);
        @unlink($filePath);
        
        $expires = 60*60*24*7; // 7 days
        header('Content-type: text/javascript');
        header("Pragma: public");
        header("Cache-Control: maxage=".$expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
        echo 'SHTTP.commonKey = "'.$key.'";';
    } else {
        header('HTTP/1.1 304 Not Modified');
    }
}

