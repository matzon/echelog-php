<?php
  setcookie('ddg', '', time()-3600);
  $back = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
  header('Location: '.$back);
  die();
?>
