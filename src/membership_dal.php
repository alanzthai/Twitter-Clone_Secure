<?php

  require_once 'functions.php';


  function doLogin($uname, $pwd, $db) {
     $username = $uname;
     // Query the database to retrieve the hashed password for the provided username
     $getPasswordQuery = "SELECT pwd FROM users WHERE uname = '$username';";
     $result = $db->query($getPasswordQuery);

     if ($result && $result->num_rows > 0) {

         // Username found, fetch hashed password
         $row = $result->fetch_assoc();
         $hashedPassword = $row['pwd'];

         if (password_verify($pwd, $hashedPassword)) {
            $dataQuery = "SELECT * FROM users WHERE uname='$username';";
            echo "valid";
            return $db->query($dataQuery);
         }
     }
     $controlQuery = "SELECT * FROM users WHERE 1 = 0;";
     return $db->query($controlQuery);
  }

  function registerNewUser($uname, $pwd, $db) {
        $username = $uname;
        $hashed = password_hash($pwd, PASSWORD_DEFAULT);
        // Query the database with the login values to create a new user into the database
        $newUserQuery = "insert into users(uname, pwd) values('$username', '$hashed');";
    $response = $db->query($newUserQuery);
    return $response;
  }

?>