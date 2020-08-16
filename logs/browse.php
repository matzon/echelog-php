<?php
require_once("../ban.inc.php");
require_once("../channels.inc.php");
require_once("../utf8.php");
require_once("lib.php");

date_default_timezone_set('Europe/Copenhagen');

define ('LOG_ROOT', "http://" . $_SERVER['SERVER_NAME'] . "/logs/");

$parameters = processURI();
$channel    = $parameters[1];
$date 	    = $parameters[2];

// channel to read logs from
if(!validate_channel($channel)) {
  header("HTTP/1.0 404 Not Found");
  die();
}

if(!$date) {
  redirect_date(time(), $channel);
} else if (!is_numeric($date)) {
  redirect_date(time(), $channel);
} else if ($date <= 1072911600) {
  header("HTTP/1.0 404 Not Found");
  die();
}

// check for archived site
if(!empty($channels[$channel]['archived'])) {
  if($date > intval($channels[$channel]['archived'])) {
    redirect_date($channels[$channel]['archived'], $channel);
  }
}

// reddit
header('X-Narwhal: Bacon');

$today 				= getdate($date);
$today_normalized	= mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']);

if($date != $today_normalized) {
  redirect_date($today_normalized, $channel);
}

ob_start();

$memcache 			= new Memcache;
$memcachekey 		= "$channel-$today_normalized-body";
$memcache_expire 	= 172800;
$flush = (isset($_REQUEST['flush']) && ($_REQUEST['flush'] == 'true'));
$memcache_connected = $memcache->connect('localhost', 11211);

$year      = date("Y", $today_normalized);
$month     = date("F", $today_normalized);
$day       = date("j", $today_normalized);
$days      = date("t", $today_normalized);
$previous  = strtotime("-$day days", $today_normalized);
$previous  = strtotime("-" . date("t", $previous)+1 . " days", $previous);
$next      = strtotime("+" . ($days - $day)+1 . " days", $today_normalized);

// if selected date is TODAY - then we expire every minute
$now = getdate();
if($today_normalized == mktime(0, 0, 0, $now['mon'], $now['mday'], $now['year'])) {
  $memcache_expire = 180;
}

render_header($channel, $month, $day, $year);
render_search($channels[$channel]['google_search'], $channel);
echo "<a name='top'>\n";
render_menu($date, $days, $next, $previous, $month, $day, $year, $channels[$channel]['network'], $channels[$channel]['name']);
render_top_ad($channels[$channel]['google_ads']);
if(isset($channels[$channel]['archived'])) {
  render_archived($channels[$channel]['archived'], $date);
}

echo "<div class='body' style='margin:2 0 0 0em; '>\n";
echo "\n<div align='center'><a href='javascript:void(toggle())'>Toggle Join/Part</a> | <a href='#bottom'>bottom</a></div>";
echo "<pre>";

// get actual logged lines from cache (or not if flushing)
$loglines = null;
if($memcache_connected && !$flush) {
	$result = $memcache->get($memcachekey);
	if(!empty($result)) {
		$expire = $result['expire'] - time();
		echo "<!-- serving cached copy, expires in $expire seconds -->";
		$loglines = $result['data'];
	}
}

// get from filesystem instead
if(empty($loglines)) {
	$log = @file("logs/" . 
		$channels[$channel]['network'] . '/' . 
		$channels[$channel]['name'] . "/" . 
		$channels[$channel]['name'] . "." . 
		date("Y-m-d", $date) . ".log");
		
	if($log) {
	  foreach($log as $key => $value) {
		if(strlen($value) > 0 && strpos($value, '-= THIS MESSAGE NOT LOGGED =-') === false ) {
		  $loglines .= formatIRCLine($value);
		}
	  }
	} else {
	  $loglines = "no log file for date";
	}
	
	// update cache
	if($memcache_connected) {
		$memcache->add($memcachekey, array('data' => $loglines, 'expire' => time() + $memcache_expire), MEMCACHE_COMPRESSED, $memcache_expire);
	}
	
	echo "<!-- serving recent log -->";
}
echo $loglines;

if($memcache_connected) {
  if($flush) {
    $memcache->replace($memcachekey, array('data' => $loglines, 'expire' => time() + $memcache_expire), MEMCACHE_COMPRESSED, $memcache_expire);
	echo "<!-- cache flushed -->";
  }
}
echo "</pre>";
echo "\n<div align='center'><a href='#top'>top</a><a name='bottom'></a></div>";
render_bottom_ad($channels[$channel]['google_ads']);
render_menu($date, $days, $next, $previous, $month, $day, $year, $channels[$channel]['network'], $channels[$channel]['name']);
//echo "</pre>\n";
echo "</div>\n";


echo "</body>\n";
echo "</html>";

ob_end_flush();
?>
