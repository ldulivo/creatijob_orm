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
   *
   * @param string|null $secretKey Optional custom secret key.
   * @param int|null $expirationTime Optional custom expiration time in seconds.
   */
  public function __construct(?string $secretKey = null, ?int $expirationTime = null)
  {
    $this->SECRET_KEY = $secretKey ?? (defined('Config\\SECRET_KEY') ? Config\SECRET_KEY : '');
    $this->TOKEN_EXPIRATION_TIME = $expirationTime ?? (defined('Config\\TOKEN_EXPIRATION_TIME') ? Config\TOKEN_EXPIRATION_TIME : 3600);

    if (empty($this->SECRET_KEY)) {
      throw new \RuntimeException("JWT Error: SECRET_KEY is not defined or is empty in configuration.");
    }
  }

  /**
   * Generates a JWT token for the given username and role.
   *
   * @param string $username The username to include in the token payload.
   * @param string $role The user role (default: 'user').
   * @param int|string|null $id Optional user ID (standard 'sub' claim).
   * @param array $customClaims Optional additional non-sensitive claims.
   * @param int|null $customTtl Optional custom expiration time in seconds.
   * @return string The generated JWT token.
   */
  public function Get(string $username, string $role = 'user', int|string|null $id = null, array $customClaims = [], ?int $customTtl = null): string
  {
    $issuedAt = time();
    $ttl = $customTtl ?? $this->TOKEN_EXPIRATION_TIME;
    $expirationTime = $issuedAt + $ttl;

    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

    $payloadData = [
      'username' => $username,
      'role' => $role,
      'iat' => $issuedAt,
      'exp' => $expirationTime
    ];

    if ($id !== null) {
      $payloadData['id'] = $id;
      $payloadData['sub'] = (string)$id;
    }

    if (!empty($customClaims)) {
      foreach ($customClaims as $key => $value) {
        if (!in_array($key, ['iat', 'exp', 'username', 'role', 'sub'])) {
          $payloadData[$key] = $value;
        }
      }
    }

    $payload = json_encode($payloadData);

    $base64UrlHeader = $this->base64UrlEncode($header);
    $base64UrlPayload = $this->base64UrlEncode($payload);
    $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $this->SECRET_KEY, true);
    $base64UrlSignature = $this->base64UrlEncode($signature);

    return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
  }

  /**
   * Alias for Get() with a more descriptive name.
   */
  public function generate(string $username, string $role = 'user', int|string|null $id = null, array $customClaims = [], ?int $customTtl = null): string
  {
    return $this->Get($username, $role, $id, $customClaims, $customTtl);
  }

  /**
   * Checks if a given JWT token is valid.
   * Validity includes correct signature, supported algorithm, and non-expired payload.
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
   * Verifies the token's signature, algorithm header, and expiration.
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

    $header = json_decode($this->base64UrlDecode($base64UrlHeader), true);
    if (!is_array($header) || ($header['alg'] ?? null) !== 'HS256' || ($header['typ'] ?? null) !== 'JWT') {
      return false;
    }

    $payload = json_decode($this->base64UrlDecode($base64UrlPayload), true);
    if (!is_array($payload) || !isset($payload['exp']) || time() > $payload['exp']) {
      return false;
    }

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

  /**
   * Decodes base64 URL-safe format restoring padding and standard characters.
   *
   * @param string $data The base64 URL-safe encoded string.
   * @return string The decoded raw string.
   */
  private function base64UrlDecode(string $data): string
  {
    $remainder = strlen($data) % 4;
    if ($remainder) {
      $data .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(str_replace(['-', '_'], ['+', '/'], $data)) ?: '';
  }
}

