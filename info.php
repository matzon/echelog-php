<?php
	echo "<pre>";

	$memcache = new Memcache;
	$memcache->connect('localhost', 11211) or die ("Could not connect");
	
	$version = $memcache->getVersion();
	echo "Server's version: ".$version."<br/>\n";
	
	$stats = $memcache->getExtendedStats();
  print_r($stats);
?>