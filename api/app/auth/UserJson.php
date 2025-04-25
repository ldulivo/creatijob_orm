<?php

namespace App\Auth;
use Config;

class UserJson
{
  private static $name = "";
  private static $username = "";
  private static $email = "";
  private static $role = "";
  private static $password = "";
  private static string $filePath = API_PATH . '/app/auth/storage/users.json';

  private static function getDataUser($dataUser)
  {
    self::$name = $dataUser['name'];
    self::$username = $dataUser['username'];
    self::$email = $dataUser['email'];
    self::$role = $dataUser['role'];
    self::$password = $dataUser['password'];
  }

  private static function EnsureFileExist()
  {
    if (!file_exists(self::$filePath)) {
      $dir = dirname(self::$filePath);
      if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
      }
      file_put_contents(self::$filePath, '[]');
    }
  }

  public static function getUsers()
  {
    self::EnsureFileExist();
    return json_decode(file_get_contents(self::$filePath), true);
  }

  /**
   * Saves the users.json file
   *
   * @param array $users
   * @return void
   */
  private static function SaveUsers(array $users)
  {
    self::EnsureFileExist();
    return file_put_contents(self::$filePath, json_encode($users));
  }

  /**
   * Finds a user in the users.json file
   *
   * @param [type] $username
   * @return void
   */
  public static function FindUser($username)
  {
    $users = self::getUsers();
    return $users[$username] ?? null;
  }

  /**
   * Adds a new user to the users.json file
   *
   * @param [type] $username
   * @param [type] $password
   * @return void
   */
  public static function AddUser($dataUser)
  {
    self::getDataUser($dataUser);

    $users = self::getUsers();
    if (isset($users[self::$username])) {
      return false;
    }

    $users[self::$username] = [
      'name' => self::$name,
      'email' => self::$email,
      'role' => self::$role,
      'password' => password_hash(self::$password, PASSWORD_DEFAULT),
      'created_at' => date('c'),
      'updated_at' => date('c'),
    ];
    return self::SaveUsers($users);
  }

  /**
   * Compares the password hash with the provided password
   *
   * @param [type] $username
   * @param [type] $password
   * @return void
   */
  public static function ComparePassword($username, $password)
  {
    $user = self::FindUser($username);
    return password_verify($password, $user['password']);
  }

  /**
   * Verifies credentials by comparing the username to the password hash
   *
   * @param [type] $username
   * @param [type] $password
   * @return void
   */
  public static function VerifyCredentials($username, $password)
  {
    return self::FindUser($username) && self::ComparePassword($username, $password);
  }

  public static function UpdateUser($dataUser)
  {
    self::getDataUser($dataUser);

    $users = self::getUsers();
    if (!isset($users[self::$username])) {
      return false;
    }
    $user = $users[self::$username];
    if (self::$name) $user['name'] = self::$name;
    if (self::$email) $user['email'] = self::$email;
    if (self::$role) $user['role'] = self::$role;
    if (self::$password) $user['password'] = password_hash(self::$password, PASSWORD_DEFAULT);
    $user['updated_at'] = date('c');

    $users[self::$username] = $user;
    return self::SaveUsers($users);
  }

  public static function DeleteUser($username)
  {
    $users = self::getUsers();
    if (!isset($users[$username])) {
      return false;
    }
    unset($users[$username]);
    return self::SaveUsers($users);
  }

  // Get role
  public static function GetRole($username)
  {
    $users = self::getUsers();
    if (!isset($users[$username])) {
      return false;
    }
    return $users[$username]['role'];
  }
}

?>