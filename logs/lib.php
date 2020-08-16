<?php
function processURI() {
  if(isset($_REQUEST['channel'])) {
    $ldate = (isset($_REQUEST['date'])) ? $_REQUEST['date'] : '';
    return array(null, $_REQUEST['channel'], $ldate);
  }

  $array = explode("/", $_SERVER["REQUEST_URI"]);
  $num = count($array);
  $url_array = array();

  for ($i = 1 ; $i < $num ; $i++) {
   $url_array[$i] = $array[$i];
  }

  return $url_array;
}

function formatIRCLine($line) {
  $date = sscanf($line, "%s %s");
  $content = substr($line, strlen($date[0] . $date[1]) + 2);
  //return "[" . $date[1] . color_code(selective_decode_utf8($content));
  return "<div>[" . $date[1] . color_code($content) . "</div>";
}

function color_code($string) {
  $string = htmlentities(trim($string));

  // check for links
  $string = preg_replace('#(https?:)([^\s]*)#', '<a href="\\1\\2" rel="nofollow" target="_blank">\\1\\2</a>', $string);

  // check for exit/join
  if(isset($string[0]) && $string[0] == '*' && isset($string[1]) && $string[1] == '*') {
    $color = ((strpos($string, 'joined') === false) ? 'a' : 'b');
    $string = "<span class=\"$color\">" . $string . '</span>';
  }

  // check for action
  else if (isset($string[0]) && $string[0] == '*') {
    $string = '<span class="c">' . $string . '</span>';
  } else {
    // check for email - in normal content
    $string = preg_replace('/([^@\s]++)@([^\s.]+)\.(\S+)/i', '$1 at $2 dot $3', $string);
    $string = '<span class="d">' . $string . '</span>';
  }

  return ' ' . $string . "\n";
}

function redirect_date($date, $channel) {

  $date = getdate($date);
  $date = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);

  // fix path forward
  $path = $_SERVER["REQUEST_URI"];
  if($path[strlen($path)-1] != '/') {
    $path .= '/';
  }
  header("channel-" . $channel . "-".$date);
  header("HTTP/1.1 303 See Other");
  header("Location: /logs/browse/$channel/$date");
  die();
}

function render_header($channel, $month, $day, $year) {
	echo "<html>\n";
	echo "<head>\n";
	echo "<title>$channel IRC logs [$month $day - $year]</title>\n";
	echo "<style>\n";
	echo "body {\n";
	echo "margin: 	10px;\n";
	echo "padding: 	10px;\n";
	echo "background-color: #ffffff;\n";
	echo "font-family: Verdana, Arial, Helvetica;\n";
	echo "line-height: 110%;\n";
	echo "}\n";
	echo ".log_menu {\n";
	echo "font-size: 80%;\n";
	echo "}\n";
	echo "pre {\n";
	echo "white-space: pre-wrap;\n";
	echo "white-space: -moz-pre-wrap;\n";
	echo "white-space: -pre-wrap;\n";
	echo "white-space: -o-pre-wrap;\n";
	echo "word-wrap: break-word;\n";
	echo "}\n";
	echo "pre div {\n";
	echo "display: inline;\n";
	echo "}\n";
	echo ".a { color: #800000; }\n";
	echo ".b { color: #009200; }\n";
	echo ".c { color: #9c009c; }\n";
	echo ".d { color: #404040; }\n";
	echo "</style>\n";
	echo "<script>\n";
	
	echo "function elForEach(array, callback, scope) {\n";
	echo "  for (var i = 0; i < array.length; i++) {\n";
	echo "    callback.call(scope, i, array[i]);\n";
	echo "  }\n";
	echo "}\n";
	echo "\n";
	echo "function toggle() {\n";
	echo "  var spans = document.querySelectorAll('span.a, span.b');\n";
	echo "  if(spans.length > 0) {\n";
	echo "    var spanStyle = (spans[0].parentElement.style.display === 'none') ? 'inline' : 'none';\n";
	echo "    elForEach(spans, function (i, v) {v.parentElement.style.display = spanStyle;});\n";
	echo "  }\n";
	echo "}\n";
	
	echo "</script>\n";
	echo "</head>\n";
	echo "<body>\n";	
}
function render_search($enabled, $channel) {
	if(isset($_COOKIE['ddg'])) {
		render_search_duckduckgo($enabled, $channel);
	} else {
		render_search_google($enabled, $channel);
	}
}

function render_search_duckduckgo($enabled, $channel) {
	echo <<<DUCKDUCKGO
	<div align="center" style="padding-bottom: 10px;">
	<iframe src="//duckduckgo.com/search.html?site=echelog.com&prefill=Search DuckDuckGo" style="overflow:hidden;margin:0;padding:0;width:408px;height:40px;" frameborder="0"></iframe>
	<br/><a href="/gcs" class="log_menu">Switch to Google Custom Search</a>
	</div>
DUCKDUCKGO;
}

function render_search_google($enabled, $channel) {
  if($enabled) {
    $google_channel_id = '3nob9c-4ru4';
    switch($channel) {
  	case 'lwjgl':			$google_channel_id = '8assjz-xscm'; break;
  	case 'jme':				$google_channel_id = 'hmmkjt-ynaz'; break;
  	case 'haiku':			$google_channel_id = '72vq15-mmoj'; break;
  	case 'netbeans':		$google_channel_id = 'pxhdk5-g1q6'; break;
  	case 'opengl':			$google_channel_id = 'q7fw2f-956j'; break;
  	case 'openal':			$google_channel_id = 'yp6vud-lsqw'; break;
  	case 'postfix':			$google_channel_id = 'tvvssh-ctvj'; break;
  	case 'eclipse':			$google_channel_id = 'k98urb-6rps'; break;
  	case 'opensolaris':		$google_channel_id = '4iw9eu-lt3k'; break;
  }
  echo <<<GOOGLE_SEARCH
  <div align="center" style="padding-bottom: 10px;">
  <form action="//www.google.com/cse" id="cse-search-box">
  <div>
  <input type="hidden" name="cx" value="partner-pub-6854624150474051:$google_channel_id" />
  <input type="hidden" name="ie" value="ISO-8859-1" />
  <input type="text" name="q" size="31" />
  <input type="submit" name="sa" value="Search" />
  </div>
  </form>
  <script type="text/javascript" src="//www.google.com/coop/cse/brand?form=cse-search-box&amp;lang=en"></script>
  <a href="/ddg" class="log_menu">Switch to DuckDuckGo Search</a>
  </div>
GOOGLE_SEARCH;
  } else {
  echo <<<GOOGLE_SEARCH
  <!-- SiteSearch Google -->
  <FORM method=GET action="//www.google.com/search">
  <input type=hidden name=ie value=UTF-8>
  <input type=hidden name=oe value=UTF-8>
  <TABLE bgcolor="#FFFFFF" align="center"><tr><td>
  <A HREF="//www.google.com/">
  <IMG SRC="//www.google.com/logos/Logo_40wht.gif" border="0" ALT="Google"></A>
  </td>
  <td>
  <INPUT TYPE=text name=q size=31 maxlength=255 value="">
  <INPUT type=submit name=btnG VALUE="Google Search">
  <font size=-1>
  <input type=hidden name=domains value="echelog.com"><br><input type=radio name=sitesearch value=""> Web <input type=radio name=sitesearch value="echelog.com" checked>echelog.com<br>
  </font>
  </td></tr></TABLE>
  </FORM>
  <!-- SiteSearch Google -->
  <a href="/ddg" class="log_menu">Switch to DuckDuckGo Search</a>
GOOGLE_SEARCH;
  }
}

function render_menu($date, $days, $next, $previous, $month, $day, $year, $network, $channel) {
  echo "<div class='log_menu' style='margin: 0; padding: 0;' align='center'>\n";
  // render menu
  // ===================================================================
  // logs
  // back forth - month
  echo "<a href='$previous'><img src='../../button_backwards.png' border='0'></a>&nbsp;&nbsp;";
  echo " $month $day, $year";
  if($next <= time()) {
    echo "&nbsp;&nbsp;<a href='$next'><img src='../../button_forwards.png' border='0'></a>";
  }
  
  echo "<br/>";
  
  // days
  $firstofmonth = strtotime("-" . ($day-1) . " day", $date);

  $previousDay = strtotime("-1 days", $date);
  echo "<a href='$previousDay'>&lt;</a> | ";

  for($i=1; $i<=$days; $i++) {
    $current = strtotime("+" . ($i-1) . " day", $firstofmonth);
    if($i > 1) {
      echo " | ";
    }
	$logpath = "logs/" . $network . '/' . $channel . "/" . $channel . "." . date("Y-m-d", $current) . ".log";
    if($current <= time() && 
	  file_exists($logpath)) {
      echo "<a href='$current'>$i</a>";
    } else {
      echo "$i";
    } 
  }

  if($next <= time()) {
    $nextDay = strtotime("+1 days", $date);
    echo " | <a href='$nextDay'>&gt;</a>";
  }


  // -------------------------------------------------------------------------------------------------------------------------------------
  echo "</div>\n";
}

function render_top_ad($enabled) {
  if($enabled) {
    echo "<br/><div align=\"center\">";
    echo <<<GOOGLE_ADS
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <!-- echelog-leaderboard -->
    <ins class="adsbygoogle"
    style="display:inline-block;width:728px;height:90px"
    data-ad-client="ca-pub-6854624150474051"
    data-ad-slot="8595655610"></ins>
    <script>
    (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
GOOGLE_ADS;
  echo "</div>";
  }
}

function render_archived($archived, $date) {
  if(!empty($archived)) {
    echo "<br/><div align=\"center\">";
    echo "<p><font color=\"#ff0000\"><b>NOTICE</b>: This channel is no longer actively logged.";
    if(intval($archived) == $date) {
      echo " You have been redirected to the last known logged date";
    }
    echo "</font></p>";
    echo "</div>";
  }
}

function render_bottom_ad($enabled) {
  if($enabled) {
    echo "<br/><div align=\"center\">";
    echo <<<GOOGLE_ADS
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <!-- echelog-leaderboard -->
    <ins class="adsbygoogle"
    style="display:inline-block;width:728px;height:90px"
    data-ad-client="ca-pub-6854624150474051"
    data-ad-slot="8595655610"></ins>
    <script>
    (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
GOOGLE_ADS;
  echo "</div>";
  }
}
?>
