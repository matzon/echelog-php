<?php
  setcookie('ddg', 'true', time()+31556926);
  $back = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/'; 
  header('Location: '.$back);
  die();
?>
