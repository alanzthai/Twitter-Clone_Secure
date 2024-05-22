<?php

  require_once 'functions.php';

   function noHTML($input, $encoding = 'UTF-8') {
        return htmlentities($input, ENT_QUOTES | ENT_HTML5, $encoding);
   }

   function postNewMsg($author, $message, $db) {
    $sanitized = noHTML($message);
    //$db->query("INSERT INTO messages(author, msg_text) VALUES($author, '$sanitized');");
    $stmt = $db->prepare("INSERT INTO messages(author, msg_text) VALUES(?, ?);");
    $stmt->bind_param("ss", $author, $sanitized);
    $stmt->execute();
  }

  function delMsg($messageID, $db) {
   // $db->query("DELETE FROM messages WHERE id=$messageID ;");
    $stmt = $db->prepare("DELETE FROM messages WHERE id=? ;");
    $stmt->bind_param("i", $messageID);
    $stmt->execute();
  }

  function getAllMsg($db) {
    $response = $db->query("SELECT * FROM messages");
    return turnQueryToReverseArray($response);
  }

  function getMsgByUserID($user, $db) {
  //  $response = $db->query("SELECT * FROM messages WHERE author IN (SELECT followed FROM followers WHERE follower=$user) ORDER BY id DESC;");
  //  return turnQueryToArray($response);
    $stmt = $db->prepare("SELECT * FROM messages WHERE author IN (SELECT followed FROM followers WHERE follower=?) ORDER BY id DESC;");
    $stmt->bind_param("i", $user);
    $stmt->execute();
    $res = $stmt->get_result();
    return turnQueryToArray($res);

  }

  /**
   * Returns all messages that includes the text passed in the filter
   * @param database: object
   * @param filter: string
   */
  function filterMsgByText($filter, $db) {
    $response = $db->query("SELECT * FROM messages WHERE msg_text LIKE '%$filter%';");
    return turnQueryToReverseArray($response);
  }

  function filterMsgByUname($author, $db) {
  //  $response = $db->query("SELECT * FROM messages WHERE author=$author ;");
    $stmt = $db->prepare("SELECT * FROM messages WHERE author=? ;");
    $stmt->bind_param("s", $author);
    $stmt->execute();
    $response = $stmt->get_result();
    return turnQueryToReverseArray($response);
  }

  function getAuthorName($db, $author) {
  //  $response = $db->query("SELECT uname FROM users WHERE id=$author;");
    $stmt = $db->prepare("SELECT uname FROM users WHERE id=?;");
    $stmt->bind_param("s", $author);
    $stmt->execute();
    $response = $stmt->get_result();

    return $response->fetch_array(MYSQLI_NUM)[0];
  }

  function updateMsgText($msg, $text, $db) {
  //  $response = $db->query("UPDATE messages SET msg_text='$text' WHERE id=$msg ;");
  //  return $response;
    $stmt = $db->prepare("UPDATE messages SET msg_text = ? WHERE id = ?");
    $stmt->bind_param("si", $text, $msg);
    $stmt->execute([$text, $msg]);
    $response = $stmt->get_result();
    return $response;
  }
?>