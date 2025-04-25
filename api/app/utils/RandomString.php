<?php

/**
 * File: RandomString.php
 * Created at: 2023-07-10 16:00:00
 * Author: Leonardo A. D'Ulivo
 * -----
 * Description:
 * This file defines the RandomString class, which is used to generate random strings
 * of a specified length, with an optional inclusion of special characters.
 * -----
 */

namespace App\Utils;

/**
 * Class: RandomString
 * -----
 * Description:
 * The RandomString class provides a static method to generate random strings.
 * It allows the generation of strings composed of alphanumeric characters, with an option
 * to include special characters.
 * -----
 */
class RandomString
{
  /**
   * Method: get
   * -----
   * Description:
   * Generates a random string of the specified length.
   * 
   * @param int $length The length of the random string to be generated.
   * @param bool $includeSpecialChars Optional flag to include special characters in the string (default: false).
   * @return string The generated random string.
   * -----
   */
  public static function get($length, $includeSpecialChars = false)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    if ($includeSpecialChars) {
      $characters .= '!@#$%^&*()-_=+[]{}<>?';
    }
    
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
  }
}
