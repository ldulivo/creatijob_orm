<?php

/**
 * File: Request.php
 * Created at: 2023-07-10 16:00:00
 * Author: LDulivo
 * -----
 * Description: 
 * This file defines the Request class which encapsulates all the details of an HTTP request.
 * It handles request paths, methods, headers, query parameters, and request body.
 * 
 * Details:
 * 1. The constructor initializes the request properties including path, method, content type, hostname, status code, time, query parameters, body, parameters, and headers.
 * 2. The getPath method retrieves and sanitizes the request URI.
 * 3. The getData method processes and retrieves the request body based on the HTTP method and content type.
 * 4. The getHeaders method retrieves all the HTTP request headers.
 * -----
 */

namespace App\Core;

class Request
{
  public string $path;
  public array  $queryParameters;
  public string $method;
  public string $contentType;
  public array  $body;
  public array  $params;
  public string $hostname;
  public string $statusCode;
  public int    $time;
  public array  $headers;

  function __construct()
  {
    $this->path             = trim(rtrim($this->getPath(), '/'));
    $this->method           = $_SERVER['REQUEST_METHOD'];
    $this->contentType      = $_SERVER['CONTENT_TYPE'] ?? '';
    $this->hostname         = $_SERVER['SERVER_NAME'];
    $this->statusCode       = $_SERVER['REDIRECT_STATUS'];
    $this->time             = $_SERVER['REQUEST_TIME'];
    $this->queryParameters  = $_GET;
    $this->body             = $this->getData($this->method);
    $this->params           = [];
    $this->headers         = $this->getHeaders();
  }

  private function getPath()
  {
    $requestsUri = $_SERVER['REQUEST_URI'];
    return parse_url(filter_var($requestsUri, FILTER_SANITIZE_URL))['path'];
  }

  private function getData($method)
  {
    switch ($method) {
      case 'GET':
      case 'POST':
        if (strpos($this->contentType, 'application/json') !== false) {
          $data = json_decode(file_get_contents('php://input'), true);
        } else if (strpos($this->contentType, 'application/x-www-form-urlencoded') !== false) {
          parse_str(file_get_contents('php://input'), $data);
        } else {
          $data = $_POST;
        }
        break;

      case 'PUT':
      case 'DELETE':
        $this->contentType = $_SERVER['CONTENT_TYPE'];
        if (strpos($this->contentType, 'application/json') !== false) {
          $data = json_decode(file_get_contents('php://input'), true);
        } else if (strpos($this->contentType, 'application/x-www-form-urlencoded') !== false) {
          parse_str(file_get_contents('php://input'), $data);
        } else {
          $data = file_get_contents('php://input');
        }
        break;

      default:
        echo 'HTTP method not supported';
    }

    return $data;
  }

  private function getHeaders()
  {
    if (function_exists('getallheaders')) {
      return getallheaders();
    }

    $headers = [];
    foreach ($_SERVER as $key => $value) {
      if (strpos($key, 'HTTP_') === 0) {
        $header = str_replace('_', '-', strtolower(substr($key, 5)));
        $headers[$header] = $value;
      }
    }
    return $headers;
  }
}
