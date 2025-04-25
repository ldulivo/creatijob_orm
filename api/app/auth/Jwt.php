<?php

namespace App\Auth;
use Config;

/**
 * File: Jwt.php
 * Created on: 2025-04-21
 * Author: Leonardo A. D'Ulivo
 * 
 * Description:
 * This class handles the creation, validation, and decoding of JSON Web Tokens (JWT)
 * using the HS256 algorithm and a secret key defined in the configuration.
 * It does not rely on any external libraries.
 */
class Jwt
{
  /**
   * Secret key used to sign the token.
   * 
   * @var string
   */
  private string $SECRET_KEY;

  /**
   * Token expiration time in seconds.
   * 
   * @var int
   */
  private int $TOKEN_EXPIRATION_TIME;

  /**
   * Constructor: Initializes the secret key and token expiration time from the Config.
   */
  public function __construct()
  {
    $this->SECRET_KEY = Config\SECRET_KEY;
    $this->TOKEN_EXPIRATION_TIME = Config\TOKEN_EXPIRATION_TIME;
  }

  /**
   * Generates a JWT token for the given username.
   *
   * @param string $username The username to include in the token payload.
   * @return string The generated JWT token.
   */
  public function Get(string $username, string $role = 'user'): string
  {
    $issuedAt = time();
    $expirationTime = $issuedAt + $this->TOKEN_EXPIRATION_TIME;

    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode([
      'username' => $username,
      'role' => $role,
      'iat' => $issuedAt,
      'exp' => $expirationTime
    ]);

    $base64UrlHeader = $this->base64UrlEncode($header);
    $base64UrlPayload = $this->base64UrlEncode($payload);
    $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $this->SECRET_KEY, true);
    $base64UrlSignature = $this->base64UrlEncode($signature);

    return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
  }

  /**
   * Checks if a given JWT token is valid.
   * Validity includes correct signature and non-expired payload.
   *
   * @param string $token The JWT token to verify.
   * @return bool True if the token is valid, false otherwise.
   */
  public function isValid(string $token): bool
  {
    return $this->Decode($token) !== false;
  }

  /**
   * Decodes a valid JWT token and returns its payload.
   * Verifies the token's signature and expiration.
   *
   * @param string $token The JWT token to decode.
   * @return array|false The decoded payload array if valid, or false if invalid or expired.
   */
  public function Decode(string $token): array|false
  {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;

    [$base64UrlHeader, $base64UrlPayload, $signatureProvided] = $parts;

    $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $this->SECRET_KEY, true);
    $expectedSignature = $this->base64UrlEncode($signature);

    if (!hash_equals($expectedSignature, $signatureProvided)) return false;

    $payload = json_decode(base64_decode($base64UrlPayload), true);
    if (!isset($payload['exp']) || time() > $payload['exp']) return false;

    return $payload;
  }

  /**
   * Encodes data in base64 URL-safe format (without padding).
   *
   * @param string $data The data to encode.
   * @return string The base64 URL-safe encoded string.
   */
  private function base64UrlEncode(string $data): string
  {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
  }
}
