<?php
  require_once 'functions.php';

  ini_set('session.cookie_samesite', 'Lax');

  session_start();

  require_once 'msg_dal.php';
  require_once 'components/navbar.php';
?>