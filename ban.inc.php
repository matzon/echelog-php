<?php
  //$banned['127.0.0.1'] = true;

  $ip = '';
  if(isset($_REQUEST['ip'])){
	$ip = $_REQUEST['ip'];
  }

  if(empty($ip)) {
    $ip = $_SERVER['REMOTE_ADDR'];
  }

  if(isset($banned[$ip]) && $banned[$ip] === true) {
    header("HTTP/1.0 404 Not Found");
    die();
  }
?>
