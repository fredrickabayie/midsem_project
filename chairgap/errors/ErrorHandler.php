<?php
/**
 * Created by PhpStorm.
 * User: fredrickabayie
 * Date: 26/02/2016
 * Time: 14:37
 */

error_reporting(E_ALL);
date_default_timezone_set('UTC');


/**
 *
 *
 * @param $errno
 * @param $errstr
 * @param $file
 * @param $line
 * @param $context
 * @return bool
 * @throws ErrorException
 */
set_error_handler(
    function ($errno, $errstr, $file, $line) {
        if (error_reporting() === 0) {
            return false;
        }
        switch ($errno) {

            case E_WARNING:
            case E_USER_WARNING:
            case E_COMPILE_WARNING:
            case E_RECOVERABLE_ERROR:
                error_log("\n[" . date('l jS F Y h:i:s A') . "]\n[$errno] {$errstr}\nError occurred in " . $file . "\nON LINE " . $line . "\n", 3,
                    "../errors/error_log.txt");
                break;

            case E_NOTICE:
            case E_USER_NOTICE:
                error_log("\n[" . date('l jS F Y h:i:s A') . "]\n[$errno] {$errstr}\nError occurred in " . $file . "\nON LINE " . $line . "\n", 3,
                    "../errors/error_log.txt");
                break;

            case E_PARSE:
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
//                echo "<b>Error:</b> [$errno] $errstr";
                error_log("\n[" . date('l jS F Y h:i:s A') . "]\n[$errno] {$errstr}\nError occurred in " . $file . "\nON LINE " . $line . "\n", 3,
                    "../errors/error_log.txt");
                break;

            default:
                break;
        }
        return true;
    });




//echo "Demonstrate warning and fatal errors";
//$ans=$_GET['val1']+$var2;
//echo "</br>The product is ".$ans;
//echo "This line creates a warning</br>";
//@mysql_connect("localhost","root","root");
//echo "Another warning</br>";
//$x= 1/$var2;
//echo "This line will result in fatal error</br>";
//echo "call to undefined fuctnion". str(1/$var2);
//echo " This line will not print because of earlier error";

//trigger_error('custom error', E_USER_ERROR);

//echo "$rofe";
//
//echo $_GET['get'];

//$connect = PDO::MYSQL_ATTR_SSL_CERT