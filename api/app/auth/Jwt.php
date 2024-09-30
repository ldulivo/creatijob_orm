<?php

namespace App\Auth;
use Config;

/**
 * File: Jwt.php
 * Created at: 2023-07-10 16:00:00
 * Author: Leonardo A. D'Ulivo
 * -----
 * Description:
 * This file defines the Jwt class, which is responsible for creating and verifying JWT tokens.
 * The class utilizes configurations defined in Config for secret key and token expiration time.
 * The tokens are encoded using the HS256 algorithm.
 * -----
 */

/**
 * Class: Jwt
 * -----
 * Description:
 * The Jwt class provides functionality to generate and verify JSON Web Tokens (JWT).
 * It uses configurations for secret key and expiration time from the Config namespace.
 * -----
 */
class Jwt
{
  private string $SECRET_KEY;
  private string $TOKEN_EXPIRATION_TIME;
  
  /**
   * Constructor: __construct
   * -----
   * Description:
   * Initializes the Jwt class by setting the secret key and token expiration time from the Config.
   * -----
   */
  function __construct()
  {
    $this->SECRET_KEY = Config\SECRET_KEY;
    $this->TOKEN_EXPIRATION_TIME = Config\TOKEN_EXPIRATION_TIME;
  }

  /**
   * Method: Get
   * -----
   * Description:
   * Generates a JWT token for a given username.
   * 
   * @param string $username The username to be included in the token payload.
   * @return string The generated JWT token.
   * -----
   */
  public function Get($username)
  {
    $issuedAt = time();
    $expirationTime = $issuedAt + $this->TOKEN_EXPIRATION_TIME; // Expiration time

    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode([
        'username' => $username,
        'iat' => $issuedAt,          // Time at which the token is created
        'exp' => $expirationTime     // Expiration time
    ]);

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->SECRET_KEY, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
  }

  /**
   * Method: Compare
   * -----
   * Description:
   * Verifies a given JWT token to check if it is valid and matches the expected username.
   * 
   * @param string $token The JWT token to be verified.
   * @param string $expectedUsername The expected username to be validated against the token payload.
   * @return bool True if the token is valid and the username matches, False otherwise.
   * -----
   */
  public function Compare($token, $expectedUsername)
  {
    $parts = explode('.', $token);

    if (count($parts) === 3) {
        list($base64UrlHeader, $base64UrlPayload, $signatureProvided) = $parts;

        $payload = json_decode(base64_decode($base64UrlPayload), true);

        // Check if the token has expired
        $currentTime = time();
        if ($payload['exp'] < $currentTime) {
            return false; // Token has expired
        }

        // Verify that the token corresponds to the expected user
        if ($payload['username'] !== $expectedUsername) {
          return false; // User does not match
        }

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->SECRET_KEY, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return hash_equals($base64UrlSignature, $signatureProvided);
    }

    return false;
  }
}
?>