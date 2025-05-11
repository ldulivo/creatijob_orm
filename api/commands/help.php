<?php

echo <<<TXT

🛠️  Creatijob CLI - Available Commands

USAGE:
  php api/creatijob.php [command] [argument]

AVAILABLE COMMANDS:

  make:migration ClassName
    Creates a new migration file.
    - "CreateUsersTable" → Creates the 'users' table
    - "AddCodeToUsersTable" → Adds the 'code' column to 'users'
    - "AddTitleAndContentToPostsTable" → Adds multiple columns

  migrate
    Executes all pending migrations.

  rollback
    Rolls back the last executed migration.

  make:resource ClassName
    Creates a basic RESTful resource with:
      - Model: src/models/ClassNameModel.php
      - Controller: src/controllers/ClassNameController.php
      - Route file: src/routes/class_name.php
      Includes methods: get, getById, create, update, delete

  help
    Displays this help message.

MIGRATION NAMING CONVENTIONS:

- Class names must start with a capital letter.
- Table names must be lowercase (e.g., 'users').
- All migrations are tracked in the 'cj_migrations' table.

EXAMPLES:

  php api/creatijob.php make:migration CreateRolesTable
  php api/creatijob.php make:migration AddEmailToUsersTable
  php api/creatijob.php migrate
  php api/creatijob.php rollback
  php api/creatijob.php make:resource Users

For more information, visit: https://creatijob.com

TXT;
