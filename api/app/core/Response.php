<?php
namespace App\Core;

/**
 * File: Response.php
 * Created at: 2023-07-10 16:00:00
 * Author: LDulivo
 * -----
 * Description: 
 * This file defines the Response class which provides helper methods for sending HTTP responses.
 * It supports various response types such as JSON, plain text, HTML, and redirects, and handles common HTTP status codes.
 * -----
 */

class Response
{
    /**
     * Method: json
     * -----
     * Description:
     * Sends a JSON response with a specified HTTP status code.
     * 
     * @param mixed $data Data to be encoded as JSON
     * @param int $statusCode HTTP status code (default: 200)
     * -----
     */
    public static function json($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }

    /**
     * Method: login
     * -----
     * Description:
     * Sends a response for a successful login with user data and a token.
     * 
     * @param mixed $data User data
     * @param string $token Authentication token
     * -----
     */
    public static function login($data, $token)
    {
        return self::json([
            "success" => true,
            "message" => "Operation completed successfully",
            "data" => [
                "user" => $data,
                "token" => $token,
            ],
        ]);
    }

    /**
     * Method: loginFailed
     * -----
     * Description:
     * Sends a response for a failed login attempt with error messages.
     * 
     * @param array $errors Error messages
     * -----
     */
    public static function loginFailed($errors)
    {
        return self::json([
            "success" => false,
            "message" => "Credentials are not valid",
            "errors" => $errors,
        ], 401);
    }

    /**
     * Method: text
     * -----
     * Description:
     * Sends a plain text response with a specified HTTP status code.
     * 
     * @param string $data Text data
     * @param int $statusCode HTTP status code (default: 200)
     * -----
     */
    public static function text($data, $statusCode = 200)
    {
        header('Content-Type: text/plain');
        http_response_code($statusCode);
        echo $data;
    }

    /**
     * Method: html
     * -----
     * Description:
     * Sends an HTML response with a specified HTTP status code.
     * 
     * @param string $data HTML data
     * @param int $statusCode HTTP status code (default: 200)
     * -----
     */
    public static function html($data, $statusCode = 200)
    {
        header('Content-Type: text/html');
        http_response_code($statusCode);
        echo $data;
    }

    /**
     * Method: redirect
     * -----
     * Description:
     * Redirects to a given URL with a specified HTTP status code.
     * 
     * @param string $url URL to redirect to
     * @param int $statusCode HTTP status code (default: 302)
     * -----
     */
    public static function redirect($url, $statusCode = 302)
    {
        header('Location: ' . $url, true, $statusCode);
        exit;
    }

    /**
     * Method: notFound
     * -----
     * Description: 
     * Sends a "404 Not Found" response.
     * 
     * @param int $statusCode HTTP status code (default: 404)
     * -----
     */
    public static function notFound($statusCode = 404)
    {
        header("HTTP/1.0 404 Not Found");
        http_response_code($statusCode);
        echo "404 Not Found";
    }

    /**
     * Method: jsonNotFound
     * -----
     * Description: 
     * Sends a "404 Not Found" response in JSON format.
     * 
     * @param string $message Error message (default: "The requested resource was not found.")
     * -----
     */
    public static function jsonNotFound($message = "The requested resource was not found.")
    {
        self::json([
            "success" => false,
            "message" => $message,
        ], 404);
    }

    /**
     * Method: methodNotAllowed
     * -----
     * Description: 
     * Sends a "405 Method Not Allowed" response.
     * 
     * @param string $message Error message (default: "The requested HTTP method is not allowed for this resource.")
     * -----
     */
    public static function methodNotAllowed($message = "The requested HTTP method is not allowed for this resource.")
    {
        self::json([
            "success" => false,
            "message" => $message,
        ], 405);
    }

    /**
     * Method: badRequest
     * -----
     * Description: 
     * Sends a "400 Bad Request" response.
     * 
     * @param string $message Error message (default: "The request could not be understood or was missing required parameters.")
     * -----
     */
    public static function badRequest($message = "The request could not be understood or was missing required parameters.")
    {
        self::json([
            "success" => false,
            "message" => $message,
        ], 400);
    }

    /**
     * Method: unauthorized
     * -----
     * Description: 
     * Sends a "401 Unauthorized" response.
     * 
     * @param string $message Error message (default: "You are not authorized to access this resource.")
     * -----
     */
    public static function unauthorized($message = "You are not authorized to access this resource.")
    {
        self::json([
            "success" => false,
            "message" => $message,
        ], 401);
    }

    /**
     * Method: forbidden
     * -----
     * Description: 
     * Sends a "403 Forbidden" response.
     * 
     * @param string $message Error message (default: "You do not have permission to access this resource.")
     * -----
     */
    public static function forbidden($message = "You do not have permission to access this resource.")
    {
        self::json([
            "success" => false,
            "message" => $message,
        ], 403);
    }

    /**
     * Method: conflict
     * -----
     * Description: 
     * Sends a "409 Conflict" response.
     * 
     * @param string $message Error message (default: "There is a conflict with the current state of the resource.")
     * -----
     */
    public static function conflict($message = "There is a conflict with the current state of the resource.")
    {
        self::json([
            "success" => false,
            "message" => $message,
        ], 409);
    }

    /**
     * Method: internalServerError
     * -----
     * Description: 
     * Sends a "500 Internal Server Error" response.
     * 
     * @param string $message Error message (default: 'Internal Server Error')
     * -----
     */
    public static function internalServerError($message = 'Internal Server Error')
    {
        self::json([
            "success" => false,
            "message" => $message,
        ], 500);
    }

    /**
     * Method: unprocessableEntity
     * -----
     * Description: 
     * Sends a "422 Unprocessable Entity" response.
     * 
     * @param array $errors Array of errors
     * -----
     */
    public static function unprocessableEntity($errors)
    {
        self::json([
            "success" => false,
            "message" => "Unprocessable Entity",
            "errors" => $errors,
        ], 422);
    }

    /**
     * Method: status
     * -----
     * Description: 
     * Sends a response with common status codes and descriptions.
     * 
     * @param int $statusCode HTTP status code
     * @param mixed $data Optional data to include in the JSON response
     * -----
     */
    public static function status($statusCode, $data = null)
    {
        $descriptions = [
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            304 => 'Not Modified',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
            503 => 'Service Unavailable',
        ];

        $description = $descriptions[$statusCode] ?? 'Unknown Status';

        // If data is provided, include it in the JSON response
        if ($data) {
            self::json([
                "status" => $statusCode,
                "description" => $description,
                "data" => $data,
            ], $statusCode);
        } else {
            // If no data, send only the description
            http_response_code($statusCode);
            echo $description;
        }
    }

    /**
     * Method: image
     * -----
     * Description: 
     * Sends an image file as the response.
     * 
     * @param string $filePath Path to the image file
     * @param string $mimeType MIME type of the image (default: 'image/jpeg')
     * @param int $statusCode HTTP status code (default: 200)
     * -----
     */
    public static function image($filePath, $mimeType = 'image/jpeg', $statusCode = 200)
    {
        // Verify that the image file exists
        if (!file_exists($filePath)) {
            self::notFound();
            return;
        }

        // Set HTTP headers for content type
        header('Content-Type: ' . $mimeType);
        http_response_code($statusCode);

        // Read and send the image
        readfile($filePath);
    }
}
?>