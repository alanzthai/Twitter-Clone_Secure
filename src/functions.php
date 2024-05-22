<?php
  // Database configuration
  require_once 'dblogin.php';
  $appname = 'Twitter clone';

  /**
   * Connects to MySQL and creates an object to access it
   * @param host
   * @param username
   * @param password
   * @param database
   */
  global $connect;
  $connect = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

  if ($connect->connect_error) die($connect->connect_error);

  /**
   * Receives a database response and converts it into an array
   * @param response: mysql response object
   */
  function turnQueryToArray($response) {
    $rows = $response->num_rows;
    $result = [];
    for ($i = 0; $i < $rows; $i++) {
      array_push($result, $response->fetch_array(MYSQLI_NUM));
    }
    return $result;
  }

  function turnQueryToReverseArray($response) {
    $rows = $response->num_rows;
    $result = [];
    for ($i = 0; $i < $rows; $i++) {
      array_unshift($result, $response->fetch_array(MYSQLI_NUM));
    }
    return $result;
  }

  function extractValuesFromNestedArray($array) {
    $newArray = [];
    foreach ($array as $value) {
      array_push($newArray, $value[0]);
    }
    return $newArray;
  }

  /**
   * Checks if a table already exists and if not create it
   * @param $name: String
   * @param $query: String
   */
  function createTable($db, $name, $query) {
    $db->query("CREATE TABLE $name($query)");
  }

  function createNewUser($db, $username, $password) {
  //  $db->query("INSERT INTO users(uname, pwd) VALUES('$username', '$password');");
    $stmt = $db->prepare("INSERT INTO users(uname, pwd) VALUES(?, ?);");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
  }

  function checkUserAuth($db, $username, $password) {
  //  $response = $db->query("SELECT * FROM users WHERE uname=$username AND pwd=$password;");
    $stmt = $db->prepare("SELECT * FROM users WHERE uname=$username AND pwd=$password;");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $response = $stmt->get_result();
    return $response;
  }



  // FOLLOW TABLE FUNCTIONS
  /**
   * Add a follow relation into the followers table
   * @param db: Database connection object
   * @param follower: Number (user's id)
   * @param followed: Number (user's id)
   */
  function followUser($db, $follower, $followed) {
  //  $db->query("INSERT INTO followers(follower, followed) VALUES('$follower', '$followed');");
    if (is_numeric($follower) && is_numeric($followed)) {
        $stmt = $db->prepare("INSERT INTO followers(follower, followed) VALUES(?, ?)");
        $stmt->bind_param("ii", $follower, $followed);
        $stmt->execute();
    }

  }

  /**
   * Removes a follow relation from the followers table
   * @param db: Database connection object
   * @param follower: Number (user's id)
   * @param followed: Number (user's id)
   */
  function unfollowUser($db, $follower, $followed) {
  //  $db->query("DELETE FROM followers WHERE follower=$follower AND followed=$followed ;");
    if (is_numeric($follower) && is_numeric($followed)) {
        $stmt = $db->prepare("DELETE FROM followers WHERE follower=? AND followed=?");
        $stmt->bind_param("ii", $follower, $followed);
        $stmt->execute();
    }

  }

  function getNumOfFollowers($db, $user) {
  //  $response = $db->query("SELECT * FROM followers WHERE followed=$user ;");
    $stmt = $db->prepare("SELECT * FROM followers WHERE followed=?");
    $stmt->bind_param("i", $user);
    $stmt->execute();
    $response = $stmt->get_result();

    return $response->num_rows;
  }

  /**
   * Returns an array with the id's of the users the current user follows
   */
  function checkCurrentUserFollows($db, $user) {
  //  $response = $db->query("SELECT followed FROM followers WHERE follower=$user ;");
    $stmt = $db->prepare("SELECT followed FROM followers WHERE follower=?");
    $stmt->bind_param("i", $user);
    $stmt->execute();
    $response = $stmt->get_result();
    return turnQueryToArray($response);
  }

  function getUserFollowers($db, $user) {
  //  $response = $db->query("SELECT follower FROM followers WHERE followed=$user ;");
    $stmt = $db->prepare("SELECT follower FROM followers WHERE followed=?");
    $stmt->bind_param("i", $user);
    $stmt->execute();
    $response = $stmt->get_result();
    return turnQueryToArray($response);
  }

  function checkIfUserFollowsUser($db, $user1, $user2) {
  //  $response = $db->query("SELECT * FROM followers WHERE follower=$user1 AND followed=$user2 ;");
    $stmt = $db->prepare("SELECT * FROM followers WHERE follower=? AND followed=?");
    $stmt->bind_param("ii", $user1, $user2);
    $stmt->execute();
    $response = $stmt->get_result();
    return $response->num_rows;
  }

  // LIKES TABLE FUNCTIONS

  /**
   *
   */
  function likeMessage($db, $user, $msg) {
  //  $response = $db->query("INSERT INTO likes(user, message) VALUES('$user', '$msg');");
    // $arrayResponse = turnQueryToArray($response);
    // $jsonResponse = json_encode($arrayResponse);

    if (is_numeric($user) && is_numeric($msg)) {
        $stmt = $db->prepare("INSERT INTO likes(user, message) VALUES(?, ?)");
        $stmt->bind_param("ii", $user, $msg);
        $stmt->execute();
        $response = $stmt->get_result();
        echo $response;
    }
  }

  function unlikeMessage($db, $user, $msg) {
  //  $response = $db->query("DELETE FROM likes WHERE user=$user AND message=$msg ;");

    if (is_numeric($user) && is_numeric($msg)) {
        $stmt = $db->prepare("DELETE FROM likes WHERE user=? AND message=?");
        $stmt->bind_param("ii", $user, $msg);
        $stmt->execute();
        $response = $stmt->get_result();
        echo $response;
    }

  }

  function checkIfMessageHasLike($db, $user, $msg) {
  //  $response = $db->query("SELECT * FROM likes WHERE user=$user AND message=$msg ;");
  //  return $response->num_rows;

    if (is_numeric($user) && is_numeric($msg)) {
        $stmt = $db->prepare("SELECT * FROM likes WHERE user=? AND message=?");
        $stmt->bind_param("ii", $user, $msg);
        $stmt->execute();
        $response = $stmt->get_result();
        return $response->num_rows;
    }


  }

  function getMessageLikes($db, $msg) {
  //  $response = $db->query("SELECT * FROM likes WHERE message=$msg");
  //  return $response->num_rows;

    if (is_numeric($msg)) {
        $stmt = $db->prepare("SELECT * FROM likes WHERE message=?");
        $stmt->bind_param("i", $msg);
        $stmt->execute();
        $response = $stmt->get_result();
        return $response->num_rows;
    }

  }

  function getUserLikes($db, $user) {
  //  $response = $db->query("SELECT message FROM likes WHERE user=$user ;");
  //  $arrayResponse = turnQueryToArray($response);
  //  return extractValuesFromNestedArray($arrayResponse);

    if (is_numeric($user)) {
        $stmt = $db->prepare("SELECT message FROM likes WHERE user=?");
        $stmt->bind_param("i", $user);
        $stmt->execute();
        $response = $stmt->get_result();
        $arrayResponse = turnQueryToArray($response);
        return extractValuesFromNestedArray($arrayResponse);
    }

  }

  function turnLikesArrayToMessages($db, $likes) {
    $response = [];
    foreach($likes as $like) {
     // $content = $db->query("SELECT * FROM messages WHERE id=$like");
        if (is_numeric($like)) {
            $stmt = $db->prepare("SELECT * FROM messages WHERE id=?");
            $stmt->bind_param("i", $like);
            $stmt->execute();
            $content = $stmt->get_result();

            array_push($response, $content->fetch_array(MYSQLI_NUM));
        }
    }
    return $response;
  }

  //  OTHER FUNCTIONS

  function redirect($url) {
    header('Location: ' . $url);
  }


?>