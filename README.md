# Creatijob ORM API

## Overview

The Creatijob ORM API is a lightweight PHP framework designed to manage HTTP requests and route handling, making it easy to start new projects and create routes effortlessly. Inspired by express.js, this project offers a modular structure to organize configurations, core functionalities, and routes. It includes built-in support for JWT authentication, UUID generation, and random string generation.

The API is located inside the `api` directory.

## Project Structure

- `api/index.php`: The main entry point of the API.
- `api/Config.php`: Contains configuration constants for debugging, tokens, CORS, and database.
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
- `api/app/middleware/`:
  - Contains reusable middleware scripts to be applied globally or per route (e.g., authentication, logging, etc.)

## Configuration

### `api/Config.php`

This file contains various configuration constants used throughout the application.

#### 🔍 Debug Configuration
- `DEBUGMODE`: Enables or disables debug mode (for error output and logging).

#### 🧪 Development Mode
- `DEVELOPMENT_MODE`: Enables development-specific features or behaviors.

#### 🔐 Token Configuration
- `SECRET_KEY`: Secret used to sign and verify JWT tokens.
- `TOKEN_EXPIRATION_TIME`: Token lifetime in seconds (default: 3600 seconds = 1 hour).

#### 🌐 CORS (Cross-Origin Resource Sharing)
- `ALLOWED_ORIGINS`: List of origins allowed to access the API.
- `ALLOW_CREDENTIALS`: Enables credentialed requests (e.g., cookies, authorization headers).
- `ALLOWED_METHODS`: HTTP methods allowed from the client (e.g., GET, POST).
- `ALLOWED_HEADERS`: List of allowed headers from requests.
- `MAX_AGE`: How long the results of a preflight request can be cached (in seconds).

#### 🗄️ Database Configuration
- `DB_HOST`: Database server address.
- `DB_USERNAME`: Username used to connect to the database.
- `DB_PASSWORD`: Password used to connect to the database.
- `DB_NAME`: Name of the database in use.
- `DB_CHARSET`: Character set used by the database.

## 🚀 Quick Start Example (Without Database)

This section demonstrates how to quickly set up simple routes in the Creatijob ORM API using static data, and how to interact with them using vanilla JavaScript. These examples assume no database connection is required.

---

### 📂 Route Files and Namespace

All routes are placed in `api/src/routes`. Each route file must include:

```php
use App\Core\App;
```

This gives access to the singleton app instance to define routes such as `get`, `post`, `put`, and `del`.

---

### 📄 1. `GET /api/items` – Get All Items

```php
App::get('/api/items', function ($req, $res) {
  $data = [
    'items' => [
      [
        'id' => 1,
        'name' => 'Apple',
        'description' => 'A fresh apple.',
        'price' => 0.5,
        'quantity' => 50,
      ],
      [
        'id' => 2,
        'name' => 'Banana',
        'description' => 'A ripe banana.',
        'price' => 0.3,
        'quantity' => 100,
      ],
    ],
  ];
  $res::json($data, 200);
});
```

```js
function getItems() {
  fetch("http://orm.test/api/items")
    .then(res => res.json())
    .then(data => console.log(data));
}

getItems();
```

---

### 📄 2. `GET /api/items/:id` – Get Item by ID

```php
App::get('/api/items/:id', function ($req, $res) {
  $data = [
    'item' => [
      'id' => $req->params['id'],
      'name' => 'Apple',
      'description' => 'A fresh apple.',
      'price' => 0.5,
      'quantity' => 50,
    ],
  ];
  $res::json($data, 200);
});
```

```js
function getItem() {
  fetch("http://orm.test/api/items/1")
    .then(res => res.json())
    .then(data => console.log(data));
}

getItem();
```

---

### 📄 3. `POST /api/items` – Add New Item

```php
App::post('/api/items', function ($req, $res) {
  $data = [
    'name' => $req->body['name'],
    'description' => $req->body['description'],
    'price' => $req->body['price'],
    'quantity' => $req->body['quantity'],
  ];
  $res::json($data, 201);
});
```

```js
function addItem() {
  const data = {
    name: "Apple",
    description: "A fresh apple",
    price: 10,
    quantity: 1
  };

  fetch("http://orm.test/api/items", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(data)
  })
  .then(res => {
    if (!res.ok) throw new Error("Error en la respuesta del servidor");
    return res.json();
  })
  .then(data => console.log(data));
}

addItem();
```


---

### 📄 4. `PUT /api/items/:id` – Update Existing Item

```php
App::put('/api/items/:id', function ($req, $res) {
  $data = [
    'item' => [
      'id' => $req->params['id'],
      'name' => $req->body['name'],
      'description' => $req->body['description'],
      'price' => $req->body['price'],
      'quantity' => $req->body['quantity'],
    ],
  ];
  $res::json($data, 200);
});
```

```js
function updateItem() {
  const data = {
    name: "Apple",
    description: "A fresh apple",
    price: 10,
    quantity: 1
  };

  fetch("http://orm.test/api/items/1", {
    method: "PUT",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(data)
  })
  .then(res => {
    if (!res.ok) throw new Error("Error en la respuesta del servidor");
    return res.json();
  })
  .then(data => console.log(data));
}

updateItem();
```

---

### 📄 5. `DELETE /api/items/:id` – Remove Item

```php
App::del('/api/items/:id', function ($req, $res) {
  $data = [
    'item' => [
      'id' => $req->params['id'],
      'name' => 'Apple',
      'description' => 'A fresh apple.',
      'price' => 0.5,
      'quantity' => 50,
    ],
  ];
  $res::json($data, 204);
});
```

```js
function deleteItem() {
  fetch("http://orm.test/api/items/1", {
    method: "DELETE"
  })
  .then(res => {
    if (!res.ok) throw new Error("Error en la respuesta del servidor");
    return res.json();
  })
  .then(data => console.log(data));
}

deleteItem();
```

---

## ⚙️ Internal Classes and Utilities

The following classes are included in the framework to handle common functionality like token authentication, user storage, middleware protection, and shared request context.

---

### 🔐 `App\Auth\Jwt`

A lightweight JWT (JSON Web Token) implementation without external libraries.

**Main Methods:**

- `__construct()`: Initializes with secret and expiration time from config.
- `Get(string $username, string $role = 'user'): string`  
  → Generates a signed JWT token.
- `isValid(string $token): bool`  
  → Returns true if the token is valid and not expired.
- `Decode(string $token): array|false`  
  → Returns decoded payload if valid, or false.

---

### 👤 `App\Auth\UserJson`

Handles user storage using a local `users.json` file. Useful for demos or apps without a DB.

**Main Methods:**

- `AddUser(array $userData): bool`  
  → Registers a new user (with hashed password).
- `FindUser(string $username): ?array`  
  → Returns the user info or null.
- `UpdateUser(array $userData): bool`  
  → Updates user info (if exists).
- `DeleteUser(string $username): bool`  
  → Removes a user.
- `VerifyCredentials(string $username, string $password): bool`  
  → Checks if credentials match.
- `GetRole(string $username): string|false`  
  → Returns the role of a user.

---

### 🧠 `App\Core\Context`

Stores data that persists during the request lifecycle. Especially useful to share authentication payloads across middleware and handlers.

**Main Methods:**

- `Context::set(string $key, mixed $value)`  
- `Context::get(string $key): mixed|null`  
- `Context::has(string $key): bool`  
- `Context::all(): array`  
- `Context::clear(): void`

---

### 🛡️ `App\Middleware\AuthMiddleware`

Validates JWT token and sets the decoded payload in `Context::set('auth', $payload)`.

**Key Logic:**

- Accepts `Authorization`, `xtoken`, or `x-token` header.
- Extracts Bearer token if present.
- Decodes using `Jwt::Decode()`.
- Returns 401/400 if missing or invalid.

---

### 🧰 `App\Middleware\PassFollowingRoles`

Same as `AuthMiddleware` but checks if the role from the decoded JWT is in a list of allowed roles.

```php
PassFollowingRoles::Handle($req, $res, ['admin', 'editor']);
```

Returns 403 if role not allowed. Sets decoded token in context like `AuthMiddleware`.



---

## 👤 User Management Example (Without Database)

This example shows how to create users using static storage (`users.json`) through the `App\Auth\UserJson` class.

---

### 📄 `POST /api/users/create` – Register New User

Create a file `api/src/routes/users.php` and define the route as follows:

```php
use App\Core\App;
use App\Auth\UserJson;

App::post('/api/users/create', function ($req, $res) {
  $dataUser['name'] = $req->body['name'];
  $dataUser['username'] = $req->body['username'];
  $dataUser['email'] = $req->body['email'];
  $dataUser['role'] = $req->body['role'];
  $dataUser['password'] = $req->body['password'];

  $user = UserJson::AddUser($dataUser);

  if (!$user) {
    return $res::json(["message" => "User already exists"], 400);
  }

  return $res::json(["message" => "User registered successfully"], 200);
});
```

---

### 💻 JavaScript – Create a User from the Frontend

```js
function addUser() {
  const data = {
    name: "Leonardo Dulivo",
    username: "leonardo",
    email: "leonardo@test.com",
    role: "admin",
    password: "123456"
  };

  fetch("http://orm.test/api/users/create", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(data)
  })
  .then(response => {
    if (!response.ok) throw new Error("Error en la respuesta del servidor");
    return response.json();
  })
  .then(data => console.log(data))
  .catch(err => console.error(err));
}

addUser();
```

---

### 🔐 `POST /api/auth/login` – Authenticate and Get Token

This route checks the provided credentials and returns a valid JWT token using the `App\Auth\Jwt` class.

Create the file `api/src/routes/auth.php`:

```php
use App\Core\App;
use App\Auth\Jwt;
use App\Auth\UserJson;

App::post('/api/auth/login', function ($req, $res) {
  $username = $req->body['username'];
  $password = $req->body['password'];

  if (UserJson::VerifyCredentials($username, $password)) {
    $token = new Jwt();
  }

  isset($token)
    ? $res::json([
        'username' => $username,
        'token' => $token->Get($username, UserJson::GetRole($username))
      ], 200)
    : $res::json([
        'message' => 'This username or password does not exist!'
      ], 200);
});
```

---

### 💻 JavaScript – Authenticate a User and Receive Token

```js
function loginUser() {
  const data = {
    username: "leonardo",
    password: "123456"
  };

  fetch("http://orm.test/api/auth/login", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(data)
  })
  .then(response => {
    if (!response.ok) throw new Error("Error en la respuesta del servidor");
    return response.json();
  })
  .then(data => console.log(data))
  .catch(err => console.error(err));
}

loginUser();
```

The response includes a token:

```json
{
  "username": "leonardo",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

You can now use this token to authorize requests to protected routes via headers:

```
Authorization: Bearer <JWT_TOKEN>
```



## 🛠️ Protected User Routes

### ✏️ `PUT /api/users/:username` – Update a User (Protected by Middleware)

This route allows users to update their own data, or administrators to update any user. It uses:

- `AuthMiddleware`: to validate the JWT token and extract the payload.
- `PassFollowingRoles`: to ensure only users with the role `admin` can modify others.

#### 📄 Route Definition

```php
use App\Core\App;
use App\Auth\UserJson;
use App\Core\Context;
use App\Middleware\AuthMiddleware;
use App\Middleware\PassFollowingRoles;

App::put('/api/users/:username', function ($req, $res) {
  $dataUser['username'] = $req->params['username'];
  $dataUser['name'] = $req->body['name'];
  $dataUser['email'] = $req->body['email'];
  $dataUser['role'] = $req->body['role'];
  $dataUser['password'] = $req->body['password'];

  $auth = Context::get('auth');

  if ($auth['username'] !== $dataUser['username'] && $auth['role'] !== 'admin') {
    return $res::json(["message" => "You are not authorized to update this user"], 403);
  }

  $user = UserJson::UpdateUser($dataUser);

  if (!$user) {
    return $res::json(["message" => "User not found"], 400);
  }

  return $res::json([
    "message" => "User updated successfully",
    "user" => UserJson::FindUser($dataUser['username']),
    "auth" => $auth
  ], 200);
},
function ($req, $res) {
  $auth = AuthMiddleware::Handle($req, $res);
  if (!$auth) return false;
  return PassFollowingRoles::Handle($req, $res, ['admin']);
});
```

#### 💻 JavaScript – Update a User via Fetch with Token

```js
function putUser() {
  const data = {
    name: "Leonardo Ariel Dulivo",
    email: "leonardo@test.com",
    role: "admin",
    password: "123456"
  };

  const token = "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."

  fetch("http://orm.test/api/users/leonardo", {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
      "Authorization": token
    },
    body: JSON.stringify(data)
  })
  .then(response => {
    if (!response.ok) throw new Error("Error en la respuesta del servidor");
    return response.json();
  })
  .then(data => console.log(data))
  .catch(err => console.error(err));
}

putUser();
```

---

### ❌ `DELETE /api/users/:username` – Delete a User

This route allows deleting a user by their username. Currently, no middleware is applied, so use with caution. In production, it’s recommended to protect this route (e.g., with `AuthMiddleware` and `PassFollowingRoles`).

#### 📄 Route Definition

```php
use App\Core\App;
use App\Auth\UserJson;

App::del('/api/users/:username', function ($req, $res) {
  $username = $req->params['username'];
  $user = UserJson::DeleteUser($username);

  if (!$user) {
    return $res::json(["message" => "User not found"], 400);
  }

  return $res::json(["message" => "User deleted successfully"], 200);
});
```

#### 💻 JavaScript – Delete a User

```js
function deleteUser() {
  fetch("http://orm.test/api/users/leonardo", {
    method: "DELETE"
  })
  .then(response => {
    if (!response.ok) throw new Error("Error en la respuesta del servidor");
    return response.json();
  })
  .then(data => console.log(data))
  .catch(err => console.error(err));
}

deleteUser();
```

---

✅ The user will be removed from the `users.json` storage file. You can add authentication middleware to protect this route as needed.
