<?php

/**
 * File: UuidV4.php
 * Created at: 2023-07-10 16:00:00
 * Author: Leonardo A. D'Ulivo
 * -----
 * Description:
 * This file defines the UuidV4 class, which is used to generate UUID (Universally Unique Identifier)
 * version 4 strings. UUIDv4 are randomly generated and follow the standard format.
 * -----
 */

namespace App\Utils;

/**
 * Class: UuidV4
 * -----
 * Description:
 * The UuidV4 class provides a static method to generate UUID version 4 strings.
 * UUIDv4 are randomly generated strings and follow the standard UUID format.
 * -----
 */

class UuidV4
{
  /**
   * Method: get
   * -----
   * Description:
   * Generates a random UUID version 4 string.
   * 
   * Details:
   * 1. Generates 16 bytes (128 bits) of random or pseudo-random data using `random_bytes`.
   * 2. Sets the version to 0100 (UUIDv4) by modifying the 7th byte.
   * 3. Sets the clock sequence high and reserved bits to 10 by modifying the 9th byte.
   * 4. Converts the binary data to its hexadecimal representation.
   * 5. Formats the hexadecimal data into the standard UUID format (8-4-4-4-12).
   * 
   * @return string The generated UUID v4 string.
   * -----
   */
  public static function get()
  {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    
    $hexData = bin2hex($data);
    
    return sprintf(
        '%s-%s-%s-%s-%s',
        substr($hexData, 0, 8),
        substr($hexData, 8, 4),
        substr($hexData, 12, 4),
        substr($hexData, 16, 4),
        substr($hexData, 20, 12)
    );
  }
}

