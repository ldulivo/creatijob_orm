# Creatijob ORM API

## Overview

This project is an API designed for the Creatijob ORM system. It is intended for general use in various projects, providing an easy way to start a new project and create routes effortlessly. The API is built using PHP and follows a modular structure to organize configuration, core functionalities, and routes. It is inspired by express.js but is a much smaller project in scope.

The API is located inside the `api` directory.

## Project Structure

- `api/index.php`: The main entry point of the API.
- `api/Config.php`: Contains configuration constants for debugging, tokens, domain, and database.
- `api/app/core/`:
  - `Autoloader.php`: Automatically loads PHP class files based on class names and namespace.
  - `ErrorHandler.php`: Handles errors and exceptions, logs them, and provides user-friendly responses.
  - `Header.php`: Sets HTTP headers for access control, allowed methods, and content type.
  - `Initialize.php`: Initializes core components, configures logging and error handling.
  - `Logger.php`: Logs messages to specified log files.
  - `Main.php`: The main file that sets up paths, loads the autoloader, and initializes the application.
  - `ReadRouteFiles.php`: Loads all route files from the specified directory.
  - `Request.php`: Encapsulates HTTP request details such as path, method, headers, query parameters, and body.
  - `Response.php`: Provides helper methods for sending HTTP responses, including JSON, text, HTML, and handling common status codes.
- `api/app/auth/`:
  - `Jwt.php`: Responsible for creating and verifying JWT tokens using the HS256 algorithm.
- `api/app/utils/`:
  - `UuidV4.php`: Generates UUID version 4 strings.
  - `RandomString.php`: Generates random strings of specified length, with optional special characters.

## Configuration

### `api/Config.php`

This file contains various configuration constants:

- **Debug Mode**
  - `DEBUGMODE`: Indicates if debugging mode is enabled.

- **Token Configuration**
  - `SECRET_KEY`: The secret key used for generating and validating tokens.
  - `TOKEN_EXPIRATION_TIME`: The lifetime of a token in seconds (default: 3600 seconds or 1 hour).

- **Domain Configuration**
  - `DOMAIN_NAME`: The name of the server domain.
  - `DOMAIN_PORT`: The port on which the server is running.

- **Database Configuration**
  - `DB_HOST`: The hostname for the database server.
  - `DB_USERNAME`: The username to connect to the database.
  - `DB_PASSWORD`: The password to connect to the database.
  - `DB_NAME`: The name of the database to connect to.
  - `DB_CHARSET`: The character set used by the database.

## Routes

### Adding and Editing Routes

Users can add and edit routes within the `api/src` directory. Routes should be defined in PHP files inside `api/src/routes`. Here is an example of a routes file for managing items (located in `api/src/routes/items.php`):

Example routes:

- **Create a new item**
  - **POST** `/api/items`
  - Request body:
    ```json
    {
      "name": "Apple",
      "description": "A fresh apple.",
      "price": 0.5,
      "quantity": 50
    }
    ```
  - Response: 
    ```json
    {
      "name": "Apple",
      "description": "A fresh apple.",
      "price": 0.5,
      "quantity": 50
    }
    ```

- **Retrieve all items**
  - **GET** `/api/items`
  - Response:
    ```json
    {
      "items": [
        {
          "id": 1,
          "name": "Apple",
          "description": "A fresh apple.",
          "price": 0.5,
          "quantity": 50
        },
        {
          "id": 2,
          "name": "Banana",
          "description": "A ripe banana.",
          "price": 0.3,
          "quantity": 100
        }
      ]
    }
    ```

- **Retrieve a specific item**
  - **GET** `/api/items/:id`
  - Response:
    ```json
    {
      "item": {
        "id": 1,
        "name": "Apple",
        "description": "A fresh apple.",
        "price": 0.5,
        "quantity": 50
      }
    }
    ```

- **Update an item**
  - **PUT** `/api/items/:id`
  - Request body:
    ```json
    {
      "name": "Apple",
      "description": "A fresh and juicy apple.",
      "price": 0.55,
      "quantity": 45
    }
    ```
  - Response:
    ```json
    {
      "item": {
        "id": 1,
        "name": "Apple",
        "description": "A fresh and juicy apple.",
        "price": 0.55,
        "quantity": 45
      }
    }
    ```

- **Delete an item**
  - **DELETE** `/api/items/:id`
  - Response:
    ```json
    {
      "item": {
        "id": 1,
        "name": "Apple",
        "description": "A fresh apple.",
        "price": 0.5,
        "quantity": 50
      }
    }
    ```

### User Routes Example

Here is an example of a routes file for managing users (located in `api/src/routes/users.php`). This file shows how to create users and retrieve user information, including the use of JWT for authentication.

- **Create a new user**
  - **POST** `/api/users/create`
  - Request body:
    ```json
    {
      "username": "john_doe",
      "password": "secure_password"
    }
    ```
  - Response if user already exists:
    ```json
    {
      "message": "User already exists"
    }
    ```
  - Response if user is created successfully:
    ```json
    {
      "message": "User registered successfully"
    }
    ```

- **Retrieve user information (with JWT authentication)**
  - **POST** `/api/users`
  - Request body:
    ```json
    {
      "username": "john_doe"
    }
    ```
  - Request headers:
    ```
    xtoken: <JWT_TOKEN>
    ```
  - Response:
    ```json
    {
      "username": "john_doe",
      "xtoken": "<JWT_TOKEN>"
    }
    ```
  - If token is expired or invalid:
    ```json
    {
      "message": "Expired Token!"
    }
    ```

### Authentication Routes Example

Here is an example of a routes file for user authentication (located in `api/src/routes/auth.php`). This file shows how to login and generate a JWT token.

- **User login**
  - **POST** `/api/auth/login`
  - Request body:
    ```json
    {
      "username": "john_doe",
      "password": "secure_password"
    }
    ```
  - Response if login is successful (JWT token is generated):
    ```json
    {
      "username": "john_doe",
      "password": "secure_password",
      "token": "<JWT_TOKEN>"
    }
    ```
  - Response if username or password is incorrect:
    ```json
    {
      "message": "This username or password does not exist!"
    }
    ```
