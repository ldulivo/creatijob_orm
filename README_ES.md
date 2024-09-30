
#### Ejemplo de `README_ES.md` Ajustado:

```markdown
# API de Creatijob ORM

## Descripción General

Este proyecto es una API diseñada para el sistema ORM de Creatijob. Está destinado para uso general en varios proyectos, proporcionando una manera fácil de iniciar un nuevo proyecto y crear rutas sin esfuerzo. La API está construida usando PHP y sigue una estructura modular para organizar la configuración, las funcionalidades principales, y las rutas. Está inspirada en express.js pero es un proyecto mucho más pequeño en alcance.

La API se encuentra dentro del directorio `api`.

## Estructura del Proyecto

- `api/index.php`: El punto de entrada principal de la API.
- `api/Config.php`: Contiene constantes de configuración para depuración, tokens, dominio y base de datos.
- `api/app/core/`:
  - `Autoloader.php`: Carga automáticamente los archivos de clase PHP basados en los nombres de clase y espacios de nombres.
  - `ErrorHandler.php`: Maneja errores y excepciones, los registra y proporciona respuestas amigables para el usuario.
  - `Header.php`: Establece encabezados HTTP para control de acceso, métodos permitidos, y tipo de contenido.
  - `Initialize.php`: Inicializa los componentes principales, configura el registro de errores y la gestión de errores.
  - `Logger.php`: Registra mensajes en archivos de log especificados.
  - `Main.php`: El archivo principal que configura las rutas, carga el autoloader e inicializa la aplicación.
  - `ReadRouteFiles.php`: Carga todos los archivos de rutas desde el directorio especificado.
  - `Request.php`: Encapsula los detalles de la solicitud HTTP, como el path, método, encabezados, parámetros de consulta y cuerpo.
  - `Response.php`: Proporciona métodos auxiliares para enviar respuestas HTTP, incluyendo JSON, texto, HTML y manejo de códigos de estado comunes.
- `api/app/auth/`:
  - `Jwt.php`: Responsable de crear y verificar tokens JWT utilizando el algoritmo HS256.
- `api/app/utils/`:
  - `UuidV4.php`: Genera cadenas UUID versión 4.
  - `RandomString.php`: Genera cadenas aleatorias de longitud especificada, con caracteres especiales opcionales.

## Configuración

### `api/Config.php`

Este archivo contiene varias constantes de configuración:

- **Modo de Depuración**
  - `DEBUGMODE`: Indica si el modo de depuración está habilitado.

- **Configuración de Tokens**
  - `SECRET_KEY`: La clave secreta utilizada para generar y validar tokens.
  - `TOKEN_EXPIRATION_TIME`: El tiempo de vida de un token en segundos (por defecto: 3600 segundos o 1 hora).

- **Configuración del Dominio**
  - `DOMAIN_NAME`: El nombre del dominio del servidor.
  - `DOMAIN_PORT`: El puerto en el que se está ejecutando el servidor.

- **Configuración de la Base de Datos**
  - `DB_HOST`: El nombre del host para el servidor de la base de datos.
  - `DB_USERNAME`: El nombre de usuario para conectar a la base de datos.
  - `DB_PASSWORD`: La contraseña para conectar a la base de datos.
  - `DB_NAME`: El nombre de la base de datos a la que se va a conectar.
  - `DB_CHARSET`: El conjunto de caracteres utilizado por la base de datos.

## Rutas

### Agregar y Editar Rutas

Los usuarios pueden agregar y editar rutas dentro del directorio `api/src`. Las rutas deben definirse en archivos PHP dentro de `api/src/routes`. Aquí hay un ejemplo de un archivo de rutas para gestionar items (ubicado en `api/src/routes/items.php`):

Ejemplo de rutas:

- **Crear un nuevo item**
  - **POST** `/api/items`
  - Cuerpo de la solicitud:
    ```json
    {
      "name": "Apple",
      "description": "Una manzana fresca.",
      "price": 0.5,
      "quantity": 50
    }
    ```
  - Respuesta: 
    ```json
    {
      "name": "Apple",
      "description": "Una manzana fresca.",
      "price": 0.5,
      "quantity": 50
    }
    ```

- **Recuperar todos los items**
  - **GET** `/api/items`
  - Respuesta:
    ```json
    {
      "items": [
        {
          "id": 1,
          "name": "Apple",
          "description": "Una manzana fresca.",
          "price": 0.5,
          "quantity": 50
        },
        {
          "id": 2,
          "name": "Banana",
          "description": "Un plátano maduro.",
          "price": 0.3,
          "quantity": 100
        }
      ]
    }
    ```

- **Recuperar un item específico**
  - **GET** `/api/items/:id`
  - Respuesta:
    ```json
    {
      "item": {
        "id": 1,
        "name": "Apple",
        "description": "Una manzana fresca.",
        "price": 0.5,
        "quantity": 50
      }
    }
    ```

- **Actualizar un item**
  - **PUT** `/api/items/:id`
  - Cuerpo de la solicitud:
    ```json
    {
      "name": "Apple",
      "description": "Una manzana fresca y jugosa.",
      "price": 0.55,
      "quantity": 45
    }
    ```
  - Respuesta:
    ```json
    {
      "item": {
        "id": 1,
        "name": "Apple",
        "description": "Una manzana fresca y jugosa.",
        "price": 0.55,
        "quantity": 45
      }
    }
    ```

- **Eliminar un item**
  - **DELETE** `/api/items/:id`
  - Respuesta:
    ```json
    {
      "item": {
        "id": 1,
        "name": "Apple",
        "description": "Una manzana fresca.",
        "price": 0.5,
        "quantity": 50
      }
    }
    ```

### Ejemplo de Rutas de Usuarios

Aquí hay un ejemplo de un archivo de rutas para gestionar usuarios (ubicado en `api/src/routes/users.php`). Este archivo muestra cómo crear usuarios y recuperar información de usuarios, incluido el uso de JWT para la autenticación.

- **Crear un nuevo usuario**
  - **POST** `/api/users/create`
  - Cuerpo de la solicitud:
    ```json
    {
      "username": "john_doe",
      "password": "secure_password"
    }
    ```
  - Respuesta si el usuario ya existe:
    ```json
    {
      "message": "User already exists"
    }
    ```
  - Respuesta si el usuario se crea con éxito:
    ```json
    {
      "message": "User registered successfully"
    }
    ```

- **Recuperar información del usuario (con autenticación JWT)**
  - **POST** `/api/users`
  - Cuerpo de la solicitud:
    ```json
    {
      "username": "john_doe"
    }
    ```
  - Encabezados de la solicitud:
    ```
    xtoken: <JWT_TOKEN>
    ```
  - Respuesta:
    ```json
    {
      "username": "john_doe",
      "xtoken": "<JWT_TOKEN>"
    }
    ```
  - Si el token ha expirado o es inválido:
    ```json
    {
      "message": "Expired Token!"
    }
    ```

### Ejemplo de Rutas de Autenticación

Aquí hay un ejemplo de un archivo de rutas para la autenticación de usuarios (ubicado en `api/src/routes/auth.php`). Este archivo muestra cómo iniciar sesión y generar un token JWT.

- **Inicio de sesión de usuario**
  - **POST** `/api/auth/login`
  - Cuerpo de la solicitud:
    ```json
    {
      "username": "john_doe",
      "password": "secure_password"
    }
    ```
  - Respuesta si el inicio de sesión tiene éxito (se genera el token JWT):
    ```json
    {
      "username": "john_doe",
      "password": "secure_password",
      "token": "<JWT_TOKEN>"
    }
    ```
  - Respuesta si el nombre de usuario o la contraseña son incorrectos:
    ```json
    {
      "message": "This username or password does not exist!"
    }
    ```

