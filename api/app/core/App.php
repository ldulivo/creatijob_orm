<?php
namespace App\Core;

use App\Core\Request;
use App\Core\Response;

/**
 * Class App
 * Created at: 2023-07-10 16:00:00
 * Author: LDulivo
 * -----
 * Description:
 * This class manages the application's routing system.
 * It handles HTTP requests, defines routes, and executes corresponding handlers.
 * -----
 */

class App {
  // Property to store the routes
  private static $routes = [];
  private static $req;
  private static $res;

  /**
   * Method: start
   * -----
   * Description:
   * Initializes the application by creating instances of Request and Response classes.
   * -----
   */
  public static function start()
  {
    self::$req = new Request();
    self::$res = new Response();
  }

  /**
   * Method: route
   * -----
   * Description:
   * Matches a given request route with a defined route and extracts parameters.
   * 
   * @param string $route The defined route pattern
   * @param string $requestRoute The incoming request route
   * @return array|false The extracted parameters if matched, or false if not
   * -----
   */
  private static function route($route, $requestRoute) {
    $routeParts = explode('/', trim($route, '/'));
    $requestParts = explode('/', trim($requestRoute, '/'));

    if (count($routeParts) !== count($requestParts)) {
        return false;
    }

    $params = [];
    foreach ($routeParts as $index => $part) {
        if (strpos($part, ':') === 0) {
            $paramName = substr($part, 1);
            $params[$paramName] = $requestParts[$index];
        } elseif ($part !== $requestParts[$index]) {
            return false;
        }
    }
    
    return $params;
  }

  /**
   * Method: get
   * -----
   * Description:
   * Defines a GET route with a handler and an optional middleware.
   * 
   * @param string $route The route to handle
   * @param callable $handler The function to handle the request
   * @param callable|null $middleware Optional middleware to execute before the handler
   * -----
   */
  public static function get($route, callable $handler, callable $middleware = null)
  {
    self::$routes['GET'][$route] = ['handler' => $handler, 'middleware' => $middleware];
  }

  /**
   * Method: post
   * -----
   * Description:
   * Defines a POST route with a handler and an optional middleware.
   * 
   * @param string $route The route to handle
   * @param callable $handler The function to handle the request
   * @param callable|null $middleware Optional middleware to execute before the handler
   * -----
   */
  public static function post($route, callable $handler, callable $middleware = null)
  {
    self::$routes['POST'][$route] = ['handler' => $handler, 'middleware' => $middleware];
  }

  /**
   * Method: put
   * -----
   * Description:
   * Defines a PUT route with a handler and an optional middleware.
   * 
   * @param string $route The route to handle
   * @param callable $handler The function to handle the request
   * @param callable|null $middleware Optional middleware to execute before the handler
   * -----
   */
  public static function put($route, callable $handler, callable $middleware = null)
  {
    self::$routes['PUT'][$route] = ['handler' => $handler, 'middleware' => $middleware];
  }

  /**
   * Method: del
   * -----
   * Description:
   * Defines a DELETE route with a handler and an optional middleware.
   * 
   * @param string $route The route to handle
   * @param callable $handler The function to handle the request
   * @param callable|null $middleware Optional middleware to execute before the handler
   * -----
   */
  public static function del($route, callable $handler, callable $middleware = null)
  {
    self::$routes['DELETE'][$route] = ['handler' => $handler, 'middleware' => $middleware];
  }

  /**
   * Method: run
   * -----
   * Description:
   * Executes the corresponding handler for the matched route.
   * If no route matches, returns a 404 response.
   * -----
   */
  public static function run()
  {
      $requestRoute = self::$req->path;
      $method = self::$req->method;

      if (isset(self::$routes[$method])) {
          foreach (self::$routes[$method] as $route => $routeConfig) {
              $params = self::route($route, $requestRoute);

              if ($params !== false) {
                  // Execute the middleware if defined
                  if ($routeConfig['middleware']) {
                      $runFunction = call_user_func($routeConfig['middleware'], self::$req, self::$res);
                      if ($runFunction === false) {
                          return;
                      }
                  }

                  // Pass captured parameters to the request
                  self::$req->params = $params;

                  // Execute the route handler
                  call_user_func($routeConfig['handler'], self::$req, self::$res);
                  return;
              }
          }
      }

      // If no route is found, return 404
      self::$res::jsonNotFound();
  }
}
?>